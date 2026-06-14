<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\Http;

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

    // 2. Xử lý đặt hàng (Đã fix logic Transaction, MoMo & Voucher)
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'address' => 'required|string',
                'city' => 'required|string',
                'phone' => ['required', 'string', 'regex:/^0[0-9]{9}$/'],
                'payment_method' => 'required|string',
            ], [
                'phone.regex' => 'Số điện thoại không hợp lệ. Vui lòng nhập 10 chữ số bắt đầu bằng số 0.',
            ]);

            $cart = session()->get('cart', []);
            if (count($cart) == 0) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
            }

            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Lấy thông tin Voucher từ Session ra
            $discountAmount = 0;
            $discountCode = null;
            $voucherId = null;

            if (session()->has('coupon')) {
                $discountAmount = session('coupon.discount');
                $discountCode = session('coupon.code');
                $voucherId = session('coupon.id');
            }

            $totalAmount = $subtotal + 30000 - $discountAmount;
            if ($totalAmount < 0) {
                $totalAmount = 0;
            }

            // Bắt đầu Transaction bảo vệ DB
            DB::beginTransaction();

            $fullShippingAddress = $request->address . ', ' . $request->city;

            // LƯU ĐƠN HÀNG VÀ KÈM LUÔN MÃ GIẢM GIÁ
            $order = Order::create([
                'user_id'          => Auth::id(),
                'total_price'      => $totalAmount,
                'status'           => 'pending',
                'phone'            => $request->phone,
                'shipping_address' => $fullShippingAddress,
                'payment_method'   => $request->payment_method,
                'discount_code'    => $discountCode,     // Thêm dòng này
                'discount_amount'  => $discountAmount,   // Thêm dòng này
            ]);

            foreach ($cart as $bookId => $item) {
                $book = Book::findOrFail($bookId);

                if ($book->stock < $item['quantity']) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'Sách "' . $book->title . '" hiện tại không đủ số lượng tồn kho!');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id'  => $bookId,
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                ]);

                $book->decrement('stock', $item['quantity']);
            }

            // TĂNG LƯỢT SỬ DỤNG VOUCHER (Nếu có dùng mã)
            if ($voucherId) {
                $voucher = Voucher::find($voucherId);
                if ($voucher) {
                    $voucher->increment('uses');
                }
            }

            // BẺ LÁI SANG MOMO NGAY TẠI ĐÂY
            if ($request->payment_method === 'momo') {
                $momoResult = $this->createMomoPayment($order);
                
                if ($momoResult['success']) {
                    DB::commit();
                    session()->forget('cart');
                    session()->forget('coupon');
                    return redirect($momoResult['url']);
                } else {
                    DB::rollBack(); // Lỗi MoMo thì nhả lại sách tồn kho, nhả luôn lượt dùng Voucher
                    return redirect()->back()->with('error', 'Lỗi thanh toán MoMo: ' . $momoResult['message']);
                }
            }

            // DÀNH CHO PHƯƠNG THỨC COD
            DB::commit();
            session()->forget('cart');
            session()->forget('coupon');

            return redirect('/profile')->with('success', '🎉 Đặt đơn hàng thành công! Đơn hàng #' . $order->id . ' của bạn đã được ghi nhận.');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return redirect()->back()->with('error', 'Có lỗi hệ thống xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }


    // Hàm 1: Đóng gói dữ liệu gửi sang MoMo (GÀI TRAP DD() BẮT BUG DỮ LIỆU THẬT)
    public function createMomoPayment($order)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        
        $partnerCode = "MOMOBKUN20180529";
        $accessKey   = "klm05TvNBzhg7h7j";
        $secretKey   = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";
        
        $amountValue = (int)$order->total_price;
        $amountString = (string)$amountValue;
        
        // Rút gọn tối đa chuỗi này, đề phòng dính ký tự lạ
        $orderInfo   = "Thanh toan don hang " . $order->id; 
        $orderId     = $order->id . "_" . time(); 
        $requestId   = $orderId;
        
        // Dùng url() hoặc env('APP_URL') thay vì route() để kiểm soát tuyệt đối đường dẫn
        $redirectUrl = url('/checkout/momo-return');
        $ipnUrl      = url('/checkout/momo-return');
        $extraData   = "";
        $requestType = "captureWallet";
        
        // Nối chuỗi trần trụi
        $rawHash = "accessKey=" . $accessKey 
                 . "&amount=" . $amountString 
                 . "&extraData=" . $extraData 
                 . "&ipnUrl=" . $ipnUrl 
                 . "&orderId=" . $orderId 
                 . "&orderInfo=" . $orderInfo 
                 . "&partnerCode=" . $partnerCode 
                 . "&redirectUrl=" . $redirectUrl 
                 . "&requestId=" . $requestId 
                 . "&requestType=" . $requestType;
        
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "V2T Bookstore",
            'storeId'     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amountValue,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];
        
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($endpoint, $data);
            
            $jsonResult = $response->json();
            
            // QUẢ MÌN DD() SẼ NỔ Ở ĐÂY NẾU MOMO CÒN DÁM TỪ CHỐI
            if (isset($jsonResult['resultCode']) && $jsonResult['resultCode'] != 0) {
                dd(
                    'MOMO TỪ CHỐI!',
                    'LÝ DO:', 
                    $jsonResult['message'] ?? 'Không rõ',
                    'DỮ LIỆU GỬI ĐI:', 
                    $data,
                    'CHUỖI BĂM CỦA MÌNH:', 
                    $rawHash
                );
            }
            
            if (isset($jsonResult['payUrl'])) {
                return ['success' => true, 'url' => $jsonResult['payUrl']];
            }
            
            return ['success' => false, 'message' => 'Không nhận được link thanh toán từ MoMo.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Lỗi kết nối HTTP: ' . $e->getMessage()];
        }
    }

    // Hàm 2: Hứng kết quả MoMo trả về
    public function momoReturn(\Illuminate\Http\Request $request)
    {
        // resultCode == 0 nghĩa là Khách đã thanh toán thành công
        if ($request->resultCode == 0) {
            // Tách cái id đơn hàng thật ra khỏi cái time()
            $realOrderId = explode('_', $request->orderId)[0];
            
            $order = \App\Models\Order::find($realOrderId);
            
            // Cập nhật trạng thái thành Đang giao
            if ($order && $order->status == 'pending') {
                $order->update(['status' => 'processing']);
            }
            
            return redirect('/profile')->with('success', '🎉 Chúc mừng! Đã thanh toán MoMo thành công cho đơn hàng #' . $realOrderId);
        }
        
        // Khách bấm hủy thanh toán hoặc quét mã bị lỗi
        return redirect('/profile')->with('error', 'Giao dịch MoMo đã bị hủy hoặc thất bại.');
    }

    // 3. Áp dụng mã giảm giá
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = strtoupper($request->coupon_code);
        $cart = session()->get('cart', []);
        
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