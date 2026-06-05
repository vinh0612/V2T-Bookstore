@extends('layouts.app')

@section('title', 'V2T Bookstore - Thiên Đường Sách Mơ Ước')

@section('content')
<div class="relative bg-slate-900 rounded-2xl overflow-hidden mb-16 shadow-md min-h-[440px] md:min-h-[480px] flex items-center mx-4 md:mx-0">
    
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=2000&q=80" 
             alt="Thư viện sách" 
             class="w-full h-full object-cover object-center opacity-30"> <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(17, 24, 39, 0.95) 0%, rgba(17, 24, 39, 0.85) 55%, rgba(17, 24, 39, 0.3) 100%);"></div>
    </div>
    
    <div class="relative w-full md:w-2/3 lg:w-1/2 px-6 sm:px-12 md:px-16 py-12 z-10 flex flex-col justify-center">
        <div class="max-w-[520px]">
            
            <div class="mb-4">
                <span class="inline-block py-1 px-3 rounded bg-orange-600 text-white text-xs md:text-sm font-bold tracking-wider uppercase shadow-sm">
                    Tri thức là sức mạnh
                </span>
            </div>
            
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-white mb-5 leading-tight tracking-tight">
                Khơi nguồn cảm hứng & mở lối tương lai
            </h1>
            
            <p class="text-sm sm:text-base text-white mb-8 font-normal leading-relaxed drop-shadow-sm">
                Chào mừng bạn đến với V2T Bookstore - nơi hội tụ những tinh hoa tri thức nhân loại. Hãy đắm chìm vào không gian văn học sâu sắc, tìm kiếm câu trả lời cho tương lai và lưu trữ những tác phẩm yêu thích của bạn ngay hôm nay.
            </p>
            
            <div class="flex flex-wrap gap-4">
                <a href="/shop" class="inline-block bg-[var(--color-v2t-green)] text-white font-bold px-7 py-3 rounded-lg text-xs shadow-md hover:brightness-110 transition duration-200 uppercase tracking-wider">
                    Mua Ngay
                </a>
                <a href="#featured-section" class="inline-block bg-transparent text-white border border-white/40 font-bold px-7 py-3 rounded-lg text-xs hover:bg-white/10 transition duration-200 uppercase tracking-wider">
                    Khám Phá Sách Nổi Bật
                </a>
            </div>
            
        </div>
    </div>
</div>

<div id="featured-section" class="mb-16">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-serif font-bold text-[var(--color-v2t-text)]">Sách Nổi Bật</h2>
            <p class="text-sm text-gray-500 mt-1">Được tuyển chọn kỹ lưỡng với đánh giá xuất sắc nhất từ độc giả.</p>
        </div>
        <a href="/shop" class="text-sm font-semibold text-[var(--color-v2t-green)] hover:underline flex items-center gap-1">Xem tất cả <span class="text-lg">&rarr;</span></a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($featuredBooks as $book)
            @include('partials.book_card', ['book' => $book, 'tag' => 'NỔI BẬT', 'tagColor' => '#1e3e36'])
        @endforeach
    </div>
</div>

<div class="mb-16">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-serif font-bold text-[var(--color-v2t-text)]">Sách Bán Chạy</h2>
            <p class="text-sm text-gray-500 mt-1">Những tựa sách đang dẫn đầu xu hướng mua sắm tuần này.</p>
        </div>
        <a href="/shop" class="text-sm font-semibold text-[var(--color-v2t-green)] hover:underline flex items-center gap-1">Xem tất cả <span class="text-lg">&rarr;</span></a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($bestSellers as $book)
            @include('partials.book_card', ['book' => $book, 'tag' => 'BÁN CHẠY', 'tagColor' => '#b45309'])
        @endforeach
    </div>
</div>

<div class="mb-20">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-serif font-bold text-[var(--color-v2t-text)]">Sách Mới Phát Hành</h2>
            <p class="text-sm text-gray-500 mt-1">Những tác phẩm mới lên kệ, đón đầu xu hướng tri thức.</p>
        </div>
        <a href="/shop" class="text-sm font-semibold text-[var(--color-v2t-green)] hover:underline flex items-center gap-1">Xem tất cả <span class="text-lg">&rarr;</span></a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($newBooks as $book)
            @include('partials.book_card', ['book' => $book, 'tag' => 'SÁCH MỚI', 'tagColor' => '#047857'])
        @endforeach
    </div>
</div>

<div class="bg-gray-50 rounded-2xl p-10 md:p-16 mb-20 border border-gray-100 shadow-sm">
    <div class="text-center max-w-xl mx-auto mb-12">
        <span class="text-xs font-bold text-[var(--color-v2t-green)] uppercase tracking-wider">Cảm Nhận Độc Giả</span>
        <h2 class="text-3xl font-serif font-bold text-gray-900 mt-2">Độc giả nói gì về V2T Bookstore</h2>
        <p class="text-sm text-gray-500 mt-2">Những chia sẻ chân thật nhất của khách hàng sau khi trải nghiệm sản phẩm và dịch vụ của chúng tôi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($customerReviews as $review)
            <div class="bg-white p-6 rounded-xl border border-gray-150 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-0.5 text-amber-500 mb-4" style="color: #f59e0b;">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-gray-700 italic text-sm mb-6 leading-relaxed">
                        "{{ Str::limit($review->comment, 150) }}"
                    </p>
                </div>
                <div class="flex items-center gap-3 border-t border-gray-100 pt-4 mt-auto">
                    <div class="w-10 h-10 bg-[var(--color-v2t-green)] text-white rounded-full flex items-center justify-center font-bold text-sm uppercase">
                        {{ substr($review->user->name ?? 'K', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-gray-900">{{ $review->user->name ?? 'Khách ẩn danh' }}</h4>
                        <span class="text-[11px] text-gray-400">Độc giả của cuốn <a href="{{ route('books.show', $review->book_id) }}" class="underline text-[var(--color-v2t-green)]">{{ $review->book->title ?? '' }}</a></span>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-xl border border-gray-150 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-0.5 text-amber-500 mb-4" style="color: #f59e0b;">
                        <span>★★★★★</span>
                    </div>
                    <p class="text-gray-700 italic text-sm mb-6 leading-relaxed">
                        "Tôi vô cùng ấn tượng với chất lượng sách và dịch vụ giao hàng siêu nhanh của tiệm. Sách được đóng gói rất cẩn thận, không móp méo chút nào!"
                    </p>
                </div>
                <div class="flex items-center gap-3 border-t border-gray-100 pt-4 mt-auto">
                    <div class="w-10 h-10 bg-amber-600 text-white rounded-full flex items-center justify-center font-bold text-sm">N</div>
                    <div>
                        <h4 class="font-bold text-sm text-gray-900">Nguyễn Văn Minh</h4>
                        <span class="text-[11px] text-gray-400">Thành viên lâu năm</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl border border-gray-150 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-0.5 text-amber-500 mb-4" style="color: #f59e0b;">
                        <span>★★★★★</span>
                    </div>
                    <p class="text-gray-700 italic text-sm mb-6 leading-relaxed">
                        "Tính năng sách yêu thích thực sự rất hữu ích. Tôi có thể lưu lại những đầu sách hay muốn đọc dần và áp mã giảm giá trực tiếp cực tiện lợi!"
                    </p>
                </div>
                <div class="flex items-center gap-3 border-t border-gray-100 pt-4 mt-auto">
                    <div class="w-10 h-10 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold text-sm">H</div>
                    <div>
                        <h4 class="font-bold text-sm text-gray-900">Trần Thị Hoa</h4>
                        <span class="text-[11px] text-gray-400">Độc giả thường trực</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-150 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-0.5 text-amber-500 mb-4" style="color: #f59e0b;">
                        <span>★★★★☆</span>
                    </div>
                    <p class="text-gray-700 italic text-sm mb-6 leading-relaxed">
                        "Không gian trang web tinh tế, gọn gàng và tạo cảm giác rất muốn đọc sách. Đội ngũ hỗ trợ cũng nhiệt tình giải đáp mọi thắc mắc."
                    </p>
                </div>
                <div class="flex items-center gap-3 border-t border-gray-100 pt-4 mt-auto">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-sm">L</div>
                    <div>
                        <h4 class="font-bold text-sm text-gray-900">Lê Hoàng Long</h4>
                        <span class="text-[11px] text-gray-400">Người yêu văn học cổ điển</span>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<div class="mb-16">
    <div class="text-center max-w-xl mx-auto mb-12">
        <span class="text-xs font-bold text-[var(--color-v2t-green)] uppercase tracking-wider">Góc Độc Giả</span>
        <h2 class="text-3xl font-serif font-bold text-gray-900 mt-2">Blog & Tin Tức Sách</h2>
        <p class="text-sm text-gray-500 mt-2">Cập nhật xu hướng văn hóa đọc sách, review chi tiết các cuốn sách bán chạy và tin tức văn học mới nhất.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="bg-white rounded-xl border border-gray-150 overflow-hidden shadow-sm hover:shadow-md transition group">
            <div class="h-48 overflow-hidden bg-gray-50">
                <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Blog 1" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                    <span>📅 02/06/2026</span>
                    <span>•</span>
                    <span>⏱️ 5 phút đọc</span>
                </div>
                <h3 class="font-serif font-bold text-lg text-gray-900 mb-2 hover:text-[var(--color-v2t-green)] transition">
                    <a href="#">Xây dựng thói quen đọc sách 15 phút mỗi ngày</a>
                </h3>
                <p class="text-gray-500 text-sm mb-4 line-clamp-3">
                    Đọc sách không chỉ mang lại kiến thức mà còn giúp tâm trí thư giãn. Làm thế nào để duy trì thói quen đọc sách giữa guồng quay bận rộn? Khám phá 5 mẹo nhỏ ngay trong bài viết này.
                </p>
                <a href="#" class="text-[var(--color-v2t-green)] font-bold text-xs hover:underline flex items-center gap-1">Đọc Tiếp &rarr;</a>
            </div>
        </article>

        <article class="bg-white rounded-xl border border-gray-150 overflow-hidden shadow-sm hover:shadow-md transition group">
            <div class="h-48 overflow-hidden bg-gray-50">
                <img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Blog 2" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                    <span>📅 28/05/2026</span>
                    <span>•</span>
                    <span>⏱️ 7 phút đọc</span>
                </div>
                <h3 class="font-serif font-bold text-lg text-gray-900 mb-2 hover:text-[var(--color-v2t-green)] transition">
                    <a href="#">Top 5 tựa sách tâm lý thay đổi nhận thức của bạn</a>
                </h3>
                <p class="text-gray-500 text-sm mb-4 line-clamp-3">
                    Những cuốn sách tâm lý học sâu sắc sẽ giúp bạn khám phá thế giới nội tâm của bản thân và thấu hiểu những hành vi của mọi người xung quanh một cách khách quan hơn.
                </p>
                <a href="#" class="text-[var(--color-v2t-green)] font-bold text-xs hover:underline flex items-center gap-1">Đọc Tiếp &rarr;</a>
            </div>
        </article>

        <article class="bg-white rounded-xl border border-gray-150 overflow-hidden shadow-sm hover:shadow-md transition group">
            <div class="h-48 overflow-hidden bg-gray-50">
                <img src="https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Blog 3" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                    <span>📅 15/05/2026</span>
                    <span>•</span>
                    <span>⏱️ 4 phút đọc</span>
                </div>
                <h3 class="font-serif font-bold text-lg text-gray-900 mb-2 hover:text-[var(--color-v2t-green)] transition">
                    <a href="#">Góc nhìn văn học đương đại: Sách nói và Sách giấy</a>
                </h3>
                <p class="text-gray-500 text-sm mb-4 line-clamp-3">
                    Liệu sự bùng nổ của sách nói (Audiobook) có làm mất đi giá trị của sách giấy truyền thống? Hãy cùng lắng nghe những chia sẻ của nhà văn và độc giả về chủ đề nóng hổi này.
                </p>
                <a href="#" class="text-[var(--color-v2t-green)] font-bold text-xs hover:underline flex items-center gap-1">Đọc Tiếp &rarr;</a>
            </div>
        </article>
    </div>
</div>
@endsection