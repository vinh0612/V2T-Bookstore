<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // <-- THÊM ĐOẠN NÀY: Ép Laravel dùng link HTTPS khi chạy trên mạng (Render)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Chia sẻ biến $categories cho tất cả các file trong folder partials
        View::composer('partials.header', function ($view) {
            $megamenuCategories = Category::whereNull('parent_id') // Lấy danh mục gốc (Cha lớn)
                ->with('children.children')                    // Nạp trước danh mục Con và Cháu
                ->get();
                
            $view->with('megamenuCategories', $megamenuCategories);
        });
    }
}