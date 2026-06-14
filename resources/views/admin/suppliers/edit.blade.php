@extends('layouts.admin')

@section('title', 'Chỉnh sửa nhà cung cấp')

@section('content')
<div style="width: 100%; max-width: 650px; margin: 0 auto;">
    <a href="{{ route('admin.suppliers.index') }}" style="text-decoration: none; color: #1e3e36; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; font-size: 0.9rem;">
        ← Quay lại danh mục
    </a>

    <div style="background: white; padding: 32px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
        <h2 style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 1.4rem; font-weight: 700; color: #1e3e36; margin: 0 0 24px 0;">Cập nhật thông tin đối tác</h2>

        <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Tên nhà cung cấp</label>
                    <input type="text" name="name" value="{{ $supplier->name }}" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Mã hợp đồng</label>
                    <input type="text" name="contract_code" value="{{ $supplier->contract_code }}" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;" required>
                </div>
            </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Người đại diện liên hệ</label>
                    <input type="text" name="contact_name" value="{{ $supplier->contact_name }}" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Chức vụ</label>
                    <input type="text" name="contact_title" value="{{ $supplier->contact_title }}" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0901234567" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;" maxlength="10" pattern="^0[0-9]{9}$" oninvalid="this.setCustomValidity('Số điện thoại phải bắt đầu bằng số 0 và đủ 10 số nha bro!')" oninput="this.setCustomValidity(''); this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Hòm thư Email</label>
                    <input type="email" name="email" value="{{ $supplier->email }}" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;" required>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #4b5563; margin-bottom: 6px;">Trạng thái hoạt động</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                    <option value="active" {{ $supplier->status === 'active' ? 'selected' : '' }}>Đang hoạt động bình thường</option>
                    <option value="paused" {{ $supplier->status === 'paused' ? 'selected' : '' }}>Tạm dừng hợp tác</option>
                </select>
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background: #92400e; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.95rem;">
                Lưu lại thay đổi thông tin
            </button>
        </form>
    </div>
</div>
@endsection