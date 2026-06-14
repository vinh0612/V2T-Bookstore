@extends('layouts.admin')

@section('title', 'Quản lý đánh giá sách')

@section('content')
<style>
    .rev-card { background: white; padding: 20px 24px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.01); }
    .tab-link { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; color: #4b5563; background: #f3f4f6; transition: all 0.2s; }
    .tab-link-active { background: #1e3e36 !important; color: white !important; }
    
    .review-item-box { background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; margin-bottom: 16px; position: relative; }
    .review-item-violated { border-color: #fca5a5; background: #fff5f5; }
    .review-item-suspicious { border-color: #fdba74; background: #fffbeb; } /* Nền vàng nhẹ cho nghi vấn */
    
    .status-lbl { font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; float: right; }
    .lbl-pending { background: #fef3c7; color: #92400e; }
    .lbl-suspicious { background: #ffedd5; color: #c2410c; } /* Màu cam cho nghi vấn */
    .lbl-approved { background: #d1fae5; color: #065f46; }
    .lbl-violated { background: #fee2e2; color: #b91c1c; }

    .reply-area { width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.85rem; outline: none; margin-top: 10px; resize: none; }
    .btn-action-small { padding: 6px 14px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; }

    .purchase-verified { font-weight: 600; color: #059669; }
    .purchase-unverified { font-weight: 600; color: #9ca3af; }
</style>

<div style="width: 100%;">
    <div style="margin-bottom: 28px;">
        <h1 style="font-family: 'Segoe UI', Roboto, sans-serif; font-size: 1.8rem; font-weight: 700; color: #1e3e36; margin: 0 0 6px 0;">Quản lý Đánh giá</h1>
        <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">Kiểm duyệt bình luận của khách hàng, phản hồi đóng góp và xử lý các nội dung vi phạm tiêu chuẩn.</p>
    </div>

    {{-- Thống kê giờ chia thành 5 cột để nhét thằng Nghi vấn vào giữa --}}
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px;">
        <div class="rev-card">
            <span style="font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;">Tổng đánh giá</span>
            <h2 style="font-size: 1.6rem; font-weight: 700; margin: 8px 0 0 0; color: #111827;">{{ number_format($totalCount ?? 0) }}</h2>
        </div>
        <div class="rev-card">
            <span style="font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;">Chờ duyệt</span>
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 8px;">
                <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0; color: #b91c1c;">{{ $pendingCount ?? 0 }}</h2>
            </div>
        </div>
        <div class="rev-card">
            <span style="font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;">Nghi vấn (AI check)</span>
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 8px;">
                <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0; color: #c2410c;">{{ $suspiciousCount ?? 0 }}</h2>
            </div>
        </div>
        <div class="rev-card">
            <span style="font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;">Điểm trung bình</span>
            <h2 style="font-size: 1.6rem; font-weight: 700; margin: 8px 0 0 0; color: #92400e;">{{ $avgRating ?? 0 }} <span style="color: #fbbf24; font-size: 1.2rem;">★</span></h2>
        </div>
        <div class="rev-card">
            <span style="font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;">Bị báo cáo vi phạm</span>
            <h2 style="font-size: 1.6rem; font-weight: 700; margin: 8px 0 0 0; color: #374151;">{{ $violatedCount ?? 0 }}</h2>
        </div>
    </div>

    {{-- Thanh điều hướng Tabs & Nút xóa All --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; background: white; padding: 12px; border-radius: 12px; border: 1px solid #e5e7eb;">
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.reviews.index') }}" class="tab-link {{ !request()->has('tab') ? 'tab-link-active' : '' }}">Tất cả</a>
            <a href="{{ route('admin.reviews.index', ['tab' => 'pending']) }}" class="tab-link {{ request('tab') === 'pending' ? 'tab-link-active' : '' }}">Chờ duyệt</a>
            <a href="{{ route('admin.reviews.index', ['tab' => 'suspicious']) }}" class="tab-link {{ request('tab') === 'suspicious' ? 'tab-link-active' : '' }}">Nghi vấn</a>
            <a href="{{ route('admin.reviews.index', ['tab' => 'approved']) }}" class="tab-link {{ request('tab') === 'approved' ? 'tab-link-active' : '' }}">Đã duyệt</a>
            <a href="{{ route('admin.reviews.index', ['tab' => 'violated']) }}" class="tab-link {{ request('tab') === 'violated' ? 'tab-link-active' : '' }}">Vi phạm</a>
        </div>
       
        {{-- Nếu đang ở tab Chờ duyệt -> Hiện nút Duyệt sạch Pending --}}
        @if(request('tab') === 'pending')
            <form action="{{ route('admin.reviews.approveAllPending') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bro có chắc chắn muốn phê duyệt TẤT CẢ bình luận đang chờ không?')">
                @csrf
                <button type="submit" class="btn-action-small" style="background: #1e3e36; color: white; padding: 8px 16px;">
                    ✓ Phê duyệt tất cả chờ duyệt
                </button>
            </form>
        @endif

        {{-- Nếu đang ở tab Nghi vấn -> Hiện nút Duyệt sạch Suspicious --}}
        @if(request('tab') === 'suspicious')
            <form action="{{ route('admin.reviews.approveAllSuspicious') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bro có chắc chắn muốn phê duyệt TẤT CẢ bình luận nghi vấn không?')">
                @csrf
                <button type="submit" class="btn-action-small" style="background: #c2410c; color: white; padding: 8px 16px;">
                    ✓ Phê duyệt tất cả nghi vấn
                </button>
            </form>
        @endif

        {{-- Nếu đang ở tab Vi phạm -> Hiện nút Xóa sạch rác --}}
        @if(request('tab') === 'violated')
            <form action="{{ route('admin.reviews.destroyAllViolated') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bro có chắc chắn muốn dọn sạch TẤT CẢ bình luận vi phạm không? Hành động này không thể hoàn tác!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action-small" style="background: #dc2626; color: white; padding: 8px 16px;">
                    🗑 Xóa sạch danh sách vi phạm
                </button>
            </form>
        @endif
    </div>

    <div style="width: 100%;">
        @forelse($reviews as $review)
        <div class="review-item-box {{ $review->status === 'violated' ? 'review-item-violated' : ($review->status === 'suspicious' ? 'review-item-suspicious' : '') }}">
            
            <span class="status-lbl 
                @if($review->status === 'pending') lbl-pending 
                @elseif($review->status === 'suspicious') lbl-suspicious
                @elseif($review->status === 'approved') lbl-approved 
                @else lbl-violated @endif">
                @if($review->status === 'pending') Chờ duyệt
                @elseif($review->status === 'suspicious') Nghi vấn
                @elseif($review->status === 'approved') Đã duyệt
                @else Vi phạm @endif
            </span>

            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <img src="{{ $review->book->image_url }}" style="width: 65px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 4px 0; font-size: 1.05rem; color: #1e3e36; font-weight: 700;">{{ $review->book->title }}</h4>
                    <div style="color: #fbbf24; font-size: 0.9rem; margin-bottom: 8px;">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $review->rating ? '★' : '☆' }}
                        @endfor
                        <span style="color: #6b7280; font-size: 0.8rem; margin-left: 6px;">({{ $review->rating }}/5 sao)</span>
                    </div>

                    @if($review->status === 'violated')
                        <p style="color: #b91c1c; font-weight: 600; font-size: 0.875rem; margin: 0 0 10px 0; font-style: italic;">[Nội dung chứa từ ngữ không phù hợp hoặc quảng cáo rác]</p>
                        <p style="color: #9ca3af; font-size: 0.9rem; margin: 0 0 12px 0; text-decoration: line-through;">{{ $review->comment }}</p>
                    @elseif($review->status === 'suspicious')
                        <p style="color: #c2410c; font-weight: 600; font-size: 0.875rem; margin: 0 0 10px 0; font-style: italic;">[Hệ thống AI nghi ngờ nội dung này cần kiểm duyệt thủ công]</p>
                        <p style="color: #374151; font-size: 0.925rem; line-height: 1.5; margin: 0 0 12px 0;">"{!! nl2br(e($review->comment)) !!}"</p>
                    @else
                        <p style="color: #374151; font-size: 0.925rem; line-height: 1.5; margin: 0 0 12px 0;">"{!! nl2br(e($review->comment)) !!}"</p>
                    @endif

                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #6b7280; border-top: 1px dashed #e5e7eb; padding-top: 10px;">
                        <span style="font-weight: 700; color: #4b5563;">👤 {{ $review->user->name }}</span>
                        <span>•</span>
                        <span>📅 {{ $review->created_at->format('d/m/Y H:i') }}</span>
                        <span>•</span>
                        <span class="{{ $review->is_purchased ? 'purchase-verified' : 'purchase-unverified' }}">
                            {{ $review->is_purchased ? '✓ Khách hàng đã mua' : '✗ Chưa xác minh mua hàng' }}
                        </span>
                    </div>

                    @if($review->admin_reply)
                        <div style="margin-top: 14px; background: #f9fafb; border-left: 3px solid #1e3e36; padding: 12px; border-radius: 0 8px 8px 0; font-size: 0.875rem;">
                            <div style="font-weight: 700; color: #1e3e36; margin-bottom: 4px;">💬 Phản hồi từ Admin:</div>
                            <p style="color: #4b5563; margin: 0; font-style: italic;">"{{ $review->admin_reply }}"</p>
                        </div>
                    @endif

                    <div style="margin-top: 16px; display: flex; gap: 8px; align-items: center;">
                        {{-- Nút Phê Duyệt dành cho cả Pending và Suspicious --}}
                        @if($review->status === 'pending' || $review->status === 'suspicious')
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-action-small" style="background: #1e3e36; color: white;">✓ Phê duyệt</button>
                            </form>
                        @endif

                        @if($review->status !== 'violated')
                            <button class="btn-action-small" style="background: white; border: 1px solid #e5e7eb; color: #4b5563;" data-id="{{ $review->id }}" onclick="toggleReplyForm(this)">↩ Phản hồi</button>
                            
                            <form action="{{ route('admin.reviews.violate', $review->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-action-small" style="background: #fee2e2; color: #b91c1c;">⚠ Ẩn đánh giá vi phạm</button>
                            </form>
                        @else
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-action-small" style="background: #e6f4ea; color: #137333;">⟲ Khôi phục</button>
                            </form>
                        @endif

                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bro có chắc chắn muốn xóa vĩnh viễn đánh giá này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action-small" style="background: none; color: #9ca3af; font-weight: 500;">Xóa vĩnh viễn</button>
                        </form>
                    </div>

                    <div id="reply-form-{{ $review->id }}" style="display: none; margin-top: 12px; border-top: 1px solid #f3f4f6; padding-top: 12px;">
                        <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                            @csrf
                            <textarea name="admin_reply" class="reply-area" rows="2" placeholder="Nhập nội dung phản hồi..." required>{{ $review->admin_reply }}</textarea>
                            <div style="margin-top: 6px; text-align: right;">
                                <button type="button" class="btn-action-small" style="background: #f3f4f6; color: #4b5563;" data-id="{{ $review->id }}" onclick="toggleReplyForm(this)">Hủy</button>
                                <button type="submit" class="btn-action-small" style="background: #1e3e36; color: white;">Gửi phản hồi</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        @empty
            <div style="text-align: center; color: #6b7280; padding: 40px; background: white; border-radius: 12px; border: 1px solid #e5e7eb;">Hiện tại chưa có đánh giá nào thuộc mục này.</div>
        @endforelse
    </div>

    <div style="margin-top: 24px;">
        {{ $reviews->appends(request()->query())->links() }}
    </div>
</div>

<script>
    function toggleReplyForm(element) {
        const id = element.getAttribute('data-id');
        const form = document.getElementById('reply-form-' + id);
        if(form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>
@endsection