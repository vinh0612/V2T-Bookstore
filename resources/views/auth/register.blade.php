@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - V2T Bookstore')

@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="bg-white p-10 rounded-lg shadow-sm w-full max-w-md border border-gray-100 my-10 relative overflow-hidden">
        
        {{-- Loader (Sẽ hiện lúc đang chờ server gửi mail) --}}
        <div id="loading-overlay" class="hidden absolute inset-0 bg-white bg-opacity-80 z-10 flex flex-col items-center justify-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[var(--color-v2t-green)] mb-3"></div>
            <p class="text-xs font-semibold text-[var(--color-v2t-green)]">Đang gửi mã xác nhận...</p>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold font-serif text-[var(--color-v2t-text)] mb-2">V2T Bookstore</h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Thiên Đường Sách Hiện Đại</p>
        </div>

        <div class="flex justify-center border-b border-gray-200 mb-6">
            <a href="/login" class="pb-2 px-4 text-gray-400 hover:text-[var(--color-v2t-text)] font-medium text-sm transition">Đăng nhập</a>
            <a href="/register" class="pb-2 px-4 border-b-2 border-[var(--color-v2t-green)] font-medium text-[var(--color-v2t-green)] text-sm">Đăng ký</a>
        </div>

        {{-- Báo lỗi chung --}}
        <div id="error-alert" class="hidden bg-[#fee2e2] text-[#991b1b] p-3 rounded mb-4 text-xs font-medium"></div>

        <form id="register-form" action="{{ route('register.send_otp') }}" method="POST">
            @csrf
            
            {{-- BƯỚC 1: NHẬP THÔNG TIN CƠ BẢN --}}
            <div id="step-1">
                <h2 class="text-xl font-serif font-bold mb-1">Tạo tài khoản mới</h2>
                <p class="text-xs text-gray-500 mb-6">Tham gia cộng đồng yêu sách của chúng tôi ngay hôm nay.</p>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:border-[var(--color-v2t-green)] focus:ring-1 focus:ring-[var(--color-v2t-green)] text-sm transition">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Địa chỉ Email</label>
                    <input type="email" id="email" name="email" placeholder="nguyenvana@email.com" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:border-[var(--color-v2t-green)] focus:ring-1 focus:ring-[var(--color-v2t-green)] text-sm transition">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:border-[var(--color-v2t-green)] focus:ring-1 focus:ring-[var(--color-v2t-green)] text-sm transition">
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Xác nhận mật khẩu</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:border-[var(--color-v2t-green)] focus:ring-1 focus:ring-[var(--color-v2t-green)] text-sm transition">
                </div>

                <button type="button" onclick="sendOtpRequest()" class="w-full bg-[var(--color-v2t-green)] text-white font-medium py-2.5 rounded hover:bg-[var(--color-v2t-green-hover)] transition duration-200 text-sm">
                    Tiếp tục
                </button>
            </div>

            {{-- BƯỚC 2: NHẬP OTP XÁC NHẬN --}}
            <div id="step-2" class="hidden">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#ecfdf5] text-[var(--color-v2t-green)] mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-lg font-serif font-bold mb-1">Xác thực Email</h2>
                    <p class="text-xs text-gray-500">Mã gồm 6 chữ số đã được gửi tới <br><span id="display-email" class="font-bold text-gray-800"></span></p>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-2 text-center">Nhập mã xác thực</label>
                    <input type="text" id="otp_code" name="otp_code" placeholder="------" maxlength="6" class="w-full text-center tracking-[0.5em] text-lg font-bold px-3 py-3 border border-gray-300 rounded focus:outline-none focus:border-[var(--color-v2t-green)] focus:ring-1 focus:ring-[var(--color-v2t-green)] transition">
                </div>

                <button type="submit" class="w-full bg-[var(--color-v2t-green)] text-white font-medium py-2.5 rounded hover:bg-[var(--color-v2t-green-hover)] transition duration-200 text-sm mb-4">
                    Hoàn tất đăng ký
                </button>

                <div class="text-center text-xs">
                    <span class="text-gray-500">Chưa nhận được mã?</span>
                    <button type="button" onclick="sendOtpRequest()" id="resend-btn" class="ml-1 text-[var(--color-v2t-green)] font-semibold hover:underline">Gửi lại</button>
                    <span id="countdown-timer" class="hidden text-gray-500 ml-1">(Chờ <span id="time-left">60</span>s)</span>
                </div>
            </div>

            <p class="text-center text-[10px] text-gray-400 mt-6">
                Bằng cách đăng ký, bạn đồng ý với các Điều khoản & Chính sách của V2T Bookstore.
            </p>
        </form>
    </div>
</div>

{{-- SCRIPT ĐIỀU HƯỚNG BƯỚC 1 -> BƯỚC 2 BẰNG AJAX --}}
<script>
    function sendOtpRequest() {
        const form = document.getElementById('register-form');
        const formData = new FormData(form);
        const errorAlert = document.getElementById('error-alert');
        const overlay = document.getElementById('loading-overlay');
        const emailInput = document.getElementById('email').value;

        // Xóa lỗi cũ, bật loader
        errorAlert.classList.add('hidden');
        overlay.classList.remove('hidden');

        // Gửi AJAX tới Controller
        fetch("{{ route('register.send_otp') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            overlay.classList.add('hidden');
            
            if(data.success) {
                // Đổi form action để bước 2 gọi qua hàm verify OTP
                form.action = "{{ route('register.verify_otp') }}";
                
                // Chuyển giao diện
                document.getElementById('step-1').classList.add('hidden');
                document.getElementById('step-2').classList.remove('hidden');
                document.getElementById('display-email').innerText = emailInput;
                
                startResendCountdown();
            } else {
                // In lỗi Validate ra màn hình
                errorAlert.innerHTML = Object.values(data.errors).map(err => `• ${err}`).join('<br>');
                errorAlert.classList.remove('hidden');
            }
        })
        .catch(error => {
            overlay.classList.add('hidden');
            errorAlert.innerHTML = 'Lỗi hệ thống, không thể gửi mã. Vui lòng thử lại sau.';
            errorAlert.classList.remove('hidden');
        });
    }

    function startResendCountdown() {
        const resendBtn = document.getElementById('resend-btn');
        const countdownTimer = document.getElementById('countdown-timer');
        const timeLeftSpan = document.getElementById('time-left');
        
        let timeLeft = 60;
        resendBtn.classList.add('hidden');
        countdownTimer.classList.remove('hidden');
        
        const timer = setInterval(() => {
            timeLeft--;
            timeLeftSpan.innerText = timeLeft;
            if(timeLeft <= 0) {
                clearInterval(timer);
                resendBtn.classList.remove('hidden');
                countdownTimer.classList.add('hidden');
            }
        }, 1000);
    }
</script>
@endsection