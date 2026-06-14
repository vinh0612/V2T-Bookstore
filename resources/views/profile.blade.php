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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

    .v2t-tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 20px;
        overflow-x: auto;
    }

    .v2t-tab {
        padding: 12px 24px;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }

    .v2t-tab:hover {
        color: #1e3e36;
    }

    .v2t-tab.active {
        color: #1e3e36;
        border-bottom-color: #1e3e36;
    }

    /* Chỉnh lại giao diện rỗng */
    #empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
</style>

<div class="v2t-profile-container">
    <div class="container mx-auto max-w-7xl">

        <div class="mb-8"

            @if ($errors->any())
            <div style="background-color: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                <strong>⚠️ Vui lòng kiểm tra lại:</strong>
                <ul style="list-style-type: disc; padding-left: 20px; margin-top: 8px; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            ```

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
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Họ và tên</label>
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

                <div class="v2t-tabs" id="order-tabs" style="display: {{ $orders->count() > 0 ? 'flex' : 'none' }};">
                    <button class="v2t-tab active" onclick="filterOrders('all', this)">Tất cả</button>
                    <button class="v2t-tab" onclick="filterOrders('pending', this)">Chờ duyệt</button>
                    <button class="v2t-tab" onclick="filterOrders('processing', this)">Đang giao</button>
                    <button class="v2t-tab" onclick="filterOrders('completed', this)">Thành công</button>
                    <button class="v2t-tab" onclick="filterOrders('cancelled', this)">Đã hủy</button>
                </div>

                <div id="order-table-container" style="overflow-x: auto; width: 100%; display: {{ $orders->count() > 0 ? 'block' : 'none' }};">
                    <table class="v2t-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr class="order-row" data-status="{{ $order->status }}">
                                <td class="font-bold text-gray-900">#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>

                                <td style="max-width: 250px;">
                                    @if($order->items && $order->items->count() > 0)
                                    <div class="truncate font-medium text-gray-800" title="{{ $order->items->first()->book->title ?? 'Sách không tồn tại' }}">
                                        {{ $order->items->first()->book->title ?? 'Sách không tồn tại' }}
                                    </div>
                                    @if($order->items->count() > 1)
                                    <div class="text-xs text-gray-500 mt-1">
                                        ... và {{ $order->items->count() - 1 }} sản phẩm khác
                                    </div>
                                    @endif
                                    @else
                                    <span class="text-gray-400 italic text-xs">Chưa có chi tiết</span>
                                    @endif
                                </td>

                                <td class="font-bold text-gray-800">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>

                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('orders.show', $order->id) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-bold rounded hover:bg-gray-200 transition" style="text-decoration: none;">Chi tiết</a>

                                        @if($order->status == 'pending')
                                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }} này không? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded border-none cursor-pointer hover:bg-red-200 transition">Hủy đơn</button>
                                        </form>

                                        {{-- NÚT MỚI: Chỉ hiện khi Admin đã gạt sang Đang giao --}}
                                        @elseif($order->status == 'processing')
                                        <form action="{{ route('orders.complete', $order->id) }}" method="POST" onsubmit="return confirm('Bạn xác nhận đã nhận được hàng và sản phẩm còn nguyên vẹn chứ?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded border-none cursor-pointer hover:bg-green-200 transition">✅ Đã nhận hàng</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="empty-state" style="display: {{ $orders->count() > 0 ? 'none' : 'block' }}; text-align: center; padding: 40px 20px; color: #9ca3af;">
                    <span style="font-size: 2.5rem;">📦</span>
                    <p class="text-sm mt-3 italic text-gray-400">{{ $orders->count() > 0 ? 'Không có đơn hàng nào thuộc trạng thái này.' : 'Bạn chưa đặt mua đơn hàng nào tại hệ thống của chúng tôi.' }}</p>
                    @if($orders->count() === 0)
                    <a href="/shop" class="inline-block mt-4 text-xs font-bold text-white px-4 py-2 rounded-lg" style="background-color: #1e3e36; text-decoration: none;">Đến cửa hàng sắm sách ngay</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function filterOrders(status, element) {
        document.querySelectorAll('.v2t-tab').forEach(tab => tab.classList.remove('active'));
        element.classList.add('active');

        const rows = document.querySelectorAll('.order-row');
        let hasVisibleRow = false;

        rows.forEach(row => {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
                hasVisibleRow = true;
            } else {
                row.style.display = 'none';
            }
        });

        const tableContainer = document.getElementById('order-table-container');
        const emptyState = document.getElementById('empty-state');
        const orderTabs = document.getElementById('order-tabs');

        if (tableContainer && emptyState && orderTabs) {
            if (hasVisibleRow) {
                tableContainer.style.display = 'block';
                emptyState.style.display = 'none';
            } else {
                tableContainer.style.display = 'none';
                emptyState.style.display = 'block';
            }
        }
    }
</script>
@endsection