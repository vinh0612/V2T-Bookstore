<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Ép kiểu xóa sạch dữ liệu cũ trong bảng nếu có
        Book::query()->delete();

        $books = [
            [
                'id' => 1,
                'title' => 'Mắt Biếc',
                'slug' => 'mat-biec',
                'author' => 'Nguyễn Nhật Ánh',
                'category_id' => 1,
                'price' => 220000,
                'stock' => 19,
                'image_url' => 'https://placehold.co/400x600/1e3e36/ffffff?text=Mat+Biec'
            ],
            [
                'id' => 2,
                'title' => 'The Silent Patient',
                'slug' => 'the-silent-patient',
                'author' => 'Alex Michaelides',
                'category_id' => 3,
                'price' => 250000,
                'stock' => 13,
                'image_url' => 'https://placehold.co/400x600/1e3e36/ffffff?text=The+Silent+Patient'
            ],
            [
                'id' => 3,
                'title' => 'The Shadow of the Wind',
                'slug' => 'the-shadow-of-the-wind',
                'author' => 'Carlos Ruiz Zafón',
                'category_id' => 3,
                'price' => 320000,
                'stock' => 8,
                'image_url' => 'https://placehold.co/400x600/1e3e36/ffffff?text=Shadow+Wind'
            ],
            [
                'id' => 4,
                'title' => 'Klara and the Sun',
                'slug' => 'klara-and-the-sun',
                'author' => 'Kazuo Ishiguro',
                'category_id' => 4,
                'price' => 285000,
                'stock' => 20,
                'image_url' => 'https://placehold.co/400x600/1e3e36/ffffff?text=Klara+Sun'
            ]
        ];

        foreach ($books as $book) {
            // Đổi sang dùng Create thuần túy cho fresh migration
            Book::create($book);
        }

        // In dòng này ra terminal để anh em mình kiểm tra xem dữ liệu có thực sự được chạy không
        $this->command->info('=====> [CHECK] Đã nạp thành công ' . Book::count() . ' cuốn sách vào database!');
    }
}