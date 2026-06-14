<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCustomer
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu là Admin mà đòi vào trang cá nhân của Khách -> Cho thẳng về Dashboard Admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}