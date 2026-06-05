<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại để làm sạch bảng cũ mà không bị lỗi ràng buộc
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // DANH MỤC GỐC (Cha Lớn - parent_id = null)
        $sachTrongNuoc = Category::create(['name' => 'Sách Trong Nước', 'slug' => 'sach-trong-nuoc', 'parent_id' => null]);
        $sachNuocNgoai = Category::create(['name' => 'Sách Nước Ngoài', 'slug' => 'sach-nuoc-ngoai', 'parent_id' => null]);

        // NHÓM DANH MỤC CON (Nằm trong "Sách Trong Nước")
        $vanHoc = Category::create(['name' => 'Văn Học', 'slug' => 'van-hoc', 'parent_id' => $sachTrongNuoc->id]);
        $kinhTe = Category::create(['name' => 'Kinh Tế', 'slug' => 'kinh-te', 'parent_id' => $sachTrongNuoc->id]);
        $tamLy  = Category::create(['name' => 'Tâm Lý - Kỹ Năng Sống', 'slug' => 'tam-ly-ky-nang-song', 'parent_id' => $sachTrongNuoc->id]);
        $nuoiDay = Category::create(['name' => 'Nuôi Dạy Con', 'slug' => 'nuoi-day-con', 'parent_id' => $sachTrongNuoc->id]);

        // CHỦ ĐỀ CHI TIẾT (Con của Cấp 2)
        
        // Thể loại thuộc nhóm Văn Học
        Category::create(['name' => 'Tiểu Thuyết', 'slug' => 'tieu-thuyet', 'parent_id' => $vanHoc->id]);
        Category::create(['name' => 'Truyện Ngắn - Tản Văn', 'slug' => 'truyen-ngan-tan-van', 'parent_id' => $vanHoc->id]);
        Category::create(['name' => 'Light Novel', 'slug' => 'light-novel', 'parent_id' => $vanHoc->id]);
        Category::create(['name' => 'Ngôn Tình', 'slug' => 'ngon-tinh', 'parent_id' => $vanHoc->id]);

        // Thể loại thuộc nhóm Kinh Tế
        Category::create(['name' => 'Nhân Vật - Bài Học Kinh Doanh', 'slug' => 'nhan-vat-bai-hoc-kinh-doanh', 'parent_id' => $kinhTe->id]);
        Category::create(['name' => 'Quản Trị - Lãnh Đạo', 'slug' => 'quan-tri-lanh-dao', 'parent_id' => $kinhTe->id]);
        Category::create(['name' => 'Marketing - Bán Hàng', 'slug' => 'marketing-ban-hang', 'parent_id' => $kinhTe->id]);

        // Thể loại thuộc nhóm Tâm Lý
        Category::create(['name' => 'Kỹ Năng Sống', 'slug' => 'ky-nang-song', 'parent_id' => $tamLy->id]);
        Category::create(['name' => 'Rèn Luyện Nhân Cách', 'slug' => 'ren-luyen-nhan-cach', 'parent_id' => $tamLy->id]);
        Category::create(['name' => 'Tâm Lý Học', 'slug' => 'tam-ly-hoc', 'parent_id' => $tamLy->id]);

        // Thể loại thuộc nhóm Nuôi Dạy Con
        Category::create(['name' => 'Cẩm Ngang Làm Cha Mẹ', 'slug' => 'cam-nang-lam-cha-me', 'parent_id' => $nuoiDay->id]);
        Category::create(['name' => 'Phương Pháp Giáo Dục Trẻ', 'slug' => 'phuong-phap-giao-duc-tre', 'parent_id' => $nuoiDay->id]);
    }
}