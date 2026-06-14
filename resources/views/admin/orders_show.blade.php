@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<style>
    .detail-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }

    .info-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 1rem;
        color: #111827;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .v2t-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v2t-table th {
        padding: 12px 16px;
        color: #6b7280;
        font-size: 0.85rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .v2t-table td {
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
</style>

<div style="width: 100%; max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('admin.orders.index') }}" style="color: #6b7280; text-decoration: none; font-weight: 600;">&larr; Quay lại</a>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">Đơn hàng #INK-{{ $order->id }}</h1>

            @if($order->status == 'pending')
            <span style="background-color: #fef3c7; color: #d97706; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Chờ duyệt</span>
            @elseif($order->status == 'processing')
            <span style="background-color: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Đang giao</span>
            @elseif($order->status == 'completed')
            <span style="background-color: #d1fae5; color: #059669; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Thành công</span>
            @else
            <span style="background-color: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Đã hủy</span>
            @endif
        </div>

        <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
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
                    <option value="processing">Giao hàng (Processing)</option>
                    <option value="cancelled">Hủy đơn</option>
                    @elseif($order->status == 'processing')
                    <option value="processing" selected>Đang giao</option>
                    <option value="completed">Xác nhận Thành công</option>
                    <option value="cancelled">Hủy đơn (Khách bom)</option>
                    @endif
                </select>
            </form>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        {{-- Box thông tin khách hàng --}}
        <div class="detail-card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px;">Thông tin Người đặt</h3>
            <p class="info-label">Khách hàng</p>
            <p class="info-value">{{ $order->user->name ?? 'Khách vãng lai' }}</p>

            <p class="info-label">Email tài khoản</p>
            <p class="info-value">{{ $order->user->email ?? 'Không có' }}</p>

            <p class="info-label">Ngày đặt hàng</p>
            <p class="info-value" style="margin-bottom: 0;">{{ $order->created_at->format('H:i:s - d/m/Y') }}</p>
        </div>

        {{-- Box thông tin giao hàng --}}
        <div class="detail-card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px;">Thông tin Giao hàng</h3>
            <p class="info-label">Số điện thoại người nhận</p>
            <p class="info-value">{{ $order->phone ?? 'Chưa cập nhật' }}</p>

            <p class="info-label">Địa chỉ nhận hàng</p>
            <p class="info-value">{{ $order->shipping_address ?? 'Chưa cập nhật' }}</p>

            <p class="info-label">Phương thức thanh toán</p>
            <p class="info-value" style="margin-bottom: 0;">
                @if($order->payment_method == 'momo')
                <span style="color: #a50064; font-weight: 700;">Ví MoMo</span>
                @elseif($order->payment_method == 'credit')
                <span style="color: #0284c7; font-weight: 700;">Thẻ ngân hàng</span>
                @else
                Thanh toán khi nhận hàng (COD)
                @endif
            </p>
        </div>
    </div>

    {{-- Box Sách cần đóng gói --}}
    <div class="detail-card" style="padding: 0; overflow: hidden;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 24px; margin-bottom: 16px;">Sách cần đóng gói ({{ $order->items->count() }})</h3>
        <table class="v2t-table">
            <thead style="background: #f9fafb;">
                <tr>
                    <th style="padding-left: 24px;">SÁCH</th>
                    <th style="text-align: center;">ĐƠN GIÁ</th>
                    <th style="text-align: center;">SỐ LƯỢNG</th>
                    <th style="text-align: right; padding-right: 24px;">THÀNH TIỀN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding-left: 24px;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <img src="{{ $item->book->image_url ?? 'https://placehold.co/100x150/10b981/ffffff?text=V2T' }}" style="width: 48px; height: 72px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;">
                            <div>
                                <div style="font-weight: 700; color: #111827; margin-bottom: 4px;">{{ $item->book->title ?? 'Sách đã xóa' }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">{{ $item->book->author ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center; color: #4b5563;">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                    <td style="text-align: center; font-weight: 700;">x{{ $item->quantity }}</td>
                    <td style="text-align: right; font-weight: 700; color: #0a3622; padding-right: 24px;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding: 24px; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end;">
            <div style="width: 300px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #6b7280;">
                    <span>Phí vận chuyển</span>
                    <span>30.000đ</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; color: #111827; border-top: 2px solid #e5e7eb; padding-top: 12px;">
                    <span>TỔNG CỘNG</span>
                    <span style="color: #059669;">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection