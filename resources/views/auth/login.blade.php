@extends('layouts.app')

@section('title', 'Đăng nhập - V2T Bookstore')

@section('content')
{{-- ĐÃ FIX: Thêm px-4 ở ngoài cùng để form cách lề điện thoại 2 bên, thêm py-8 để cách trên dưới --}}
<div class="flex items-center justify-center min-h-[70vh] px-4 md:px-0 py-8 text-[var(--color-v2t-text)]">
    
    {{-- ĐÃ FIX: Giảm padding trên mobile (p-6), lên PC tự giãn ra (md:p-10). Bo góc tròn hơn (rounded-xl) --}}
    <div class="bg-white p-6 md:p-10 rounded-xl shadow-sm w-full max-w-md border border-gray-100">
        <div class="text-center mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold font-serif mb-1 md:mb-2 text-v2t-green">V2T Bookstore</h1>
            <p class="text-[9px] md:text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Thiên Đường Sách Hiện Đại</p>
        </div>

        <div class="flex justify-center border-b border-gray-200 mb-6">
            <a href="/login" class="pb-2 px-4 border-b-2 border-v2t-green font-medium text-v2t-green text-sm">Đăng nhập</a>
            <a href="/register" class="pb-2 px-4 text-gray-400 hover:text-v2t-green font-medium text-sm transition">Đăng ký</a>
        </div>
        
        @if ($errors->any())
            <div style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 0.875rem; margin-bottom: 16px; font-weight: 500;">
                {{ $errors->first() }}
            </div>
        @endif

        <h2 class="text-lg md:text-xl font-serif font-bold mb-1">Chào mừng trở lại</h2>
        <p class="text-xs text-gray-500 mb-6">Vui lòng nhập thông tin tài khoản để tiếp tục.</p>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Địa chỉ Email</label>
                {{-- ĐÃ FIX: Tăng chiều cao thẻ input (py-3) để tay dễ bấm trên màn hình cảm ứng --}}
                <input type="email" name="email" placeholder="nguyenvana@email.com" class="w-full px-4 py-3 md:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-v2t-green focus:ring-2 focus:ring-v2t-green text-sm transition" required>
            </div>

            <div class="mb-4">
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-xs font-semibold text-gray-700">Mật khẩu</label>
                    <a href="#" class="text-xs text-gray-500 hover:text-v2t-green hover:underline">Quên mật khẩu?</a>
                </div>
                <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 md:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-v2t-green focus:ring-2 focus:ring-v2t-green text-sm transition" required>
            </div>

            <div style="margin-top: 16px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="remember" id="remember" style="cursor: pointer; width: 16px; height: 16px; accent-color: #1e3e36;">
                {{-- ĐÃ FIX: Chữ App\for đổi thành for chuẩn HTML --}}
                <label for="remember" style="font-size: 0.875rem; color: #4b5563; cursor: pointer;">Lưu tài khoản</label>
            </div>

            <div class="mt-6 md:mt-8">
                {{-- ĐÃ FIX: Nút to hơn (py-3) và có hiệu ứng bóng đổ (shadow-md) --}}
                <button type="submit" class="w-full bg-[var(--color-v2t-green)] text-white font-medium py-3 rounded-lg hover:bg-opacity-90 transition duration-200 text-sm shadow-md cursor-pointer">
                    Đăng nhập
                </button>
            </div>
        </form>
    </div>
</div>
@endsection