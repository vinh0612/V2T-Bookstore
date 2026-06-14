@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="container mx-auto max-w-4xl py-10 px-4">
    <a href="{{ route('profile') }}" class="inline-flex items-center text-sm font-bold text-gray-600 hover:text-green-800 mb-6" style="text-decoration: none;">
        ← Quay lại trang tài khoản
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-6 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-serif font-bold text-gray-900">Chi tiết đơn hàng #{{ $order->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">Đặt lúc: {{ $order->created_at->format('H:i - d/m/Y') }}</p>
            </div>
            <div>
                @if($order->status == 'pending')
                <span class="px-4 py-2 text-sm font-bold uppercase rounded-full bg-amber-100 text-amber-800">Chờ duyệt</span>
                @elseif($order->status == 'processing')
                <span class="px-4 py-2 text-sm font-bold uppercase rounded-full bg-blue-100 text-blue-800">Đang giao</span>
                @elseif($order->status == 'completed')
                <span class="px-4 py-2 text-sm font-bold uppercase rounded-full bg-green-100 text-green-800">Thành công</span>
                @else
                <span class="px-4 py-2 text-sm font-bold uppercase rounded-full bg-red-100 text-red-800">Đã hủy đơn</span>
                @endif
            </div>
        </div>

        <div class="p-6 border-b border-gray-100 bg-white">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">Thông tin nhận hàng</h3>
            <div class="text-sm text-gray-600 space-y-2">
                <p><strong>Người nhận:</strong> {{ $order->user->name ?? 'Khách hàng' }}</p>
                <p><strong>Số điện thoại:</strong> {{ $order->phone ?? 'Không có thông tin' }}</p>
                <p><strong>Địa chỉ giao:</strong> {{ $order->shipping_address }}</p>
                <p><strong>Phương thức TT:</strong>
                    <span class="font-medium text-gray-800">
                        @if($order->payment_method == 'momo')
                        <span class="text-[#a50064] font-bold">Ví điện tử MoMo</span>
                        @elseif($order->payment_method == 'credit')
                        <span class="text-blue-600 font-bold">Thẻ Tín dụng / Ghi nợ</span>
                        @else
                        Thanh toán khi nhận hàng (COD)
                        @endif
                    </span>
                </p>
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Sản phẩm đã mua</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-200">
                        <th class="py-3 px-4 text-xs font-bold text-gray-600">SÁCH</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 text-center">ĐƠN GIÁ</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 text-center">SỐ LƯỢNG</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-600 text-right">THÀNH TIỀN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="py-4 px-4 flex items-center gap-4">
                            <div class="w-12 h-16 bg-gray-200 rounded object-cover flex-shrink-0" style="background-image: url('{{ asset('storage/' . ($item->book->image_url ?? '')) }}'); background-size: cover; background-position: center;"></div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $item->book->title ?? 'Sách đã bị xóa khỏi hệ thống' }}</p>
                                <p class="text-xs text-gray-500">{{ $item->book->author ?? '' }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center text-sm text-gray-600">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                        <td class="py-4 px-4 text-center text-sm text-gray-600 font-medium">x{{ $item->quantity }}</td>
                        <td class="py-4 px-4 text-right text-sm font-bold text-gray-800">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- KHU VỰC TỔNG KẾT TIỀN CHUẨN SHOPEE --}}
            <div class="mt-6 flex justify-end">
                <div class="w-72 space-y-3">
                    @php
                        // Tính tổng tiền gốc của các món hàng
                        $subTotal = $order->items->sum(function($item) {
                            return $item->price * $item->quantity;
                        });
                        
                        // Phí vận chuyển mặc định (Bro có thể thay bằng $order->shipping_fee nếu có trong DB)
                        $shippingFee = 30000;
                    @endphp

                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tổng tiền hàng:</span>
                        <span>{{ number_format($subTotal, 0, ',', '.') }}đ</span>
                    </div>

                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($shippingFee, 0, ',', '.') }}đ</span>
                    </div>

                    {{-- Bỏ đoạn > 0 đi, chỉ cần có cột này trong DB là cho hiện luôn --}}
                    @if(isset($order->discount_amount))
                    <div class="flex justify-between text-sm font-medium text-green-600">
                        <span>
                            Voucher giảm giá: 
                            @if(!empty($order->discount_code))
                                <span class="uppercase border border-green-200 bg-green-50 px-1 rounded text-xs ml-1">{{ $order->discount_code }}</span>
                            @else
                                <span class="text-xs text-gray-400 ml-1">(Không áp dụng)</span>
                            @endif
                        </span>
                        <span>- {{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-lg font-bold text-gray-900 border-t border-gray-200 pt-3">
                        <span>Thành tiền:</span>
                        <span class="text-[var(--color-v2t-green)]" style="color: #1e3e36;">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection