<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $userA = User::where('email', 'nguyena@gmail.com')->first();
        $userB = User::where('email', 'nguyenb@gmail.com')->first();

        if ($userA && $userB) {
            $orders = [
                [
                    'id' => 1,
                    'user_id' => $userA->id,
                    'total_price' => 280000,
                    'status' => 'pending',
                    'phone' => '0901234567',
                    'shipping_address' => '280 An Dương Vương, Quận 5, TP.HCM',
                    'payment_method' => 'cod', // Bổ sung phương thức thanh toán mẫu
                    'created_at' => now(),
                ],
                [
                    'id' => 2,
                    'user_id' => $userA->id,
                    'total_price' => 280000,
                    'status' => 'shipping',
                    'phone' => '0901234567',
                    'shipping_address' => '280 An Dương Vương, Quận 5, TP.HCM',
                    'payment_method' => 'cod',
                    'created_at' => now(),
                ],
                [
                    'id' => 3,
                    'user_id' => $userA->id,
                    'total_price' => 280000,
                    'status' => 'completed',
                    'phone' => '0901234567',
                    'shipping_address' => '280 An Dương Vương, Quận 5, TP.HCM',
                    'payment_method' => 'cod',
                    'created_at' => now(),
                ],
                [
                    'id' => 4,
                    'user_id' => $userB->id,
                    'total_price' => 250000,
                    'status' => 'pending',
                    'phone' => '0987654321',
                    'shipping_address' => '115 Lữ Gia, Quận 11, TP.HCM',
                    'payment_method' => 'banking', // Thử nghiệm một đơn chuyển khoản ngân hàng
                    'created_at' => now(),
                ],
            ];

            foreach ($orders as $order) {
                Order::updateOrCreate(['id' => $order['id']], $order);
            }
        }
    }
}