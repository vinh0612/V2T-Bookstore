@extends('layouts.app')

@section('title', 'Khám phá bộ sưu tập sách - V2T Bookstore')

@section('content')
<style>
    .v2t-shop-container {
        background-color: #f4f1ea;
        min-height: 100vh;
        margin-left: -1rem;
        margin-right: -1rem;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-top: 2.5rem;
        padding-bottom: 2.5rem;
    }
    .v2t-shop-layout {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }
    .v2t-sidebar {
        width: 100%;
        background-color: rgba(255, 255, 255, 0.5);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
    }
    .v2t-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #d1d5db;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    .v2t-sidebar-section {
        margin-bottom: 24px;
    }
    .v2t-sidebar-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .v2t-filter-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: white;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .v2t-filter-label {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.875rem;
        cursor: pointer;
        color: #374151;
    }
    .v2t-main-grid {
        width: 100%;
    }
    .v2t-shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #d1d5db;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }
    .v2t-sort-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: #4b5563;
    }
    .v2t-sort-select {
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        font-weight: 600;
        cursor: pointer;
        color: #1f2937;
    }
    .v2t-book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 24px;
        width: 100%;
    }
    .v2t-book-card {
        background: white;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 410px;
    }
    .v2t-img-link {
        aspect-ratio: 2 / 3;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f9fafb;
        border-radius: 8px;
        border: 1px solid #f3f4f6;
        margin-bottom: 16px;
        padding: 12px;
        overflow: hidden;
    }
    .v2t-book-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }
    .v2t-meta-bar {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    .v2t-title-heading {
        font-family: Georgia, Cambria, serif;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.35;
        margin: 0 0 6px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-clamp: 2; /* Thêm thuộc tính chuẩn để triệt tiêu hoàn toàn cảnh báo vàng linter */
        overflow: hidden;
        min-height: 2.7em;
    }
    .v2t-title-heading a {
        text-decoration: none;
        color: inherit;
    }
    .v2t-author-text {
        font-size: 0.75rem;
        color: #9ca3af;
        font-style: italic;
        margin: 0 0 16px 0;
    }
    .v2t-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f3f4f6;
        padding-top: 12px;
        margin-top: auto;
    }
    .v2t-price-text {
        font-family: Georgia, Cambria, serif;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }
    .v2t-btn-submit {
        background-color: #1e3e36;
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .v2t-btn-disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        cursor: not-allowed;
    }
    .v2t-empty-container {
        background: white;
        padding: 48px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        text-align: center;
    }
    .v2t-empty-link {
        display: inline-block;
        margin-top: 16px;
        background-color: #1e3e36;
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
    }

    /* ĐỊNH VỊ ĐÁP ỨNG CHO MÀN HÌNH MÁY TÍNH */
    @media (min-width: 768px) {
        .v2t-shop-layout {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 32px;
            align-items: start;
        }
        .v2t-sidebar {
            position: sticky;
            top: 96px;
        }
        .v2t-book-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
</style>

<div class="v2t-shop-container">
    <div class="container mx-auto max-w-7xl">
        <div class="v2t-shop-layout">
            
            <div class="v2t-sidebar shadow-sm">
                <div class="v2t-sidebar-header">
                    <h2 class="font-serif font-bold text-gray-900 tracking-wide style-title-sidebar" style="font-size: 1.25rem; font-weight: 700; margin: 0;">Bộ lọc sách</h2>
                    @if(request('category') || request('search') || request('author'))
                        <a href="{{ route('shop.index') }}" class="text-xs text-amber-800 hover:underline font-semibold" style="color: #78350f; font-size: 0.75rem;">Xóa bộ lọc</a>
                    @endif
                </div>

                <div class="v2t-sidebar-section">
                    <h3 class="v2t-sidebar-title">Danh mục sách</h3>
                    <div class="v2t-filter-box">
                        <label class="v2t-filter-label">
                            <input type="radio" name="filter_cat" data-url="{{ route('shop.index', request()->only(['search', 'author', 'sort'])) }}" {{ !request('category') ? 'checked' : '' }} class="navigation-filter-item w-4 h-4 text-emerald-700 focus:ring-emerald-600 border-gray-300">
                            <span>📚 Tất cả danh mục</span>
                        </label>

                        @foreach($categories as $cat)
                            <label class="v2t-filter-label">
                                <input type="radio" name="filter_cat" data-url="{{ route('shop.index', array_merge(request()->only(['search', 'author', 'sort']), ['category' => $cat->id])) }}" {{ request('category') == $cat->id ? 'checked' : '' }} class="navigation-filter-item w-4 h-4 text-emerald-700 focus:ring-emerald-600 border-gray-300">
                                <span>📖 {{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="v2t-sidebar-section">
                    <h3 class="v2t-sidebar-title">Tác giả nổi bật</h3>
                    <div class="v2t-filter-box">
                        <label class="v2t-filter-label">
                            <input type="radio" name="filter_author" data-url="{{ route('shop.index', request()->only(['search', 'category', 'sort'])) }}" {{ !request('author') ? 'checked' : '' }} class="navigation-filter-item w-4 h-4 text-emerald-700 focus:ring-emerald-600 border-gray-300">
                            <span>👥 Tất cả tác giả</span>
                        </label>

                        @foreach($authors as $auth)
                            <label class="v2t-filter-label">
                                <input type="radio" name="filter_author" data-url="{{ route('shop.index', array_merge(request()->only(['search', 'category', 'sort']), ['author' => $auth])) }}" {{ request('author') == $auth ? 'checked' : '' }} class="navigation-filter-item w-4 h-4 text-emerald-700 focus:ring-emerald-600 border-gray-300">
                                <span>✍️ {{ $auth }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="v2t-main-content">
                <div class="v2t-shop-header">
                    <div>
                        <h2 class="text-2xl font-serif font-bold text-gray-900 tracking-tight" style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">Khám Phá Bộ Sưu Tập</h2>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 4px 0 0 0;">
                            Hiển thị {{ $books->count() }} kết quả 
                            @if(request('search')) cho từ khóa "{{ request('search') }}" @endif
                        </p>
                    </div>
                    
                    <div class="v2t-sort-wrapper">
                        <span>Sắp xếp:</span>
                        <select onchange="location = this.value;" class="v2t-sort-select">
                            <option value="{{ route('shop.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Sách mới nhất</option>
                            <option value="{{ route('shop.index', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                            <option value="{{ route('shop.index', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                @if($books->count() > 0)
                    <div class="v2t-book-grid">
                        @foreach($books as $index => $book)
                            <div class="v2t-book-card" style="position: relative;">
                                <!-- Nút Yêu thích (Wishlist Heart Icon) -->
                                <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST" style="position: absolute; top: 24px; right: 24px; z-index: 10; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 32px; height: 32px; background: white; border: 1px solid #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); cursor: pointer; color: #ef4444; outline: none;" title="Yêu thích">
                                        @if(in_array($book->id, session('wishlist', [])))
                                            <svg style="width: 20px; height: 20px; fill: currentColor;" viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        @else
                                            <svg style="width: 20px; height: 20px; fill: none; stroke: #9ca3af; stroke-width: 2;" viewBox="0 0 24 24">
                                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                <div>
                                    <a href="{{ route('books.show', $book->id) }}" class="v2t-img-link">
                                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" onerror="this.onerror=null; this.src='https://placehold.co/400x600/1e3e36/ffffff?text=V2T+Bookstore';" class="v2t-book-img">
                                    </a>
                                    
                                    <div class="v2t-meta-bar">
                                        @if($book->stock > 15)
                                            <span class="v2t-tag-banchay">BÁN CHẠY</span>
                                        @else
                                            <span class="v2t-tag-sachmoi">SÁCH MỚI</span>
                                        @endif
                                        @if($book->reviews_avg_rating)
                                            <span class="v2t-rating" style="color: #f59e0b;">★ {{ number_format($book->reviews_avg_rating, 1) }}</span>
                                        @else
                                            <span class="v2t-rating" style="color: #9ca3af; font-size: 9px; text-transform: none; font-weight: 500; font-family: sans-serif;">Chưa có đánh giá</span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="v2t-book-title">
                                        <a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a>
                                    </h3>
                                    <p class="v2t-author-text">bởi {{ $book->author }}</p>
                                </div>
                                
                                <div class="v2t-card-footer">
                                    <span class="v2t-book-price">
                                        {{ number_format($book->price, 0, ',', '.') }}đ
                                    </span>
                                    
                                    <form action="{{ route('cart.add', $book->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @if($book->stock > 0)
                                            <button type="submit" class="v2t-btn-submit">
                                                Thêm vào giỏ
                                            </button>
                                        @else
                                            <button type="button" disabled class="v2t-btn-disabled">
                                                Hết hàng
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="display: flex; justify-content: center; padding-top: 32px;">
                        {{ $books->links() }}
                    </div>
                @else
                    <div class="v2t-empty-container">
                        <span style="font-size: 2.5rem;">📚</span>
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-top: 16px;">Không tìm thấy sách phù hợp</h3>
                        <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 4px; max-width: 384px; margin-left: auto; margin-right: auto;">Vui lòng thử tìm kiếm bằng một từ khóa hoặc bộ lọc khác xem sao nhé!</p>
                        <a href="{{ route('shop.index') }}" class="v2t-empty-link">Quay lại cửa hàng</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.navigation-filter-item').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const targetUrl = this.getAttribute('data-url');
                if (targetUrl) { window.location.href = targetUrl; }
            }
        });
    });
</script>
@endsection