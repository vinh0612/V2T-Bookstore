<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu đã đăng nhập MÀ KHÔNG PHẢI admin -> Cho về trang chủ khách hàng
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect('/home')->with('error', '⛔ Bro đi lạc rồi, đây là khu vực của Quản trị viên!');
        }

        return $next($request);
    }
}