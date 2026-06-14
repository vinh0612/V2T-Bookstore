@extends('layouts.admin')

@section('title', 'Chỉnh sửa sách')

@section('content')
<style>
    .v2t-form-card {
        background: #ffffff;
        padding: 28px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        max-width: 760px;
        width: 100%;
    }
    .v2t-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 20px;
    }
    .v2t-form-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .v2t-form-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #111827;
        background-color: #f9fafb;
        outline: none;
        transition: all 0.2s;
    }
    .v2t-form-input:focus {
        border-color: #1e3e36;
        background-color: #ffffff;
        box-shadow: 0 0 0 2px rgba(30, 62, 54, 0.1);
    }
</style>

<div style="width: 100%;">
    
    <div style="margin-bottom: 24px;">
        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
            <a href="{{ route('admin.books.index') }}" style="color: #1e3e36; text-decoration: none; font-weight: 600;">← Quay lại danh sách</a>
        </p>
        <h1 style="font-family: Georgia, serif; font-size: 1.75rem; font-weight: 700; color: #111827; margin: 8px 0 0 0;">Chỉnh sửa thông tin sách</h1>
    </div>

    <div class="v2t-form-card">
        <form action="{{ route('admin.books.update', $book->id) }}" method="POST">
            @csrf

            @if ($errors->any())
                <div style="background-color: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px 20px; border-radius: 8px; margin-bottom: 24px;">
                    <strong style="font-size: 0.875rem;">⚠️ Vui lòng kiểm tra lại:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 0.8rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @method('PUT')

            <div class="v2t-form-group">
                <label class="v2t-form-label">Tiêu đề sách <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" class="v2t-form-input" required>
            </div>

            <div class="v2t-form-group">
                <label class="v2t-form-label">Tác giả / Dịch giả <span style="color: #dc2626;">*</span></label>
                <input type="text" name="author" value="{{ old('author', $book->author) }}" class="v2t-form-input" required>
            </div>

            <div class="v2t-form-group">
                <label class="v2t-form-label">Danh mục sách <span style="color: #dc2626;">*</span></label>
                <select name="category_id" class="v2t-form-input" style="cursor: pointer;" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="v2t-form-group">
                    <label class="v2t-form-label">Giá bán (VNĐ) <span style="color: #dc2626;">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $book->price) }}" min="0" class="v2t-form-input" required>
                </div>
                <div class="v2t-form-group">
                    <label class="v2t-form-label">Số lượng tồn kho <span style="color: #dc2626;">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" min="0" class="v2t-form-input" required>
                </div>
            </div>

            <div class="v2t-form-group">
                <label class="v2t-form-label">Đường dẫn ảnh bìa sách (URL)</label>
                <input type="url" name="image_url" value="{{ old('image_url', $book->image_url) }}" class="v2t-form-input">
            </div>

            <div class="v2t-form-group">
                <label class="v2t-form-label">Tóm tắt nội dung cuốn sách</label>
                <textarea name="description" rows="5" class="v2t-form-input" style="resize: vertical; min-height: 100px;">{{ old('description', $book->description) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 28px; border-top: 1px solid #f3f4f6; padding-top: 20px;">
                <button type="submit" style="background-color: #1e3e36; color: white; font-size: 0.875rem; font-weight: 700; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer;" class="hover:opacity-95 transition shadow-sm">
                    Lưu các thay đổi
                </button>
                <a href="{{ route('admin.books.index') }}" style="background-color: #f3f4f6; color: #4b5563; font-size: 0.875rem; font-weight: 600; padding: 10px 20px; border: 1px solid #d1d5db; border-radius: 8px; text-decoration: none;" class="hover:bg-gray-100 transition">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </div>

</div>
@endsection