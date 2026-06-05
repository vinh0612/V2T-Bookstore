<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        // Làm sạch bảng cũ để tránh trùng lặp dữ liệu khi chạy lại lệnh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Voucher::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Mã giảm giá Hè - INKWELL10
        Voucher::create([
            'code' => 'INKWELL10',
            'name' => 'Giảm giá hè 2026',
            'type' => 'percentage',
            'value' => 10.00,
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-31',
            'max_uses' => 500,
            'uses' => 452,
            'status' => 'active'
        ]);

        // 2. Mã Bạn đọc mới - BOOKLOVER50
        Voucher::create([
            'code' => 'BOOKLOVER50',
            'name' => 'Ưu đãi bạn đọc mới',
            'type' => 'fixed',
            'value' => 50000.00,
            'start_date' => '2026-01-01',
            'end_date' => null, // Vô thời hạn
            'max_uses' => 0,    // Vô hạn lượt dùng
            'uses' => 890,
            'status' => 'active'
        ]);

        // 3. Mã Tết cũ - TET2026
        Voucher::create([
            'code' => 'TET2026',
            'name' => 'Khuyến mãi Tết',
            'type' => 'percentage',
            'value' => 25.00,
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-15',
            'max_uses' => 1000,
            'uses' => 1000, // Đã dùng hết 1000/1000
            'status' => 'expired'
        ]);
    }
}