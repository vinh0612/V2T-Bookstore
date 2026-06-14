<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;

class UserProfileController extends Controller
{
    // Hiển thị thông tin cá nhân kèm lịch sử đơn hàng (Đã nâng cấp)
    public function index()
    {
        $user = Auth::user();
        
        // NÂNG CẤP: Dùng with('items.book') để lấy luôn chi tiết đơn và tên sách (Eager Loading)
        $orders = Order::with('items.book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('profile', compact('user', 'orders'));
    }

    // Xử lý cập nhật thông tin cơ bản
    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Bạn không được để trống họ tên nhé.',
            'email.required' => 'Vui lòng điền địa chỉ Email đầy đủ.',
            'email.unique' => 'Email này đã có người khác sử dụng rồi bạn ơi.'
        ]);

        $user->update([
            'name' => $request->name,   
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', '🎉 Cập nhật thông tin cá nhân thành công rồi nhé!');
    }

    // TÍNH NĂNG MỚI: Xử lý Hủy đơn hàng và hoàn kho
    public function cancel($id)
    {
        // Tìm đơn hàng đúng của user đang đăng nhập
        $order = Order::with('items.book')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Ràng buộc bảo mật: Chỉ cho hủy nếu đang chờ duyệt
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng đang ở trạng thái chờ duyệt.');
        }

        // Logic Sinh tử: Hoàn lại số lượng tồn kho cho từng cuốn sách
        foreach ($order->items as $item) {
            if ($item->book) {
                // Lấy số lượng khách đã mua cộng trả lại vào kho
                $item->book->increment('stock', $item->quantity);
            }
        }

        // Cập nhật trạng thái đơn thành "Đã hủy"
        $order->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Đã hủy đơn hàng và hoàn số lượng sách về kho thành công.');
    }

    // TÍNH NĂNG MỚI: Xem chi tiết đơn hàng
    public function showOrder($id)
    {
        // Vẫn phải bọc bảo mật: Chỉ cho xem đơn của chính mình
        $order = Order::with('items.book')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('order-details', compact('order'));
    }

    public function completeOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        // Chỉ cho phép bấm khi đang giao
        if ($order->status == 'processing') {
            $order->status = 'completed';
            $order->save();
            return back()->with('success', '🎉 Cảm ơn bạn đã xác nhận. Chúc bạn có những phút giây đọc sách vui vẻ!');
        }

        return back()->with('error', 'Không thể thao tác trên đơn hàng này!');
    }
}