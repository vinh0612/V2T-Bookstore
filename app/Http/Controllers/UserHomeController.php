<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review; // BỔ SUNG: Import trực tiếp Model Review cho code sạch sẽ
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    public function index()
    {
        // 1. Sách mới phát hành (Lấy 4 cuốn mới nhất kèm điểm đánh giá trung bình)
        $newBooks = Book::withAvg('reviews', 'rating')->latest()->take(4)->get();

        // 2. Sách nổi bật (Sắp xếp theo Điểm trung bình rating từ cao đến thấp)
        $featuredBooks = Book::withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->take(4)
            ->get();

        // 3. Sách bán chạy nhất (Đếm số lần xuất hiện trong chi tiết đơn hàng order_items)
        $bestSellers = Book::withAvg('reviews', 'rating')
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(4)
            ->get();

        // 4. Ý kiến khách hàng (Ưu tiên lấy 3 đánh giá tích cực 4-5 sao đã được Admin duyệt)
        $customerReviews = Review::with(['user', 'book'])
            ->where('rating', '>=', 4)
            ->where('status', 'approved')
            ->latest()
            ->take(3)
            ->get();

        // Dự phòng: Nếu chưa có đánh giá nào được duyệt, lấy đánh giá chung từ 4 sao trở lên
        if ($customerReviews->isEmpty()) {
            $customerReviews = Review::with(['user', 'book'])
                ->where('rating', '>=', 4)
                ->latest()
                ->take(3)
                ->get();
        }

        return view('home', compact('newBooks', 'featuredBooks', 'bestSellers', 'customerReviews'));
    }
}