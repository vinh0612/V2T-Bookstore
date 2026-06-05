<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class UserWishlistController extends Controller
{
    // 1. Hiển thị trang danh sách sản phẩm yêu thích của tôi
    public function index()
    {
        // Lấy danh sách ID sách được lưu trong Session
        $wishlistIds = session()->get('wishlist', []);
        
        // Truy vấn dữ liệu sách kèm điểm đánh giá trung bình
        $books = Book::withAvg('reviews', 'rating')
            ->whereIn('id', $wishlistIds)
            ->get();

        return view('wishlist', compact('books'));
    }

    // 2. Xử lý Bật/Tắt (Thêm/Xóa) sách khỏi danh sách yêu thích
    public function toggle(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $wishlist = session()->get('wishlist', []);

        if (in_array($id, $wishlist)) {
            // Nếu đã có sẵn trong danh sách -> Tiến hành XÓA KHỎI WISHLIST
            $wishlist = array_values(array_diff($wishlist, [$id]));
            session()->put('wishlist', $wishlist);
            $message = '💔 Đã xóa "' . $book->title . '" khỏi danh sách yêu thích!';
            $status = 'removed';
        } else {
            // Nếu chưa có -> Tiến hành THÊM VÀO WISHLIST
            $wishlist[] = (int)$id;
            session()->put('wishlist', $wishlist);
            $message = '❤️ Đã thêm "' . $book->title . '" vào danh sách yêu thích!';
            $status = 'added';
        }

        // ĐÁP ỨNG UI/UX: Nếu giao diện gọi bằng AJAX (Thả tim không load lại trang)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $status,
                'message' => $message,
                'count'   => count($wishlist)
            ]);
        }

        // Dự phòng: Nếu gọi bằng form submit HTML truyền thống
        return redirect()->back()->with('success', $message);
    }
}