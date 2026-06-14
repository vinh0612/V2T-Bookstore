<div class="group relative flex flex-col bg-white p-4 rounded-lg shadow-sm border border-gray-150 hover:shadow-md transition">
    
    <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST" class="absolute top-6 right-6 z-10">
        @csrf
        <button type="submit" class="w-8 h-8 bg-white hover:bg-red-50 rounded-full flex items-center justify-center shadow-sm border border-gray-100 transition focus:outline-none cursor-pointer" title="Yêu thích">
            @if(in_array($book->id, session('wishlist', [])))
                <svg class="w-5 h-5 text-red-500 fill-current" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-gray-400 hover:text-red-500 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            @endif
        </button>
    </form>

    <a href="{{ route('books.show', $book->id) }}" class="relative h-64 mb-4 overflow-hidden rounded bg-gray-50 block">
        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
    </a>
    
    <div class="flex items-center justify-between gap-2 mb-2 text-[10px] font-bold tracking-wider uppercase">
        <span @style(['color' => $tagColor ?? '#047857'])>{{ $tag ?? 'SÁCH' }}</span>

        @if(isset($book->reviews_avg_rating))
            <span class="text-amber-500 flex items-center gap-0.5" style="color: #f59e0b; font-size: 11px;">★ {{ number_format($book->reviews_avg_rating, 1) }}</span>
        @else
            <span style="color: #9ca3af; font-size: 9px; text-transform: none; font-weight: 500; font-family: sans-serif;">Chưa có đánh giá</span>
        @endif
    </div>
    
    <h3 class="font-serif font-bold text-lg text-gray-900 leading-tight mb-1 hover:text-[var(--color-v2t-green)] transition">
        <a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a>
    </h3>
    
    <p class="text-sm text-gray-650 italic mb-4">bởi {{ $book->author }}</p>
    
    <div class="mt-auto flex items-center justify-between">
        <span class="font-bold text-[var(--color-v2t-green)]">{{ number_format($book->price, 0, ',', '.') }}đ</span>
        
        <button type="button" 
                onclick="addToCart(event, {{ $book->id }})" 
                class="bg-[var(--color-v2t-bg)] text-[var(--color-v2t-green)] border border-[var(--color-v2t-green)] hover:bg-[var(--color-v2t-green)] hover:text-white text-xs px-4 py-2 rounded transition font-medium cursor-pointer flex items-center gap-1">
            Thêm
        </button>
    </div>
</div>