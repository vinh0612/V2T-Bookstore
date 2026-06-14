<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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

        // Lưu thông tin vào Session (cho phép sống trong 15 phút)
        Session::put('register_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Mã hóa pass luôn từ bước này
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15)
        ]);

        // Gửi Mail chứa mã OTP (Bọc Try-Catch để lỡ chưa setup Mail thì web không bị sập)
        try {
            Mail::raw("Chào {$request->name},\n\nMã xác thực OTP đăng ký tài khoản tại V2T Bookstore của bạn là: {$otp}.\n\nMã này sẽ hết hạn sau 15 phút. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Mã xác thực đăng ký - V2T Bookstore');
            });
        } catch (\Exception $e) {
            // Mẹo cho Dev: Nếu XAMPP chưa gửi được mail, mở file laravel.log ra coi mã OTP luôn!
            Log::error("Lỗi gửi mail OTP: " . $e->getMessage());
            Log::info("🔴 [DEV-MODE] Mã OTP dành cho {$request->email} là: {$otp}");
        }

        // Báo cho giao diện (JS) biết là gửi thành công
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

        // Mọi thứ qua cửa trót lọt -> Nhét thẳng vào Database
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