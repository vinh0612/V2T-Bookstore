<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegister() {
        return view('auth.register');
    }

    // Xử lý đăng ký tài khoản mới
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Tạo người dùng mới
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Đồng bộ về 'user' cho khớp hoàn toàn với Seeder nha bro
        ]);

        // Mẹo trải nghiệm: Tự động đăng nhập luôn cho khách sau khi đăng ký thành công
        Auth::login($user);

        return redirect('/home')->with('success', 'Chào mừng bro đến với V2T Bookstore!');
    }
}