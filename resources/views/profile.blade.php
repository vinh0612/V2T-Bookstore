@extends('layouts.app')

@section('title', 'Tài khoản của tôi - V2T Bookstore')

@section('content')
<style>
    .v2t-profile-container {
        background-color: #f4f1ea;
        min-height: 100vh;
        margin-left: -1rem;
        margin-right: -1rem;
        padding: 2.5rem 1.5rem;
    }
    .v2t-profile-layout {
        display: flex;
        flex-direction: column;
        gap: 32px;
        width: 100%;
    }
    .v2t-profile-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .v2t-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }
    .v2t-input-field {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #1f2937;
        background-color: #f9fafb;
        outline: none;
        transition: all 0.2s;
    }
    .v2t-input-field:focus {
        border-color: #1e3e36;
        background-color: #ffffff;
    }
    .v2t-btn-save {
        background-color: #1e3e36;
        color: white;
        font-size: 0.875rem;
        font-weight: 700;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .v2t-btn-save:hover {
        opacity: 0.95;
    }
    .v2t-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.875rem;
    }
    .v2t-table th {
        background-color: #f3f4f6;
        color: #374151;
        font-weight: 700;
        padding: 12px;
        border-bottom: 2px solid #e5e7eb;
    }
    .v2t-table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        color: #4b5563;
    }
    
    /* Responsive cho màn hình máy tính lớn */
    @media (min-width: 992px) {
        .v2t-profile-layout {
            display: grid;
            grid-template-columns: 4fr 7fr;
            gap: 32px;
            align-items: start;
        }
    }
</style>

<div class="v2t-profile-container">
    <div class="container mx-auto max-w-7xl">
        
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-gray-900">Quản lý tài khoản</h1>
            <p class="text-xs text-gray-500 mt-1">Xin chào bạn, đây là nơi cập nhật thông tin và theo dõi đơn hàng của bạn.</p>
        </div>

        <div class="v2t-profile-layout">
            
            <div class="space-y-6">
                
                <div class="v2t-profile-card">
                    <h3 class="font-serif font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4" style="font-size: 1.15rem;">Thông tin cá nhân</h3>
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="v2t-input-group">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Họ và tên</rb>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="v2t-input-field" required>
                        </div>

                        <div class="v2t-input-group">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Địa chỉ Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="v2t-input-field" required>
                        </div>

                        <button type="submit" class="v2t-btn-save mt-2">Lưu thay đổi</button>
                    </form>
                </div>

                <div class="v2t-profile-card">
                    <h3 class="font-serif font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4" style="font-size: 1.15rem;">Đổi mật khẩu bảo mật</h3>
                    
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="v2t-input-group">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="v2t-input-field" required>
                        </div>

                        <div class="v2t-input-group">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="v2t-input-field" placeholder="Tối thiểu 8 ký tự" required>
                        </div>

                        <div class="v2t-input-group">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Nhập lại mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" class="v2t-input-field" required>
                        </div>

                        <button type="submit" class="v2t-btn-save mt-2">Cập nhật mật khẩu</button>
                    </form>
                </div>

            </div>

            <div class="v2t-profile-card">
                <h3 class="font-serif font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4" style="font-size: 1.15rem;">Lịch sử đặt hàng</h3>
                
                @if($orders->count() > 0)
                    <div style="overflow-x: auto; width: 100%;">
                        <table class="v2t-table">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Địa chỉ giao hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="font-bold text-gray-900">#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="max-width: 220px;" class="truncate" title="{{ $order->shipping_address }}">{{ $order->shipping_address }}</td>
                                        <td class="font-bold text-gray-800">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded bg-amber-100 text-amber-800">Chờ duyệt</span>
                                            @elseif($order->status == 'processing')
                                                <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded bg-blue-100 text-blue-800">Đang đóng gói</span>
                                            @elseif($order->status == 'completed')
                                                <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded bg-green-100 text-green-800">Thành công</span>
                                            @else
                                                <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded bg-red-100 text-red-800">Đã hủy đơn</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                        <span style="font-size: 2.5rem;">📦</span>
                        <p class="text-sm mt-3 italic text-gray-400">Bạn chưa đặt mua đơn hàng nào tại hệ thống của chúng tôi.</p>
                        <a href="/shop" class="inline-block mt-4 text-xs font-bold text-white px-4 py-2 rounded-lg" style="background-color: #1e3e36; text-decoration: none;">Đến cửa hàng sắm sách ngay</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection