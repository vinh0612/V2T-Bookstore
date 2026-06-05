<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordController extends Controller
{
    // Xử lý đổi mật khẩu an toàn
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng điền mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải dài từ 8 ký tự trở lên nha bạn.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới nhập lại chưa khớp kìa bạn.'
        ]);

        $user = User::findOrFail(Auth::id());

        // Kiểm tra xem mật khẩu hiện tại gõ vào có khớp với DB không
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Mật khẩu hiện tại không chính xác, thử lại nhé bạn!');
        }

        // Cập nhật mật khẩu mới đã được mã hóa
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', '🔒 Đổi mật khẩu mới thành công và bảo mật rồi nhé!');
    }
}