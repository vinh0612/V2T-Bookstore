<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Kéo bộ HTTP Client của Laravel vào để gọi API
use Illuminate\Support\Facades\Log;  // Dùng để ghi log nếu AI bị lỗi mạng

class UserReviewController extends Controller
{
    // Xử lý lưu đánh giá và chấm điểm sách từ phía khách hàng
    public function store(Request $request, $bookId)
    {
        // 0. HỆ THỐNG PHẠT THẺ ĐỎ: Đếm số bình luận vi phạm trong ngày hôm nay
        $violationCountToday = Review::where('user_id', Auth::id())
            ->where('status', 'violated')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->count();

        if ($violationCountToday >= 5) {
            return redirect()->back()->with('error', '⛔ Bạn đã vi phạm tiêu chuẩn cộng đồng quá 5 lần. Tính năng bình luận của bạn đã bị khóa đến hết ngày hôm nay!');
        }
        
        // 1. CHẶN SPAM CẤP ĐỘ 1: Kiểm tra user hiện tại đã từng mua sách chưa
        $hasPurchased = OrderItem::where('book_id', $bookId)
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->exists();

        if (!$hasPurchased) {
            return redirect()->back()->with('error', 'Bạn cần mua sách này trước khi đánh giá!');
        }

        // 2. Kiểm tra dữ liệu đầu vào
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ], [
            'comment.required' => 'Bạn vui lòng viết một vài lời nhận xét nhé!',
            'rating.required' => 'Bạn quên chọn số sao đánh giá rồi.'
        ]);

        $commentText = $request->comment;
        $finalStatus = 'pending'; // Mặc định là chờ Admin duyệt tay

        // 3. TÍCH HỢP BỘ NÃO AI (GOOGLE GEMINI) ĐỂ KIỂM DUYỆT TỰ ĐỘNG
        try {
            $apiKey = env('GEMINI_API_KEY');

            if ($apiKey) {
                // Cấu hình Prompt "Nhốt" AI: Ép nó chỉ được nhả ra chữ VIOLATED hoặc SAFE
                $prompt = "Bạn là hệ thống kiểm duyệt bình luận tự động cho một nhà sách. Hãy phân tích bình luận sau. Nếu bình luận chứa từ ngữ chửi thề, tục tĩu, phân biệt vùng miền, thù ghét, hoặc quảng cáo rác (spam link),cẩn thận cả những từ ngữ teen code dùng để tục tĩu, hãy trả lời duy nhất 1 từ: 'VIOLATED'. Nếu bình luận là nhận xét bình thường về sách (khen hoặc chê một cách lịch sự), hãy trả lời duy nhất 1 từ: 'SAFE'. Bình luận cần kiểm duyệt: \"" . $commentText . "\"";

                // Gửi request API thẳng đến não bộ của Google Gemini
                // Quay lại kênh v1beta và gọi con "Flash-lite" siêu nhẹ (gemini-1.5-flash-8b)
                // Chân ái đây rồi! Gọi đúng tên model có trong danh sách của bro
                $response = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=' . $apiKey, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    // ÉP TẮT TOÀN BỘ MÀNG LỌC BẢO VỆ ĐỂ AI ĐỌC ĐƯỢC CHỬI THỀ
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1, // Để AI không bị ảo giác, trả lời chuẩn 1 từ
                        'maxOutputTokens' => 10
                    ]
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    Log::info('Gemini API Response:', ['data' => $responseData]);
                    
                    // Kiểm tra xem có lỗi không
                    if (isset($responseData['error'])) {
                        Log::error('Lỗi từ Gemini API: ' . $responseData['error']['message']);
                    } elseif (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                        // Lóc thịt lấy đúng cái từ khóa AI trả về
                        $aiResult = $responseData['candidates'][0]['content']['parts'][0]['text'];
                        
                        // Chuẩn hóa chuỗi: Chuyển hết về chữ thường, xóa sạch dấu xuống dòng, khoảng trắng, markdown, asterisks
                        $aiResult = strtolower(trim($aiResult));
                        $aiResult = preg_replace('/[`*_\n\r\s]+/', ' ', $aiResult);
                        $aiResult = trim($aiResult);
                        
                        Log::info('AI Result sau khi chuẩn hóa: ' . $aiResult);

                        // Kiểm tra kết quả sau khi đã dọn dẹp sạch sẽ
                        if (strpos($aiResult, 'violated') !== false) {
                            $finalStatus = 'violated'; 
                        } elseif (strpos($aiResult, 'safe') !== false) {
                            $finalStatus = 'approved'; 
                        }
                    }
                } else {
                    Log::error('Gemini API Request Failed', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Nếu AI sập hoặc rớt mạng, cứ kệ nó, ghi lỗi vào log và cho status thành 'pending' để Admin duyệt tay
            Log::error('Lỗi khi gọi AI kiểm duyệt: ' . $e->getMessage(), ['exception' => $e]);
        }

        // 4. Lưu vào Database với cái status vừa được AI ban cho
        Review::create([
            'book_id' => $bookId,
            'user_id' => Auth::id(),
            'rating'  => $request->rating,
            'comment' => $commentText,
            'status'  => $finalStatus, // Gắn mác AI vào đây
            'is_purchased' => true     // Đã qua bước 1 thì chắc chắn true
        ]);

        // 5. Trả về thông báo dựa theo trạng thái
        if ($finalStatus == 'violated') {
            return redirect()->back()->with('error', '⚠️ Bình luận của bạn chứa từ ngữ vi phạm tiêu chuẩn cộng đồng và đã bị hệ thống ẩn!');
        }

        return redirect()->back()->with('success', '🎉 Cảm ơn bạn đã để lại đánh giá cho cuốn sách này!');
    }
}