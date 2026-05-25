<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi trọn gói chuỗi seeder theo thứ tự logic bắt buộc
        $this->call([
            UserSeeder::class,        // 1. Tạo người dùng trước để lấy ID
            CategorySeeder::class,    // 2. Tạo danh mục sách
            SupplierSeeder::class,    // 3. Tạo nhà cung cấp đối tác
            BookSeeder::class,        // 4. Tạo sách (Cần ID danh mục)
            OrderSeeder::class,       // 5. Tạo đơn hàng (Cần ID khách hàng)
            ReviewSeeder::class,      // 6. Tạo đánh giá bình luận (Cần ID sách và khách)
        ]);
    }
}