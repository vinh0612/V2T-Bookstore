<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Review;
use App\Models\Import;
use App\Models\ImportItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /* =========================================================================
     * 1. DASHBOARD CHÍNH
     * ========================================================================= */
    public function index()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        $totalOrders = Order::count();
        $totalUsers = User::count();

        $topBooks = Book::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(3)
            ->get();

        $recentOrders = Order::with(['user', 'books'])->latest()->limit(5)->get();

        $dbDriver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        if ($dbDriver === 'sqlite') {
            $monthlyRevenueData = Order::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->selectRaw('strftime("%m", created_at) as month, SUM(total_price) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->all();
            $tempData = [];
            foreach ($monthlyRevenueData as $m => $tot) {
                $tempData[(int)$m] = $tot;
            }
            $monthlyRevenueData = $tempData;
        } else {
            $monthlyRevenueData = Order::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->all();
        }

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyRevenueData[$i] ?? 0;
        }

        return view('admin.dashboard', compact('totalRevenue', 'totalOrders', 'totalUsers', 'topBooks', 'recentOrders', 'chartData'));
    }

    /* =========================================================================
     * 2. QUẢN LÝ KHO SÁCH
     * ========================================================================= */
    public function booksIndex()
    {
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');
        $lowStock = Book::where('stock', '<', 10)->count();
        
        // Đã khóa chặt theo năm hiện tại tránh lỗi cộng gộp dữ liệu năm cũ
        $monthlyRevenue = Order::where('status', 'completed')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('total_price');

        $books = Book::withAvg('reviews', 'rating')->latest()->paginate(10);

        return view('admin.books', compact('books', 'totalBooks', 'totalStock', 'lowStock', 'monthlyRevenue'));
    }

    public function booksCreate()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with('error', 'Bạn không có quyền truy cập vào khu vực này!');
        }
        $categories = Category::all(); 
        return view('admin.books_create', compact('categories'));
    }

    public function booksStore(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with('error', 'Bạn không có quyền truy cập vào khu vực này!');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sách.',
            'author.required' => 'Vui lòng nhập tên tác giả.',
            'category_id.required' => 'Vui lòng chọn danh mục cho cuốn sách này.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'image_url.url' => 'Địa chỉ đường dẫn ảnh không hợp lệ.',
        ]);

        $slug = Str::slug($request->title);
        $count = Book::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . time();
        }

        Book::create([
            'title' => $request->title,
            'slug' => $slug,
            'author' => $request->author,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_url' => $request->image_url ?? 'https://placehold.co/400x600/10b981/ffffff?text=V2T+Bookstore',
            'description' => $request->description,
        ]);

        return redirect()->route('admin.books.index')->with('success', '🎉 Thêm cuốn sách mới vào kho hàng thành công rồi nhé!');
    }

    public function booksEdit($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with('error', 'Bạn không có quyền truy cập vào khu vực này!');
        }
        $book = Book::findOrFail($id);
        $categories = Category::all(); 
        return view('admin.books_edit', compact('book', 'categories'));
    }

    public function booksUpdate(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with('error', 'Bạn không có quyền truy cập vào khu vực này!');
        }

        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'title.required' => 'Tiêu đề sách không được để trống.',
            'author.required' => 'Tên tác giả không được để trống.',
            'price.required' => 'Vui lòng nhập giá tiền.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
        ]);

        $slug = Str::slug($request->title);
        $count = Book::where('slug', $slug)->where('id', '!=', $id)->count();
        if ($count > 0) {
            $slug = $slug . '-' . time();
        }

        $book->update([
            'title' => $request->title,
            'slug' => $slug,
            'author' => $request->author,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_url' => $request->image_url ?? 'https://placehold.co/400x600/10b981/ffffff?text=V2T+Bookstore',
            'description' => $request->description,
        ]);

        return redirect()->route('admin.books.index')->with('success', '🎉 Cập nhật thông tin cuốn sách thành công!');
    }

    public function booksDestroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with('error', 'Bạn không có quyền truy cập vào khu vực này!');
        }
        $book = Book::findOrFail($id);
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', '🗑️ Đã xóa cuốn sách khỏi hệ thống thành công!');
    }

    /* =========================================================================
     * 3. QUẢN LÝ ĐƠN HÀNG
     * ========================================================================= */
    public function ordersIndex(Request $request)
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        // ĐÃ CHUẨN HÓA: Dùng 'processing' thay vì 'shipping' để khớp với UserProfile
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        // THÊM MỚI: Đếm số lượng đơn đã hủy
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $query = Order::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Hỗ trợ tìm kiếm theo ID đơn hàng hoặc Tên khách hàng
                $cleanSearchId = str_replace('INK-', '', $search);
                $cleanSearchId = str_replace('#', '', $cleanSearchId); // Bỏ luôn cả dấu # nếu khách gõ
                $q->where('id', 'like', '%' . $cleanSearchId . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(10);
        
        // ĐÃ CHUẨN HÓA: Truyền biến processingOrders và cancelledOrders ra View
        return view('admin.orders', compact('orders', 'totalOrders', 'pendingOrders', 'processingOrders', 'completedOrders', 'cancelledOrders'));
    }

    public function ordersShow($id)
    {
        // Kéo đơn hàng ra, đính kèm thông tin User (Người mua) và Items (Các cuốn sách)
        $order = Order::with(['user', 'items.book'])->findOrFail($id);
        
        return view('admin.orders_show', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // 1. CHỐNG HACK: Nếu đơn đã khóa (Thành công / Hủy) thì cấm tiệt mọi thao tác
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', '❌ Đơn hàng này đã đóng, không thể thay đổi trạng thái!');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);
        
        $newStatus = $request->status;

        // 2. NGĂN NHẢY CÓC: Chờ duyệt KHÔNG ĐƯỢC nhảy thẳng lên Thành công
        if ($order->status == 'pending' && $newStatus == 'completed') {
            return back()->with('error', '❌ Sai luồng! Đơn hàng phải qua bước Đang giao trước khi Thành công.');
        }
        
        // Logic hoàn kho nếu Hủy
        if ($newStatus == 'cancelled' && $order->status != 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->book) {
                    $item->book->increment('stock', $item->quantity);
                }
            }
        }

        $order->status = $newStatus;
        $order->save();
        
        return back()->with('success', '✅ Đã cập nhật trạng thái đơn hàng thành công!');
    }

    /* =========================================================================
     * 4. QUẢN LÝ NGƯỜI DÙNG
     * ========================================================================= */
    public function usersIndex()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $users = User::latest()->paginate(10);

        return view('admin.users', compact('users', 'totalUsers', 'activeUsers'));
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        // LỚP BẢO VỆ 1: Chặn đứng ý đồ khóa tài khoản Admin
        if ($user->role === 'admin') {
            return back()->with('error', '⛔ Cảnh báo: Bạn không có quyền khóa tài khoản Quản trị viên!');
        }

        $user->status = $user->status == 'active' ? 'blocked' : 'active';
        $user->save();
        return back()->with('success', 'Đã cập nhật trạng thái tài khoản người dùng thành công!');
    }

    public function usersShow($id)
    {
        $user = User::findOrFail($id);
        $orderCount = Order::where('user_id', $id)->count();
        $totalSpent = Order::where('user_id', $id)->where('status', 'completed')->sum('total_price');
        return view('admin.users_show', compact('user', 'orderCount', 'totalSpent'));
    }

    // HÀM MỚI: Xử lý xóa người dùng
    public function usersDestroy($id)
    {
        $user = User::findOrFail($id);

        // LỚP BẢO VỆ 2: Chặn đứng ý đồ xóa tài khoản Admin
        if ($user->role === 'admin') {
            return back()->with('error', '⛔ Cảnh báo: Tài khoản Quản trị viên được bảo vệ tuyệt đối, không thể xóa!');
        }

        $user->delete();

        return back()->with('success', '🗑️ Đã xóa vĩnh viễn tài khoản người dùng khỏi hệ thống!');
    }

    /* =========================================================================
     * 5. QUẢN LÝ NHÀ CUNG CẤP (SUPPLIERS)
     * ========================================================================= */
    public function suppliersIndex()
    {
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('status', 'active')->count();
        $pausedSuppliers = Supplier::where('status', 'paused')->count();
        $suppliers = Supplier::latest()->paginate(10);

        return view('admin.suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers', 'pausedSuppliers'));
    }

    public function suppliersCreate()
    {
        return view('admin.suppliers.create');
    }

    public function suppliersStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contract_code' => 'required|string|max:255|unique:suppliers,contract_code',
            'contact_name' => 'required|string|max:255',
            'contact_title' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:20'],
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,paused',
        ], [
            // VIETSUB CÂU LỖI CHO NÓ THÂN THIỆN
            'name.required' => 'Tên nhà cung cấp không được để trống.',
            'contract_code.required' => 'Mã hợp đồng không được để trống.',
            'contract_code.unique' => 'Mã hợp đồng này đã tồn tại trong hệ thống rồi.',
            'contact_name.required' => 'Không được để trống tên người liên hệ.',
            'contact_title.required' => 'Chức vụ người liên hệ không được bỏ trống.',
            'phone.required' => 'Không được để trống số điện thoại.',
            'phone.regex' => 'Số điện thoại phải bắt đầu từ 0 và đủ 10 số',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 số.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Sai định dạng Email, phải có @domain.com.',
        ]);

        Supplier::create($request->all());
        return redirect()->route('admin.suppliers.index')->with('success', '🎉 Thêm nhà cung cấp mới thành công!');
    }

    public function suppliersEdit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function suppliersUpdate(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'contract_code' => 'required|string|max:255|unique:suppliers,contract_code,' . $id,
            'contact_name' => 'required|string|max:255',
            'contact_title' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^0[0-9]{9}$/'],
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,paused',
        ], [
            // VIETSUB TƯƠNG TỰ BÊN STORE
            'name.required' => 'Tên nhà cung cấp không được để trống.',
            'contract_code.required' => 'Mã hợp đồng không được để trống.',
            'contract_code.unique' => 'Mã hợp đồng này đã tồn tại trong hệ thống rồi.',
            'contact_name.required' => 'Không được để trống tên người liên hệ.',
            'contact_title.required' => 'Chức vụ người liên hệ không được bỏ trống.',
            'phone.required' => 'Quên nhập số điện thoại kìa bro.',
            'phone.regex' => 'Số điện thoại nhìn có vẻ sai sai, chỉ được nhập số (có thể chứa khoảng trắng hoặc dấu + -).',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 số.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Định dạng Email sai rồi.',
        ]);

        $supplier->update($request->all());
        return redirect()->route('admin.suppliers.index')->with('success', '🎉 Cập nhật thông tin nhà cung cấp thành công!');
    }

    public function suppliersDestroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', '🗑️ Đã xóa nhà cung cấp khỏi hệ thống!');
    }

    public function suppliersImport($id)
    {
        $supplier = Supplier::findOrFail($id);
        // Lấy tất cả sách ra để Admin chọn cuốn muốn nhập kho
        $books = Book::orderBy('title', 'asc')->get();
        return view('admin.suppliers.import', compact('supplier', 'books'));
    }

    public function suppliersImportStore(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'book_ids' => 'required|array|min:1',
            'quantities' => 'required|array',
            'import_prices' => 'required|array',
            'note' => 'nullable|string',
        ], [
            'book_ids.required' => 'Vui lòng chọn ít nhất một cuốn sách để nhập kho.',
        ]);

        // Dùng Transaction để đảm bảo an toàn dữ liệu tuyệt đối
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $supplier) {
            // 1. Tạo phiếu nhập tổng
            $import = Import::create([
                'supplier_id' => $supplier->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'total_amount' => 0, // Sẽ tính toán và update sau
                'note' => $request->note,
            ]);

            $totalAmount = 0;

            // 2. Duyệt qua từng dòng sách được chọn để lưu chi tiết và cộng kho
            foreach ($request->book_ids as $index => $bookId) {
                $qty = $request->quantities[$index];
                $price = $request->import_prices[$index];
                $subTotal = $qty * $price;
                $totalAmount += $subTotal;

                // Lưu vào bảng chi tiết phiếu nhập
                ImportItem::create([
                    'import_id' => $import->id,
                    'book_id' => $bookId,
                    'quantity' => $qty,
                    'import_price' => $price,
                ]);

                // ĐẬP THẲNG VÀO KHO: Cộng dồn số lượng vào bảng books
                $book = Book::findOrFail($bookId);
                $book->increment('stock', $qty);
            }

            // 3. Cập nhật lại tổng tiền chính xác cho phiếu nhập
            $import->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('admin.suppliers.index')->with('success', '🎉 Lập phiếu nhập kho thành công! Sách đã được tự động cộng thẳng vào kho hàng.');
    }

    /* =========================================================================
     * 6. QUẢN LÝ ĐÁNH GIÁ (REVIEWS) - ĐÃ BỔ SUNG ĐẦY ĐỦ TẠI ĐÂY
     * ========================================================================= */
    public function reviewsIndex(Request $request)
    {
        $totalCount = Review::count();
        $pendingCount = Review::where('status', 'pending')->count();
        $avgRating = round(Review::where('status', 'approved')->avg('rating') ?? 0, 1);
        $violatedCount = Review::where('status', 'violated')->count();
        $suspiciousCount = Review::where('status', 'suspicious')->count(); // Đếm ở đây rồi
        
        $query = Review::with(['book', 'user']);
        
        if ($request->has('tab') && in_array($request->tab, ['pending', 'approved', 'violated', 'suspicious'])) {
            $query->where('status', $request->tab);
        }

        $reviews = $query->latest()->paginate(10);
        
        return view('admin.reviews.index', compact('reviews', 'totalCount', 'pendingCount', 'avgRating', 'violatedCount', 'suspiciousCount'));
    }

    public function reviewsApprove($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'approved']);
        return back()->with('success', '🎉 Phê duyệt đánh giá của khách hàng thành công!');
    }

    public function reviewsReply(Request $request, $id)
    {
        $request->validate(['admin_reply' => 'required|string']);
        $review = Review::findOrFail($id);
        $review->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'approved' 
        ]);
        return back()->with('success', '🎉 Gửi phản hồi đến khách hàng thành công!');
    }

    public function reviewsViolate($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'violated']);
        return back()->with('success', '🛡️ Đã đánh dấu đánh giá này là Vi phạm / Spam.');
    }

    public function reviewsDestroy($id)
    {
        Review::findOrFail($id)->delete();
        return back()->with('success', '🗑️ Đã xóa vĩnh viễn đánh giá khỏi hệ thống!');
    }

    public function destroyAllViolated() {
    Review::where('status', 'violated')->delete();
    return redirect()->back()->with('success', 'Đã dọn dẹp sạch sẽ toàn bộ bình luận vi phạm!');
    }

    public function approveAllPending()
    {
        // Quét sạch những ông đang pending và chuyển thành approved
        Review::where('status', 'pending')->update(['status' => 'approved']);
        return back()->with('success', '🎉 Đã phê duyệt toàn bộ bình luận đang chờ duyệt thành công!');
    }

    public function approveAllSuspicious()
    {
        // Quét sạch những ông đang bị AI nghi vấn và chuyển thành approved
        Review::where('status', 'suspicious')->update(['status' => 'approved']);
        return back()->with('success', '🎉 Đã phê duyệt toàn bộ bình luận nghi vấn thành công!');
    }

    /* =========================================================================
     * 7. XUẤT BÁO CÁO ĐƠN HÀNG (CSV)
     * ========================================================================= */
    public function exportOrders()
    {
        $orders = Order::with('user')->orderBy('id', 'desc')->get();

        $filename = "report-orders-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Mã Đơn Hàng', 'Khách Hàng', 'Email', 'Số Điện Thoại', 'Địa Chỉ Giao Hàng', 'Tổng Tiền (VND)', 'Phương Thức Thanh Toán', 'Trạng Thái', 'Ngày Tạo'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM to display Vietnamese characters correctly in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row['id']             = 'INK-' . $order->id;
                $row['customer']       = $order->user ? $order->user->name : 'Khách vãng lai';
                $row['email']          = $order->user ? $order->user->email : '';
                $row['phone']          = $order->phone;
                $row['address']        = $order->shipping_address;
                $row['total']          = number_format($order->total_price, 0, ',', '') . 'đ';
                $row['payment_method'] = $order->payment_method === 'credit' ? 'Thẻ tín dụng' : 'COD';
                $row['status']         = $order->status === 'completed' ? 'Hoàn thành' : ($order->status === 'shipping' ? 'Đang giao' : 'Đang xử lý');
                $row['created_at']     = $order->created_at->format('Y-m-d H:i:s');

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}