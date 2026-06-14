@extends('layouts.admin')

@section('title', 'Danh mục Nhà cung cấp')

@section('content')
<style>
    .supplier-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid #e5e7eb; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.01); }
    .supplier-table { width: 100%; border-collapse: collapse; text-align: left; }
    .supplier-table th { padding: 16px; color: #6b7280; font-size: 0.75rem; font-weight: 700; border-bottom: 1px solid #f3f4f6; text-transform: uppercase; letter-spacing: 0.05em; }
    .supplier-table td { padding: 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; font-size: 0.9rem; }
    
    .supplier-avatar { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; background: #e5e7eb; color: #1e3e36; }
    
    .badge-supplier { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .badge-supplier-active { background: #e6f4ea; color: #137333; }
    .badge-supplier-paused { background: #fce8e6; color: #c5221f; }

    .btn-action-round { border: none; background: none; color: #6b7280; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; transition: all 0.2s; text-decoration: none; }
    .btn-action-round:hover { background: #f3f4f6; color: #1e3e36; }
    .btn-delete-round:hover { background: #fee2e2; color: #dc2626; }
</style>

<div style="width: 100%;">
    <div style="margin-bottom: 28px; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <p style="font-size: 0.8rem; color: #6b7280; margin: 0 0 6px 0; font-weight: 500;">Hệ thống &gt; <span style="color: #92400e;">Đối tác &amp; Nhà cung cấp</span></p>
            <h1 style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 1.8rem; font-weight: 700; color: #1e3e36; margin: 0 0 8px 0;">Danh mục Nhà cung cấp</h1>
            <p style="color: #6b7280; font-size: 0.9rem; margin: 0; max-width: 600px; line-height: 1.5;">Theo dõi và quản lý mối quan hệ với các nhà xuất bản và đối tác cung ứng sách.</p>
        </div>
        <a href="{{ route('admin.suppliers.create') }}" style="padding: 10px 20px; background: #1e3e36; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Thêm nhà cung cấp mới
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px;">
        <div class="supplier-card">
            <p style="color: #6b7280; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin: 0;">Tổng số nhà cung cấp</p>
            <h2 style="font-size: 1.8rem; font-weight: 700; margin: 12px 0 0 0; color: #111827;">{{ $totalSuppliers }}</h2>
        </div>
        <div class="supplier-card">
            <p style="color: #6b7280; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin: 0;">Đang hoạt động</p>
            <h2 style="font-size: 1.8rem; font-weight: 700; margin: 12px 0 0 0; color: #137333;">{{ $activeSuppliers }}</h2>
        </div>
        <div class="supplier-card">
            <p style="color: #6b7280; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin: 0;">Đang tạm dừng</p>
            <h2 style="font-size: 1.8rem; font-weight: 700; margin: 12px 0 0 0; color: #c5221f;">{{ $pausedSuppliers }}</h2>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 12px 16px; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 20px; font-weight: 500; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 700; font-size: 1.05rem; color: #111827; margin: 0;">Danh sách đối tác</h3>
        </div>

        <table class="supplier-table">
            <thead>
                <tr>
                    <th>Nhà cung cấp</th>
                    <th>Người liên hệ</th>
                    <th>Thông tin liên lạc</th>
                    <th>Trạng thái</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                @php
                    // Sử dụng mb_substr để tránh lỗi font chữ tiếng Việt có dấu
                    $words = explode(' ', $supplier->name);
                    if (count($words) > 1) {
                        $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
                    } else {
                        $initials = mb_substr($supplier->name, 0, 2);
                    }
                    $initials = mb_strtoupper($initials);
                @endphp
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div class="supplier-avatar">{{ $initials }}</div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">{{ $supplier->name }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">Hợp đồng: {{ $supplier->contract_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #111827;">{{ $supplier->contact_name }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">{{ $supplier->contact_title }}</div>
                    </td>
                    <td style="line-height: 1.5;">
                        <div style="color: #4b5563;">📞 {{ $supplier->phone }}</div>
                        <div style="color: #6b7280; font-size: 0.8rem;">✉️ {{ $supplier->email }}</div>
                    </td>
                    <td>
                        <span class="badge-supplier {{ $supplier->status === 'active' ? 'badge-supplier-active' : 'badge-supplier-paused' }}">
                            {{ $supplier->status === 'active' ? 'Đang hoạt động' : 'Tạm dừng' }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; gap: 4px; align-items: center;">
                            
                            {{-- ĐÃ GHÉP THÀNH CÔNG NÚT NHẬP KHO (MÀU XANH DƯƠNG) VÀO ĐÂY --}}
                            <a href="{{ route('admin.suppliers.import', $supplier->id) }}" class="btn-action-round" style="color: #0284c7;" title="Lập phiếu nhập kho sách">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM19.5 18.75a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 5.25h16.5l1.05 7.5h2.25v5.25H2.25V5.25Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 5.25V12.75" />
                                </svg>
                            </a>

                            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn-action-round" title="Chỉnh sửa thông tin">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                            </a>
                            
                            <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bro có chắc chắn muốn xóa nhà cung cấp này khỏi hệ thống không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-round btn-delete-round" title="Xóa nhà cung cấp">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280; padding: 32px;">Hiện chưa có nhà cung cấp nào trong database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 24px;">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection