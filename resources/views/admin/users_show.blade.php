@extends('layouts.admin')

@section('title', 'Hồ sơ người dùng')

@section('content')
<style>
    .profile-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .stat-box { background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 16px; text-align: center; }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #111827; }
    
    .info-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
    .info-label { font-size: 0.85rem; color: #6b7280; font-weight: 600; width: 30%; }
    .info-value { font-size: 0.95rem; color: #111827; font-weight: 500; width: 70%; }
</style>

<div style="width: 100%; max-width: 900px; margin: 0 auto;">
    
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('admin.users.index') }}" style="color: #6b7280; text-decoration: none; font-weight: 600; font-size: 0.9rem;">&larr; Quay lại danh sách</a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        
        {{-- Box Trái: Avatar và Trạng thái --}}
        <div>
            <div class="profile-card" style="text-align: center;">
                <div style="width: 96px; height: 96px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 2.5rem; color: #1e3e36; margin: 0 auto 16px auto;">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
                <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 4px 0; color: #111827;">{{ $user->name }}</h2>
                <p style="font-size: 0.85rem; color: #6b7280; margin: 0 0 16px 0;">{{ $user->email }}</p>
                
                <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 24px;">
                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; background: {{ $user->role === 'admin' ? '#fef3c7' : '#f3f4f6' }}; color: {{ $user->role === 'admin' ? '#92400e' : '#4b5563' }}; text-transform: uppercase;">
                        {{ $user->role === 'admin' ? 'Admin' : 'Customer' }}
                    </span>
                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; background: {{ $user->status === 'active' ? '#d1fae5' : '#fee2e2' }}; color: {{ $user->status === 'active' ? '#065f46' : '#dc2626' }}; text-transform: uppercase;">
                        {{ $user->status === 'active' ? 'Hoạt động' : 'Đã khóa' }}
                    </span>
                </div>

                {{-- Nút Khóa/Mở khóa --}}
                <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST">
                    @csrf
                    @if($user->status === 'active')
                        <button type="submit" style="width: 100%; padding: 10px; background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; border-radius: 8px; font-weight: 700; cursor: pointer;">🔒 Khóa tài khoản này</button>
                    @else
                        <button type="submit" style="width: 100%; padding: 10px; background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; border-radius: 8px; font-weight: 700; cursor: pointer;">🔓 Mở khóa tài khoản</button>
                    @endif
                </form>
            </div>
        </div>

        {{-- Box Phải: Thông tin chi tiết và Thống kê mua hàng --}}
        <div>
            <div class="profile-card">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 16px 0; color: #111827;">Thống kê mua sắm</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="stat-box">
                        <div class="stat-label">Tổng số đơn hàng</div>
                        <div class="stat-value">{{ number_format($orderCount) }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Tổng tiền đã chi tiêu</div>
                        <div class="stat-value" style="color: #059669;">{{ number_format($totalSpent, 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 16px 0; color: #111827;">Thông tin hệ thống</h3>
                <div class="info-row">
                    <div class="info-label">Mã định danh (ID)</div>
                    <div class="info-value">#{{ $user->id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ngày tham gia</div>
                    <div class="info-value">{{ $user->created_at->format('d/m/Y - H:i') }}</div>
                </div>
                <div class="info-row" style="border-bottom: none;">
                    <div class="info-label">Cập nhật cuối cùng</div>
                    <div class="info-value">{{ $user->updated_at->format('d/m/Y - H:i') }}</div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection