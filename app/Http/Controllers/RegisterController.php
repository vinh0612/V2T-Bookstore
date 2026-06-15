<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//API của Google để gửi mail
use Google\Client as GoogleClient;
use Google\Service\Gmail as GoogleGmail;
use Google\Service\Gmail\Message as GmailMessage;

class RegisterController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegister() {
        return view('auth.register');
    }

    // BƯỚC 1: Xử lý AJAX - Validate, Tạo OTP, Gửi Mail và Lưu Session
    public function sendOtp(Request $request) {
        // Validate bằng thư viện Validator để dễ dàng trả về JSON
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email này đã tồn tại trong hệ thống rồi bro.',
            'password.min' => 'Mật khẩu phải từ 8 ký tự trở lên cho an toàn.',
            'password.confirmed' => 'Hai ô mật khẩu chưa khớp nhau.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Tạo mã OTP 6 số ngẫu nhiên
        $otp = rand(100000, 999999);

        // Lưu thông tin vào Session (cho phép tồn tại trong 15 phút)
        Session::put('register_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Mã hóa pass luôn từ bước này
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15)
        ]);

        //Gửi mail OTP bằng Google API 
        try {
            $client = new GoogleClient();
            $client->setClientId(env('GMAIL_CLIENT_ID'));
            $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
            $client->refreshToken(env('GMAIL_REFRESH_TOKEN'));
            
            $gmailService = new GoogleGmail($client);

            $toEmail = $request->email;
            $subject = "Mã xác nhận tài khoản V2T Bookstore";
            
            // Format mail chuẩn HTML không bị vỡ font
            $mailContent = "To: $toEmail\r\n";
            $mailContent .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
            $mailContent .= "MIME-Version: 1.0\r\n";
            $mailContent .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
            $mailContent .= '
                <div style="font-family: Arial, sans-serif; padding: 20px; text-align: center; background-color: #f9fafb; border-radius: 8px;">
                    <h2 style="color: #1e3e36; margin-bottom: 5px;">V2T BOOKSTORE</h2>
                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 2px;">Xác thực tài khoản</p>
                    <div style="background-color: white; padding: 30px; border-radius: 8px; margin-top: 20px; border: 1px solid #e5e7eb; display: inline-block;">
                        <p style="color: #374151; font-size: 16px;">Chào <strong>' . $request->name . '</strong>, Mã OTP của bạn là:</p>
                        <p style="font-size: 36px; font-weight: bold; color: #1e3e36; letter-spacing: 5px; margin: 15px 0;">' . $otp . '</p>
                        <p style="color: #9ca3af; font-size: 12px;">Mã này sẽ hết hạn sau 15 phút. Tuyệt đối không chia sẻ cho người khác.</p>
                    </div>
                </div>
            ';

            // Base64URL encode theo đúng luật của Google
            $mimeMessage = rtrim(strtr(base64_encode($mailContent), '+/', '-_'), '=');
            
            $message = new GmailMessage();
            $message->setRaw($mimeMessage);

            // Bấm nút phóng thư
            $gmailService->users_messages->send('me', $message);

        } catch (\Exception $e) {
            // Lỡ Token bị hết hạn hay rớt mạng, web vẫn không sập, vẫn ghi Log để bro soi lại
            Log::error("Lỗi gửi mail OTP Google API: " . $e->getMessage());
            Log::info("🔴 [DEV-MODE] Mã OTP dành cho {$request->email} là: {$otp}");
            
            // Xóa session vì mail lỗi không gửi được, bắt người dùng thử lại
            Session::forget('register_data');
            
            return response()->json([
                'success' => false,
                'errors' => ['Lỗi hệ thống gửi mail. Vui lòng thử lại sau!']
            ]);
        }

        // Báo cho giao diện biết là gửi thành công
        return response()->json(['success' => true]);
    }

    // BƯỚC 2: Kiểm tra OTP và Tạo tài khoản thực sự
    public function verifyOtp(Request $request) {
        // Validate bằng Validator để ép trả về JSON nếu có lỗi
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|numeric'
        ], [
            'otp_code.required' => 'Bro chưa nhập mã OTP kìa.',
            'otp_code.numeric' => 'Mã OTP chỉ bao gồm các chữ số.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Lấy lại cái gói thông tin mình cất trong Session
        $registerData = Session::get('register_data');

        // Kiểm tra xem có dữ liệu không, hoặc quá 15 phút chưa
        if (!$registerData || now()->greaterThan($registerData['expires_at'])) {
            Session::forget('register_data');
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng ký đã hết hạn hoặc không tồn tại. Vui lòng tải lại trang và làm lại từ đầu.'
            ]);
        }

        // Kiểm tra mã OTP nhập vào có khớp không
        if ($request->otp_code != $registerData['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'Mã OTP không chính xác. Bro kiểm tra lại email nhé!'
            ]);
        }

        // Thành công -> Đưa vào Database
        $user = User::create([
            'name' => $registerData['name'],
            'email' => $registerData['email'],
            'password' => $registerData['password'],
            'role' => 'user', 
        ]);

        // Dọn dẹp rác trong Session
        Session::forget('register_data');

        // Auto đăng nhập
        Auth::login($user);

        // Ném thêm cái session flash message để lúc redirect về trang chủ nó hiện thông báo Toast
        Session::flash('success', '🎉 Chúc mừng bro đã gia nhập V2T Bookstore!');

        // Báo cho JS biết là thành công và quăng kèm cái link để nó tự chuyển hướng
        return response()->json([
            'success' => true,
            'redirect_url' => url('/')
        ]);
    }
}