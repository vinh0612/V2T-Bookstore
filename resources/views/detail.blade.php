@extends('layouts.app')

@section('title', $book->title . ' - V2T Bookstore')

@section('content')
<style>
    .detail-hero { background: linear-gradient(135deg, #0a3622 0%, #1a5c3a 50%, #0d4a2b 100%); }
    .detail-img-wrap {
        background: white; border-radius: 16px; padding: 12px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
        transition: transform 0.5s ease;
    }
    .detail-img-wrap:hover { transform: translateY(-8px) scale(1.02); }
    .detail-img-wrap img { border-radius: 10px; width: 100%; height: 100%; object-fit: contain; }
    .detail-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .detail-info-card { background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; }
    .detail-info-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .detail-price { font-family: 'Playfair Display', serif; font-size: 2.25rem; font-weight: 800; color: #0a3622; }
    .detail-btn-cart {
        background: #0a3622; color: white; font-weight: 700; font-size: 0.875rem;
        padding: 14px 28px; border-radius: 12px; border: none; cursor: pointer;
        transition: all 0.2s; box-shadow: 0 4px 14px rgba(10,54,34,0.3);
    }
    .detail-btn-cart:hover { background: #062416; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(10,54,34,0.4); }
    .detail-btn-wish {
        width: 52px; height: 52px; border-radius: 12px; border: 2px solid #e5e7eb;
        background: white; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; color: #ef4444;
    }
    .detail-btn-wish:hover { background: #fef2f2; border-color: #fca5a5; }
    .detail-qty-wrap {
        display: flex; align-items: center; border: 2px solid #e5e7eb; border-radius: 12px;
        background: #f9fafb; overflow: hidden;
    }
    .detail-qty-wrap button { padding: 12px 16px; background: transparent; border: none; cursor: pointer; font-size: 1.1rem; font-weight: 700; color: #374151; }
    .detail-qty-wrap button:hover { background: #e5e7eb; }
    .detail-qty-wrap input {
        width: 50px; text-align: center; border: none; background: transparent;
        font-weight: 700; font-size: 1rem; color: #111827; outline: none;
    }
    .detail-section-title {
        font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: #111827;
        padding-left: 16px; border-left: 4px solid #0a3622; margin-bottom: 20px;
    }
    .detail-review-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #f3f4f6; }
    .detail-review-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .detail-avatar {
        width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0;
    }
    .detail-editor-note {
        background: linear-gradient(135deg, #0a3622, #1a5c3a);
        padding: 28px; border-radius: 16px; color: white;
        box-shadow: 0 8px 30px rgba(10,54,34,0.25);
    }
    .detail-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .detail-meta-item {
        background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 16px;
        text-align: center; transition: all 0.2s;
    }
    .detail-meta-item:hover { background: white; border-color: #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .stars-select { display: flex; gap: 4px; }
    .stars-select label { cursor: pointer; font-size: 1.5rem; color: #d1d5db; transition: color 0.15s; }
    .stars-select input { display: none; }
    .stars-select label:hover, .stars-select label:hover ~ label { color: #f59e0b; }
    .stars-select input:checked ~ label { color: #d1d5db; }
    .stars-select label.active { color: #f59e0b; }
</style>

{{-- ═══ HERO BANNER ═══ --}}
<div class="detail-hero" style="border-radius: 16px; overflow: hidden; margin-bottom: 40px;">
    <div style="display: grid; grid-template-columns: 1fr; gap: 32px; padding: 40px 32px; align-items: center;">

        {{-- Layout 2 cột trên desktop --}}
        <div style="display: flex; flex-wrap: wrap; gap: 40px; align-items: center;">

            {{-- Ảnh bìa --}}
            <div style="flex-shrink: 0; display: flex; justify-content: center; width: 240px; margin: 0 auto;">
                <div class="detail-img-wrap" style="width: 100%; aspect-ratio: 2/3;">
                    <img src="{{ $book->image_url }}"
                         alt="{{ $book->title }}"
                         onerror="this.onerror=null; this.src='https://placehold.co/400x600/0a3622/ffffff?text=V2T';">
                </div>
            </div>

            {{-- Thông tin chính --}}
            <div style="flex: 1; min-width: 280px; color: white;">
                {{-- Breadcrumb --}}
                <nav style="font-size: 12px; color: rgba(255,255,255,0.55); margin-bottom: 16px; font-weight: 500;">
                    <a href="/" style="color: rgba(255,255,255,0.55); text-decoration: none;">Trang chủ</a>
                    <span style="margin: 0 6px;">/</span>
                    <a href="/shop" style="color: rgba(255,255,255,0.55); text-decoration: none;">Cửa hàng</a>
                    <span style="margin: 0 6px;">/</span>
                    <span style="color: rgba(255,255,255,0.85);">{{ Str::limit($book->title, 40) }}</span>
                </nav>

                {{-- Tiêu đề --}}
                <h1 style="font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 800; margin: 0 0 8px 0; line-height: 1.3; color: white; word-break: break-word;">{{ $book->title }}</h1>

                {{-- Tác giả --}}
                <p style="font-size: 1rem; color: rgba(255,255,255,0.65); font-style: italic; margin: 0 0 20px 0;">bởi {{ $book->author }}</p>

                {{-- Badges --}}
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px;">
                    @if($book->reviews_avg_rating)
                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(245,158,11,0.2); color: #fbbf24;">
                            ★ {{ number_format($book->reviews_avg_rating, 1) }} / 5.0
                        </span>
                    @endif
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(255,255,255,0.15); color: white;">
                        {{ $book->stock > 0 ? '✓ Còn hàng ('.$book->stock.')' : '✗ Hết hàng' }}
                    </span>
                </div>

                {{-- Giá --}}
                <div style="margin: 0;">
                    <span style="font-family: 'Playfair Display', serif; font-size: 2.25rem; font-weight: 800; color: white;">{{ number_format($book->price, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>

{{-- ═══ KHU VỰC MUA HÀNG & THÔNG TIN ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
    {{-- Cột trái: Mua hàng + Mô tả --}}
    <div class="lg:col-span-2 space-y-8">

        {{-- Card mua hàng --}}
        <div class="detail-info-card" style="padding: 28px;">
            <h3 style="font-size: 0.75rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">Đặt mua ngay</h3>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <div class="detail-qty-wrap">
                    <button type="button" id="qty-minus">&minus;</button>
                    <input type="number" id="qty-input" value="1" min="1" max="{{ $book->stock }}" readonly>
                    <button type="button" id="qty-plus">&plus;</button>
                </div>

                <form action="{{ route('cart.add', $book->id) }}" method="POST" class="flex-1 flex gap-3" id="cart-form">
                    @csrf
                    <input type="hidden" name="quantity" id="qty-hidden" value="1">
                    @if($book->stock > 0)
                        <button type="submit" class="detail-btn-cart flex-1">🛒 Thêm vào giỏ hàng</button>
                    @else
                        <button type="button" disabled style="flex:1; padding: 14px 28px; border-radius: 12px; background: #f3f4f6; color: #9ca3af; font-weight: 700; border: none; cursor: not-allowed;">Tạm hết hàng</button>
                    @endif
                </form>

                <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="detail-btn-wish" title="Yêu thích">
                        @if(in_array($book->id, session('wishlist', [])))
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        @endif
                    </button>
                </form>
            </div>
        </div>

        {{-- Tóm tắt nội dung --}}
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: #111827; padding-left: 16px; border-left: 4px solid #0a3622; margin-bottom: 20px;">Tóm tắt nội dung</h2>
            <div class="detail-info-card">
                <p style="color: #4b5563; line-height: 1.8; font-size: 0.9375rem; white-space: pre-line;">{{ $book->description ?? 'Đang cập nhật nội dung mô tả cho cuốn sách này...' }}</p>
            </div>
        </div>

        {{-- Đánh giá --}}
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: #111827; padding-left: 16px; border-left: 4px solid #0a3622; margin-bottom: 20px;">Đánh giá từ độc giả ({{ $book->reviews->count() }})</h2>

            <div class="detail-info-card" style="margin-bottom: 16px;">
                @auth
                    @if($hasPurchased)
                        <form action="{{ route('books.review.store', $book->id) }}" method="POST">
                            @csrf
                            <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151; margin-bottom: 12px;">💬 Chia sẻ cảm nhận của bạn</h3>

                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #6b7280;">Đánh giá:</span>
                                <select name="rating" style="padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb; font-weight: 700; color: #f59e0b; cursor: pointer; outline: none;">
                                    <option value="5">★★★★★</option>
                                    <option value="4">★★★★☆</option>
                                    <option value="3">★★★☆☆</option>
                                    <option value="2">★★☆☆☆</option>
                                    <option value="1">★☆☆☆☆</option>
                                </select>
                            </div>

                            <textarea name="comment" rows="3" required placeholder="Cuốn sách này để lại cho bạn ấn tượng gì?..." style="width: 100%; padding: 14px; border: 1px solid #e5e7eb; border-radius: 12px; background: #f9fafb; font-size: 0.875rem; resize: vertical; outline: none; line-height: 1.6; transition: border-color 0.2s; margin-bottom: 12px;" onfocus="this.style.borderColor='#0a3622'; this.style.background='white'" onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'"></textarea>

                            <button type="submit" class="detail-btn-cart" style="font-size: 0.8rem; padding: 10px 20px;">Gửi đánh giá</button>
                        </form>
                    @else
                        <div style="text-align: center; padding: 20px;">
                            <p style="font-size: 1.5rem; margin-bottom: 8px;">🛒</p>
                            <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Bạn cần <strong>mua cuốn sách này</strong> trước khi có thể viết đánh giá.</p>
                            <a href="/shop" style="display: inline-block; margin-top: 10px; color: #0a3622; font-weight: 700; font-size: 0.8125rem; text-decoration: underline;">Mua sắm ngay →</a>
                        </div>
                    @endif
                @else
                    <div style="text-align: center; padding: 16px;">
                        <p style="font-size: 0.875rem; color: #6b7280;">Bạn cần <a href="/login" style="color: #0a3622; font-weight: 700; text-decoration: underline;">Đăng nhập</a> để viết đánh giá.</p>
                    </div>
                @endauth
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($book->reviews as $review)
                    <div class="detail-review-card" style="display: flex; gap: 14px; align-items: flex-start;">
                        <div class="detail-avatar" style="background: #ecfdf5; color: #0a3622; border: 1px solid #d1fae5;">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <span style="font-size: 0.875rem; font-weight: 700; color: #1f2937;">{{ $review->user->name ?? 'Ẩn danh' }}</span>
                                <span style="font-size: 0.6875rem; color: #9ca3af;">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div style="color: #f59e0b; font-size: 0.75rem; margin-bottom: 6px;">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </div>
                            <p style="font-size: 0.875rem; color: #4b5563; line-height: 1.6;">{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <div style="background: #fafaf9; padding: 32px; border-radius: 12px; text-align: center; border: 2px dashed #e5e7eb;">
                        <p style="font-size: 0.8125rem; color: #9ca3af; font-style: italic;">Chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ cảm nghĩ nhé!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Cột phải: Thông tin bổ sung --}}
    <div class="space-y-6">
        {{-- Thông tin sách --}}
        <div class="detail-info-card">
            <h3 style="font-size: 0.75rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">Thông tin chi tiết</h3>
            <div class="detail-meta-grid">
                <div class="detail-meta-item">
                    <p style="font-size: 0.625rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px;">Nhà xuất bản</p>
                    <p style="font-weight: 600; color: #374151; font-size: 0.8125rem;">{{ $book->publisher ?? 'NXB V2T' }}</p>
                </div>
                <div class="detail-meta-item">
                    <p style="font-size: 0.625rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px;">Năm xuất bản</p>
                    <p style="font-weight: 600; color: #374151; font-size: 0.8125rem;">{{ $book->publish_year ?? '2024' }}</p>
                </div>
                <div class="detail-meta-item">
                    <p style="font-size: 0.625rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px;">Tình trạng</p>
                    <p style="font-weight: 600; color: {{ $book->stock > 0 ? '#059669' : '#dc2626' }}; font-size: 0.8125rem;">{{ $book->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</p>
                </div>
                <div class="detail-meta-item">
                    <p style="font-size: 0.625rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px;">Kho</p>
                    <p style="font-weight: 600; color: #374151; font-size: 0.8125rem;">{{ $book->stock }} cuốn</p>
                </div>
            </div>
        </div>

        {{-- Ghi chú biên tập --}}
        <div class="detail-editor-note">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.125rem; font-weight: 700; margin-bottom: 12px;">📝 Ghi chú từ Biên tập viên</h3>
            <p style="font-size: 0.875rem; font-style: italic; opacity: 0.9; line-height: 1.7; margin-bottom: 20px;">
                "Cuốn sách này là một hành trình trải nghiệm sâu sắc về tư duy, rất đáng có một vị trí trang trọng trên kệ sách của bạn."
            </p>
            <div style="display: flex; align-items: center; gap: 12px; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">V2T</div>
                <div>
                    <div style="font-size: 0.8125rem; font-weight: 700;">Ban biên tập</div>
                    <div style="font-size: 0.6875rem; opacity: 0.6;">V2T Bookstore</div>
                </div>
            </div>
        </div>

        {{-- Cam kết --}}
        <div class="detail-info-card" style="padding: 24px;">
            <h3 style="font-size: 0.75rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">Cam kết của chúng tôi</h3>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.25rem;">🚚</span>
                    <div><span style="font-size: 0.8125rem; font-weight: 600; color: #374151;">Giao hàng nhanh chóng</span><br><span style="font-size: 0.6875rem; color: #9ca3af;">Nhận sách trong 2-5 ngày làm việc</span></div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.25rem;">🔄</span>
                    <div><span style="font-size: 0.8125rem; font-weight: 600; color: #374151;">Đổi trả miễn phí</span><br><span style="font-size: 0.6875rem; color: #9ca3af;">Trong vòng 7 ngày nếu có lỗi</span></div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div><span style="font-size: 0.8125rem; font-weight: 600; color: #374151;">Sách chính hãng 100%</span><br><span style="font-size: 0.6875rem; color: #9ca3af;">Cam kết bản quyền gốc</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const qtyMinus = document.getElementById('qty-minus');
    const qtyPlus = document.getElementById('qty-plus');
    const qtyInput = document.getElementById('qty-input');
    const qtyHidden = document.getElementById('qty-hidden');
    if (qtyMinus && qtyPlus && qtyInput && qtyHidden) {
        qtyMinus.addEventListener('click', () => { qtyInput.stepDown(); qtyHidden.value = qtyInput.value; });
        qtyPlus.addEventListener('click', () => { qtyInput.stepUp(); qtyHidden.value = qtyInput.value; });
    }
</script>
@endsection