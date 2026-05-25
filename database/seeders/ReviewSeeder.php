<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $userA = User::where('email', 'nguyena@gmail.com')->first();
        
        if ($userA) {
            Review::updateOrCreate(
                ['id' => 1],
                [
                    'user_id' => $userA->id,
                    'book_id' => 1, // Sách Mắt Biếc
                    'rating' => 5,
                    'comment' => 'Sách đóng gói rất cẩn thận, nội dung của Nguyễn Nhật Ánh thì không cần bàn cãi nhiều. Rất đáng mua!',
                    'status' => 'approved',
                    'is_purchased' => true,
                    'admin_reply' => 'Cảm ơn bro đã ủng hộ kho sách V2T Bookstore nhé!',
                ]
            );

            Review::updateOrCreate(
                ['id' => 2],
                [
                    'user_id' => $userA->id,
                    'book_id' => 2, // The Silent Patient
                    'rating' => 2,
                    'comment' => 'Giao hàng hơi chậm, bìa sách bị móp nhẹ một góc làm mình không ưng ý lắm.',
                    'status' => 'pending', // Trạng thái chờ duyệt hiển thị ngoài admin
                    'is_purchased' => true,
                    'admin_reply' => null,
                ]
            );
        }
    }
}