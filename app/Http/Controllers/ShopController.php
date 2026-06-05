<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    // 1. Logic hiển thị trang Cửa hàng (Tích hợp Sắp xếp + Lọc Danh mục + Lọc Tác giả + Tìm kiếm)
    public function index(Request $request)
    {
        // Khởi tạo câu lệnh lấy sách kèm điểm đánh giá trung bình
        $query = Book::withAvg('reviews', 'rating');

        // BỘ LỌC 1: Lọc theo Danh mục sách (?category=id)
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // BỘ LỌC 2: Tìm kiếm theo từ khóa (?search=từ_khóa)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('author', 'like', '%' . $searchTerm . '%');
            });
        }

        // BỘ LỌC 3: Lọc theo Tác giả động (?author=Tên_Tác_Giả)
        if ($request->has('author') && $request->author != '') {
            $query->where('author', $request->author);
        }

        // BỘ LỌC 4: Sắp xếp động (?sort=loại_sắp_xếp)
        $sortType = $request->input('sort', 'newest'); // Mặc định là sách mới nhất
        if ($sortType == 'price_asc') {
            $query->orderBy('price', 'asc');   // Giá tăng dần
        } elseif ($sortType == 'price_desc') {
            $query->orderBy('price', 'desc');  // Giá giảm dần
        } else {
            $query->latest();                  // Mới phát hành lên đầu
        }

        // Thực hiện phân trang (9 cuốn/trang) và đính kèm các tham số lọc lên URL thanh địa chỉ
        $books = $query->paginate(9)->withQueryString();
        
        // Lấy danh mục để hiển thị lên Sidebar
        $categories = Category::all();

        // Lấy danh sách các tác giả duy nhất đang thực sự có sách trong kho làm bộ lọc
        $authors = Book::select('author')->distinct()->pluck('author');

        return view('shop', compact('books', 'categories', 'authors'));
    }

    // 2. Logic hiển thị trang Chi tiết sách và kiểm tra điều kiện đánh giá
    public function show($id)
    {
        // Lấy thông tin sách kèm điểm trung bình và danh sách nhận xét kèm người viết
        $book = Book::with(['reviews.user'])->withAvg('reviews', 'rating')->findOrFail($id);

        // Lấy thêm 4 cuốn sách cùng danh mục làm sách liên quan (loại trừ cuốn đang xem)
        $relatedBooks = Book::where('category_id', $book->category_id)
                            ->where('id', '!=', $book->id)
                            ->take(4)
                            ->get();

        // Kiểm tra xem User hiện tại đã từng mua và hoàn thành đơn hàng cuốn sách này chưa
        $hasPurchased = false;
        if (Auth::check()) {
            $hasPurchased = OrderItem::where('book_id', $id)
                ->whereHas('order', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->exists();
        }

        return view('detail', compact('book', 'relatedBooks', 'hasPurchased'));
    }
}