<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tài khoản Admin của bro
        User::updateOrCreate(
            ['email' => 'admin@v2t.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'), // Đổi mật khẩu của bro ở đây
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. Các khách hàng mẫu xuất hiện trong đơn hàng
        User::updateOrCreate(
            ['email' => 'nguyena@gmail.com'],
            [
                'name' => 'Nguyen Van A',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'nguyenb@gmail.com'],
            [
                'name' => 'Nguyen Thi B',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'status' => 'active',
            ]
        );
    }
}