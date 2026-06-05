<?php

use Illuminate\Support\Facades\Route;
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

// 1. Công khai
Route::get('/', [UserHomeController::class, 'index'])->name('home');
Route::get('/home', [UserHomeController::class, 'index']);
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/book/{id}', [ShopController::class, 'show'])->name('books.show');

// Luồng Đăng nhập
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Luồng Đăng ký
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Luồng Đăng xuất
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
// 3. Nhóm yêu cầu Đăng nhập (User)
Route::middleware('auth')->group(function () {
    // Quản lý hồ sơ cá nhân
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    
    // Đổi mật khẩu độc lập
    Route::put('/profile/change-password', [ChangePasswordController::class, 'update'])->name('profile.password.update');
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
    // Coupon Routes
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');

    Route::post('/books/{bookId}/review', [UserReviewController::class, 'store'])->name('books.review.store');

    Route::get('/wishlist', [UserWishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{id}', [UserWishlistController::class, 'toggle'])->name('wishlist.toggle');

    // 4. Nhóm Quản trị (Admin)
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/export-orders', [AdminController::class, 'exportOrders'])->name('admin.export.orders');
        
        // Quản lý sách
        Route::get('/books', [AdminController::class, 'booksIndex'])->name('admin.books.index');
        Route::get('/books/create', [AdminController::class, 'booksCreate'])->name('admin.books.create');
        Route::post('/books/store', [AdminController::class, 'booksStore'])->name('admin.books.store');
        Route::get('/books/{id}/edit', [AdminController::class, 'booksEdit'])->name('admin.books.edit');
        Route::put('/books/{id}', [AdminController::class, 'booksUpdate'])->name('admin.books.update');
        Route::delete('/books/{id}', [AdminController::class, 'booksDestroy'])->name('admin.books.destroy');

        // Đơn hàng
        Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('admin.orders.index');
        Route::post('/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');

        // Nhà cung cấp
        Route::get('/suppliers', [AdminController::class, 'suppliersIndex'])->name('admin.suppliers.index');

        // Quản lý người dùng
        Route::get('/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggleStatus');
        Route::get('/users/{id}', [AdminController::class, 'usersShow'])->name('admin.users.show');

        // Quản lý nhà cung cấp (Đầy đủ chức năng)
        Route::get('/suppliers', [AdminController::class, 'suppliersIndex'])->name('admin.suppliers.index');
        Route::get('/suppliers/create', [AdminController::class, 'suppliersCreate'])->name('admin.suppliers.create');
        Route::post('/suppliers/store', [AdminController::class, 'suppliersStore'])->name('admin.suppliers.store');
        Route::get('/suppliers/{id}/edit', [AdminController::class, 'suppliersEdit'])->name('admin.suppliers.edit');
        Route::put('/suppliers/{id}', [AdminController::class, 'suppliersUpdate'])->name('admin.suppliers.update');
        Route::delete('/suppliers/{id}', [AdminController::class, 'suppliersDestroy'])->name('admin.suppliers.destroy');

        // Nghiệp vụ Quản lý đánh giá sách
        Route::get('/reviews', [AdminController::class, 'reviewsIndex'])->name('admin.reviews.index');
        Route::post('/reviews/{id}/approve', [AdminController::class, 'reviewsApprove'])->name('admin.reviews.approve');
        Route::post('/reviews/{id}/reply', [AdminController::class, 'reviewsReply'])->name('admin.reviews.reply');
        Route::post('/reviews/{id}/violate', [AdminController::class, 'reviewsViolate'])->name('admin.reviews.violate');
        Route::delete('/reviews/{id}', [AdminController::class, 'reviewsDestroy'])->name('admin.reviews.destroy');

        Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('admin.vouchers.index');
        Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('admin.vouchers.store');
        Route::put('/vouchers/{id}', [AdminVoucherController::class, 'update'])->name('admin.vouchers.update');
        Route::delete('/vouchers/{id}', [AdminVoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
        Route::post('/vouchers/{id}/toggle-status', [AdminVoucherController::class, 'toggleStatus'])->name('admin.vouchers.toggleStatus');
    });
});