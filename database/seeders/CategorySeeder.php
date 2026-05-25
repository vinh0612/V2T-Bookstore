<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Văn học Trong Nước', 'slug' => 'van-hoc-trong-nuoc'],
            ['id' => 2, 'name' => 'Tâm Lý Học', 'slug' => 'tam-ly-hoc'],
            ['id' => 3, 'name' => 'Trinh Thám / Kinh Dị', 'slug' => 'trinh-tham-kinh-di'],
            ['id' => 4, 'name' => 'Khoa Học / Lịch Sử', 'slug' => 'khoa-hoc-lich-su'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['id' => $cat['id']], $cat);
        }
    }
}