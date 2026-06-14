@extends('layouts.app')

@section('title', 'Sách Yêu Thích - V2T Bookstore')

@section('content')
<div class="py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-[var(--color-v2t-text)]">Sách Yêu Thích Của Bạn</h1>
        <p class="text-sm text-gray-500 mt-1">Lưu trữ những tác phẩm bạn muốn sở hữu hoặc đọc sau này.</p>
    </div>

    @if($books->isEmpty())
        <div class="bg-white rounded-xl border border-gray-150 p-12 text-center shadow-sm max-w-xl mx-auto my-12">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Danh sách yêu thích đang trống</h3>
            <p class="text-gray-500 text-sm mb-8">Hãy duyệt qua hàng ngàn tựa sách hấp dẫn tại cửa hàng và lưu lại cuốn sách bạn yêu thích nhé.</p>
            <a href="/shop" class="bg-[var(--color-v2t-green)] text-white hover:bg-[var(--color-v2t-green-hover)] px-6 py-3 rounded font-medium transition inline-block">
                Khám phá Cửa hàng
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="group relative flex flex-col bg-white p-4 rounded-lg shadow-sm border border-gray-150 hover:shadow-md transition">
                    
                    <!-- Nút xóa nhanh khỏi wishlist -->
                    <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST" class="absolute top-6 right-6 z-10">
                        @csrf
                        <button type="submit" class="w-8 h-8 bg-white hover:bg-red-50 text-red-500 rounded-full flex items-center justify-center shadow-sm border border-gray-100 transition focus:outline-none" title="Xóa khỏi danh sách yêu thích">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                    </form>

                    <a href="{{ route('books.show', $book->id) }}" class="relative h-64 mb-4 overflow-hidden rounded bg-gray-50 block">
                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
                    </a>
                    
                    <div class="flex items-center justify-between gap-2 mb-2 text-[10px] font-bold tracking-wider uppercase">
                        <span style="color: #047857;">{{ $book->category->name ?? 'SÁCH' }}</span>

                        @if($book->reviews_avg_rating)
                            <span class="text-amber-500 flex items-center gap-0.5" style="color: #f59e0b; font-size: 11px;">★ {{ number_format($book->reviews_avg_rating, 1) }}</span>
                        @else
                            <span style="color: #9ca3af; font-size: 9px; text-transform: none; font-weight: 500; font-family: sans-serif;">Chưa có đánh giá</span>
                        @endif
                    </div>
                    
                    <h3 class="font-serif font-bold text-lg text-gray-900 leading-tight mb-1 hover:text-[var(--color-v2t-green)] transition">
                        <a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a>
                    </h3>
                    
                    <p class="text-sm text-gray-600 italic mb-4">bởi {{ $book->author }}</p>
                    
                    <div class="mt-auto flex items-center justify-between">
                        <span class="font-bold text-[var(--color-v2t-green)]">{{ number_format($book->price, 0, ',', '.') }}đ</span>
                        
                        <form action="{{ route('cart.add', $book->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-[var(--color-v2t-green)] text-white hover:bg-[var(--color-v2t-green-hover)] text-xs px-4 py-2 rounded transition font-medium">
                                Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
