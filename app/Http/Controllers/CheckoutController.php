<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;

class CheckoutController extends Controller
{
    // 1. Hiển thị trang thanh toán độc lập
    public function index()
    {
        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        return view('checkout', compact('cart'));
    }

    // 2. Xử lý đặt hàng thực tế, lưu DB và TRỪ KHO (Bọc Transaction)
    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Tính toán tổng tiền đơn hàng
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Xử lý tính toán giảm giá từ coupon trong session
        $discount = 0;
        if (session()->has('coupon')) {
            $coupon = session()->get('coupon');
            if ($coupon['code'] === 'V2T10') {
                $discount = $subtotal * 0.1;
            } elseif ($coupon['code'] === 'GIAM50' && $subtotal >= 200000) {
                $discount = 50000;
            }
        }

        $totalAmount = $subtotal + 30000 - $discount;
        if ($totalAmount < 0) {
            $totalAmount = 0;
        }

        // Bắt đầu Transaction bảo vệ tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            $fullShippingAddress = $request->address . ', ' . $request->city;

            // Tạo đơn hàng mới
            $order = Order::create([
                'user_id'          => Auth::id(),
                'total_price'      => $totalAmount,
                'status'           => 'pending',
                'phone'            => $request->phone,
                'shipping_address' => $fullShippingAddress,
                'payment_method'   => $request->payment_method,
            ]);

            // Duyệt giỏ hàng để lưu chi tiết đơn và trừ kho
            foreach ($cart as $bookId => $item) {
                $book = Book::findOrFail($bookId);

                // Kiểm tra xem kho còn đủ sách không
                if ($book->stock < $item['quantity']) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'Sách "' . $book->title . '" hiện tại không đủ số lượng tồn kho!');
                }

                // Lưu bản ghi chi tiết đơn hàng
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id'  => $bookId,
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                ]);

                // Thực hiện trừ số lượng tồn kho của sách
                $book->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Xóa sạch giỏ hàng và coupon sau khi đặt thành công
            session()->forget('cart');
            session()->forget('coupon');

            return redirect('/')->with('success', '🎉 Đặt đơn hàng thành công! Đơn hàng #' . $order->id . ' của bạn đã được ghi nhận.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi hệ thống xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    // 3. Áp dụng mã giảm giá (Nối thẳng vào Database)
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = strtoupper($request->coupon_code);
        $cart = session()->get('cart', []);
        
        // Tính tổng tiền hiện tại của giỏ hàng
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return redirect()->back()->with('error', 'Mã giảm giá không tồn tại!');
        }


        if ($voucher->status !== 'active') {
            return redirect()->back()->with('error', 'Mã giảm giá này đã hết hạn hoặc bị khóa!');
        }

        if ($voucher->max_uses > 0 && $voucher->uses >= $voucher->max_uses) {
            return redirect()->back()->with('error', 'Mã giảm giá này đã hết lượt sử dụng!');
        }

        if ($subtotal < $voucher->min_order_value) {
            return redirect()->back()->with('error', 'Đơn hàng của bạn phải từ ' . number_format($voucher->min_order_value, 0, ',', '.') . 'đ để áp dụng mã này!');
        }

        $discount = 0;
        
        if ($voucher->type === 'percentage') {
            $discount = ($subtotal * $voucher->value) / 100;
            
            if (isset($voucher->max_discount) && $voucher->max_discount > 0 && $discount > $voucher->max_discount) {
                $discount = $voucher->max_discount;
            }
        } else {
            $discount = $voucher->value;
        }

        session()->put('coupon', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'type' => $voucher->type,
            'value' => $voucher->value,
            'discount' => $discount,
        ]);

        return redirect()->back()->with('success', '🎉 Đã áp dụng mã giảm giá ' . $voucher->code . ' thành công!');
    }

    // 4. Hủy mã giảm giá
    public function removeCoupon()
    {
        session()->forget('coupon');
        return redirect()->back()->with('success', 'Đã hủy áp dụng mã giảm giá.');
    }
}