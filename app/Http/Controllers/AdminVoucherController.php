<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Order; // Liên kết nếu muốn tính doanh thu tiết kiệm thực tế
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminVoucherController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tính toán số liệu cho 3 thẻ thống kê ở hàng đầu
        $totalRunning = Voucher::where('status', 'active')
            ->where(function($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            })->count();

        $totalUses = Voucher::sum('uses');
        
        // Tính tổng tiền hệ thống đã giảm giá cho khách (giả định cột discount_amount ở bảng orders)
        $totalSaved = Schema::hasColumn('orders', 'discount_amount') ? Order::where('status', 'completed')->sum('discount_amount') : 12500000;

        // 2. Xử lý bộ lọc tìm kiếm và phân trang dữ liệu bảng
        $query = Voucher::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vouchers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers', 'totalRunning', 'totalUses', 'totalSaved'));
    }

    public function store(Request $request)
    {
        // Kiểm tra dữ liệu nhập vào
        $request->validate([
            'code' => 'required|unique:vouchers,code|max:50',
            'name' => 'required|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:0',
        ]);

        // Lưu dữ liệu vào Database
        Voucher::create([
            'code' => strtoupper($request->code), // Ép chữ in hoa cho mã code đẹp mắt
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_uses' => $request->max_uses ?? 0,
            'uses' => 0,
            'status' => 'active',
        ]);

        // Quay lại kèm thông báo thành công
        return redirect()->back()->with('success', 'Đã tạo mã giảm giá mới thành công!');
    }

    // 1. Hàm cập nhật thông tin mã giảm giá (Update)
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'code' => 'required|max:50|unique:vouchers,code,' . $id,
            'name' => 'required|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:0',
        ]);

        $voucher->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_uses' => $request->max_uses ?? 0,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin mã giảm giá thành công!');
    }

    // 2. Hàm xoá mã giảm giá khỏi hệ thống (Delete)
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->back()->with('success', 'Đã xóa mã giảm giá khỏi hệ thống!');
    }

    // 3. Hàm hoán đổi trạng thái Tạm dừng / Kích hoạt lại
    public function toggleStatus($id)
    {
        $voucher = Voucher::findOrFail($id);
        
        // Nếu đang chạy hoặc hết hạn thì hoán đổi sang Tạm dừng, ngược lại kích hoạt lại hoạt động
        if ($voucher->status === 'active') {
            $voucher->status = 'paused';
        } else {
            // Kiểm tra nếu có ngày hết hạn và đã qua ngày hiện tại thì ép trạng thái expired, không thì active
            $voucher->status = ($voucher->end_date && $voucher->end_date < now()->toDateString()) ? 'expired' : 'active';
        }
        
        $voucher->save();

        return redirect()->back()->with('success', 'Đã thay đổi trạng thái hoạt động của mã!');
    }
}