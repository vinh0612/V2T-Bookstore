<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;

class UserProfileController extends Controller
{
    // Hiển thị thông tin cá nhân kèm lịch sử đơn hàng
    public function index()
    {
        $user = Auth::user();
        
        // Lấy toàn bộ đơn hàng của chính user này từ mới đến cũ
        $orders = Order::where('user_id', $user->id)->latest()->get();

        return view('profile', compact('user', 'orders'));
    }

    // Xử lý cập nhật thông tin cơ bản (Họ tên, Email)
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
}