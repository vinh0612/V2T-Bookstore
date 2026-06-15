<?php
//IMPORT THƯ VIỆN & CONTROLLERS
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Import Controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\UserReviewController;
use App\Http\Controllers\UserWishlistController;
use App\Http\Controllers\AdminVoucherController;

// Import Thư viện Google API
use Google\Client as GoogleClient;
use Google\Service\Gmail as GoogleGmail;


// 1: Công khai
Route::get('/', [UserHomeController::class, 'index'])->name('home');
Route::get('/home', [UserHomeController::class, 'index']);
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/book/{id}', [ShopController::class, 'show'])->name('books.show');

// Luồng Xác thực (Đăng nhập / Đăng ký / Đăng xuất)
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register/send-otp', [RegisterController::class, 'sendOtp'])->name('register.send_otp');
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.verify_otp');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');


// 2: Yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    // 2.1. PHÂN HỆ KHÁCH HÀNG (Middleware: customer)
    Route::middleware('customer')->group(function () {
        // Quản lý hồ sơ cá nhân
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
        Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/change-password', [ChangePasswordController::class, 'update'])->name('profile.password.update');
        
        // Quản lý đơn hàng cá nhân
        Route::put('/orders/{id}/cancel', [UserProfileController::class, 'cancel'])->name('orders.cancel');
        Route::put('/orders/{id}/complete', [UserProfileController::class, 'completeOrder'])->name('orders.complete');
        Route::get('/orders/{id}/details', [UserProfileController::class, 'showOrder'])->name('orders.show');

        // Giỏ hàng & Thanh toán
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
        
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('cart.checkout');
        Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('cart.placeOrder');
        Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('coupon.apply');
        Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
        Route::get('/checkout/momo-return', [CheckoutController::class, 'momoReturn'])->name('momo.return');

        // Tương tác sách (Đánh giá, Yêu thích)
        Route::post('/books/{bookId}/review', [UserReviewController::class, 'store'])->name('books.review.store');
        Route::get('/wishlist', [UserWishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/toggle/{id}', [UserWishlistController::class, 'toggle'])->name('wishlist.toggle');
    });

    // 2.2. PHÂN HỆ QUẢN TRỊ (Middleware: admin)
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/export-orders', [AdminController::class, 'exportOrders'])->name('admin.export.orders');

        // Quản lý Sách
        Route::get('/books', [AdminController::class, 'booksIndex'])->name('admin.books.index');
        Route::get('/books/create', [AdminController::class, 'booksCreate'])->name('admin.books.create');
        Route::post('/books/store', [AdminController::class, 'booksStore'])->name('admin.books.store');
        Route::get('/books/{id}/edit', [AdminController::class, 'booksEdit'])->name('admin.books.edit');
        Route::put('/books/{id}', [AdminController::class, 'booksUpdate'])->name('admin.books.update');
        Route::delete('/books/{id}', [AdminController::class, 'booksDestroy'])->name('admin.books.destroy');

        // Quản lý Đơn hàng
        Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('admin.orders.index');
        Route::get('/orders/{id}', [AdminController::class, 'ordersShow'])->name('admin.orders.show');
        Route::post('/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');

        // Quản lý Người dùng
        Route::get('/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggleStatus');
        Route::get('/users/{id}', [AdminController::class, 'usersShow'])->name('admin.users.show');
        Route::delete('/users/{id}', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');

        // Quản lý Nhà cung cấp
        Route::get('/suppliers', [AdminController::class, 'suppliersIndex'])->name('admin.suppliers.index');
        Route::get('/suppliers/create', [AdminController::class, 'suppliersCreate'])->name('admin.suppliers.create');
        Route::post('/suppliers/store', [AdminController::class, 'suppliersStore'])->name('admin.suppliers.store');
        Route::get('/suppliers/{id}/edit', [AdminController::class, 'suppliersEdit'])->name('admin.suppliers.edit');
        Route::put('/suppliers/{id}', [AdminController::class, 'suppliersUpdate'])->name('admin.suppliers.update');
        Route::delete('/suppliers/{id}', [AdminController::class, 'suppliersDestroy'])->name('admin.suppliers.destroy');
        Route::get('/suppliers/{id}/import', [AdminController::class, 'suppliersImport'])->name('admin.suppliers.import');
        Route::post('/suppliers/{id}/import', [AdminController::class, 'suppliersImportStore'])->name('admin.suppliers.import.store');

        // Quản lý Đánh giá sách
        Route::get('/reviews', [AdminController::class, 'reviewsIndex'])->name('admin.reviews.index');
        Route::post('/reviews/{id}/approve', [AdminController::class, 'reviewsApprove'])->name('admin.reviews.approve');
        Route::post('/reviews/{id}/reply', [AdminController::class, 'reviewsReply'])->name('admin.reviews.reply');
        Route::post('/reviews/{id}/violate', [AdminController::class, 'reviewsViolate'])->name('admin.reviews.violate');
        Route::delete('/reviews/{id}', [AdminController::class, 'reviewsDestroy'])->name('admin.reviews.destroy');
        Route::delete('/admin/reviews/destroy-all-violated', [AdminController::class, 'destroyAllViolated'])->name('admin.reviews.destroyAllViolated');
        Route::post('/admin/reviews/approve-all-pending', [AdminController::class, 'approveAllPending'])->name('admin.reviews.approveAllPending');
        Route::post('/admin/reviews/approve-all-suspicious', [AdminController::class, 'approveAllSuspicious'])->name('admin.reviews.approveAllSuspicious');

        // Quản lý Voucher
        Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('admin.vouchers.index');
        Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('admin.vouchers.store');
        Route::put('/vouchers/{id}', [AdminVoucherController::class, 'update'])->name('admin.vouchers.update');
        Route::delete('/vouchers/{id}', [AdminVoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
        Route::post('/vouchers/{id}/toggle-status', [AdminVoucherController::class, 'toggleStatus'])->name('admin.vouchers.toggleStatus');
    });
});


// KHU VỰC 3: TOOL GOOGLE GMAIL API (Lấy Refresh Token)
Route::get('/gmail/auth', function () {
    $client = new GoogleClient();
    $client->setClientId(env('GMAIL_CLIENT_ID'));
    $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
    $client->setRedirectUri(env('GMAIL_REDIRECT_URI'));
    // Xin quyền chỉ để gửi mail
    $client->addScope(GoogleGmail::GMAIL_SEND);
    // Bắt buộc phải có dòng này để Google nhả Refresh Token
    $client->setAccessType('offline'); 
    $client->setApprovalPrompt('force');

    return redirect($client->createAuthUrl());
});

Route::get('/gmail/callback', function (Request $request) {
    $client = new GoogleClient();
    $client->setClientId(env('GMAIL_CLIENT_ID'));
    $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
    $client->setRedirectUri(env('GMAIL_REDIRECT_URI'));

    if ($request->has('code')) {
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        if (isset($token['refresh_token'])) {
            return response()->json([
                'CHÚC MỪNG BRO, ĐÂY LÀ MÃ REFRESH TOKEN VĨNH VIỄN CỦA BRO (COPY CẤT ĐI NGAY):' => $token['refresh_token']
            ]);
        }
        return response()->json(['Lỗi' => 'Không lấy được Refresh Token. Đọc kỹ lại các bước nhé!', 'Chi tiết' => $token]);
    }
    return 'Thiếu code xác thực từ Google!';
});


// KHU VỰC 4: DEV TOOLS (Các công cụ hỗ trợ gỡ lỗi)
// Dọn rác cấu hình Render
Route::get('/clear-hacker', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'Đã dọn sạch sẽ rác cấu hình cũ trên Render! Bro test gửi mail đi!';
});

// Soi biến môi trường
Route::get('/soi-cau-hinh', function () {
    return response()->json([
        'KIEU_XEP_HANG_QUEUE' => config('queue.default'),
        'CACH_GUI_MAIL' => config('mail.default'),
        'MAY_CHU_MAIL' => config('mail.mailers.smtp.host'),
        'TAI_KHOAN_MAIL' => config('mail.mailers.smtp.username'),
        'DO_DAI_MAT_KHAU' => strlen((string) config('mail.mailers.smtp.password')),
    ]);
});

// Xem Log hệ thống
Route::get('/xem-log', function () {
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        return response(file_get_contents($logPath), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }
    return 'Web sạch bong, không có lỗi nào được ghi lại!';
});

// Test kết nối AI (Gemini)
Route::get('/kiem-tra-ai', function () {
    $response = Http::withoutVerifying()
        ->get('https://generativelanguage.googleapis.com/v1beta/models?key=' . env('GEMINI_API_KEY'));
    return $response->json();
});