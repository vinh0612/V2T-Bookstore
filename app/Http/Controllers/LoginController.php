<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLogin() {
        return view('auth.login');
    }

    // Xử lý đăng nhập hệ thống
    public function login(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Kiểm tra trạng thái hoạt động của tài khoản
        $user = User::where('email', $request->email)->first();

        if ($user && $user->status === 'blocked') {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn hiện đang bị khóa. Vui lòng liên hệ Admin để được hỗ trợ!',
            ])->withInput();
        }

        // 3. Tiến hành kiểm tra đăng nhập
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            // Phân luồng điều hướng dựa trên Role
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect('/home');
        }

        // 4. Trả về lỗi nếu sai thông tin
        return back()->withErrors([
            'email' => 'Thông tin tài khoản hoặc mật khẩu không chính xác.',
        ])->withInput();
    }
}