@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<style>
    .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .v2t-table { width: 100%; border-collapse: collapse; text-align: left; background: white; border-radius: 12px; }
    .v2t-table th { padding: 16px; color: #6b7280; font-size: 0.85rem; border-bottom: 1px solid #f3f4f6; }
    .v2t-table td { padding: 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    
    .status-badge { 
        padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; 
    }
    .status-select { padding: 6px 10px; border-radius: 6px; border: 1px solid #e5e7eb; background: #f9fafb; cursor: pointer; font-weight: 600; font-size: 0.85rem; color: #374151;}
    .btn-detail { padding: 6px 12px; background: #f3f4f6; color: #374151; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; border: 1px solid #e5e7eb; transition: background 0.2s;}
    .btn-detail:hover { background: #e5e7eb; }
</style>

<div style="width: 100%;">
    
    {{-- ĐÃ FIX LỖI SỐ 4: Thêm khối thông báo khi cập nhật thành công --}}
    @if(session('success'))
        <div style="background-color: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            {{ session('success') }}
        </div>
    @endif

    <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 24px;">Đơn hàng</h1>

    {{-- ĐÃ FIX LỖI SỐ 1: Đổi layout thành 5 cột để hiển thị đủ 5 trạng thái --}}
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px;">
        <div class="stat-card">
            <p style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 5px;">TỔNG ĐƠN HÀNG</p>
            <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0;">{{ $totalOrders }}</h3>
        </div>
        <div class="stat-card">
            <p style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 5px;">CHỜ DUYỆT</p>
            <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #d97706;">{{ $pendingOrders }}</h3>
        </div>
        <div class="stat-card">
            <p style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 5px;">ĐANG GIAO</p>
            <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #0284c7;">{{ $processingOrders }}</h3>
        </div>
        <div class="stat-card">
            <p style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 5px;">THÀNH CÔNG</p>
            <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #059669;">{{ $completedOrders }}</h3>
        </div>
        <div class="stat-card">
            <p style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 5px;">ĐÃ HỦY</p>
            <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #dc2626;">{{ $cancelledOrders }}</h3>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb;">
        <table class="v2t-table">
            <thead>
                <tr>
                    <th>MÃ ĐƠN HÀNG</th>
                    <th>KHÁCH HÀNG</th>
                    <th>NGÀY ĐẶT</th>
                    <th>TỔNG CỘNG</th>
                    <th>TRẠNG THÁI</th>
                    <th style="text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td style="font-weight: 600;">#INK-{{ $order->id }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $order->user->name ?? 'Khách vãng lai' }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">{{ $order->user->email ?? '' }}</div>
                    </td>
                    <td>{{ $order->created_at->format('H:i - d/m/Y') }}</td>
                    <td style="font-weight: 600; color: #0a3622;">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                    <td>
                        {{-- ĐÃ FIX LỖI SỐ 3: Dịch ra Tiếng Việt và tô màu chuẩn UI/UX --}}
                        @if($order->status == 'pending')
                            <span class="status-badge" style="background-color: #fef3c7; color: #d97706;">Chờ duyệt</span>
                        @elseif($order->status == 'processing')
                            <span class="status-badge" style="background-color: #e0f2fe; color: #0284c7;">Đang giao</span>
                        @elseif($order->status == 'completed')
                            <span class="status-badge" style="background-color: #d1fae5; color: #059669;">Thành công</span>
                        @else
                            <span class="status-badge" style="background-color: #fee2e2; color: #dc2626;">Đã hủy</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                            {{-- Nút Chi Tiết để Admin soi sách --}}
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-detail">Chi tiết</a>

                            {{-- HIỂN THỊ ĐỘNG THEO TRẠNG THÁI --}}
                            @if(in_array($order->status, ['completed', 'cancelled']))
                                {{-- Nếu đã xong -> Khóa cái ổ khóa lại --}}
                                <span style="color: #9ca3af; font-size: 0.8rem; font-weight: 700; background: #f3f4f6; padding: 6px 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
                                    🔒 Đã khóa
                                </span>
                            @else
                                {{-- Nếu đang chạy -> Chỉ hiện các Option hợp lệ --}}
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        @if($order->status == 'pending')
                                            <option value="pending" selected>Chờ duyệt</option>
                                            <option value="processing">Giao hàng</option>
                                            <option value="cancelled">Hủy đơn</option>
                                        @elseif($order->status == 'processing')
                                            <option value="processing" selected>Đang giao</option>
                                            <option value="completed">Xác nhận Thành công</option>
                                            <option value="cancelled">Hủy đơn </option>
                                        @endif
                                    </select>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- Phân trang --}}
        <div style="margin-top: 24px;">{{ $orders->links() }}</div>
        
    </div>
</div>
@endsection