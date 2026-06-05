@extends('layouts.admin')

@section('title', 'Hệ thống kiểm soát - Quản lý mã giảm giá')

@section('content')
<div class="p-6 max-w-[1400px] mx-auto bg-[#fcfbf7] min-h-screen relative">
    
    @if(session('success'))
    <div id="toast-success" class="fixed top-20 right-6 z-50 flex items-center gap-3 bg-[#1e3e36] text-white px-5 py-3 rounded-lg shadow-xl border border-emerald-800 transition-all duration-300">
        <span class="text-sm font-semibold"> {{ session('success') }}</span>
        <button onclick="document.getElementById('toast-success').remove()" class="text-white/70 hover:text-white font-bold text-xs focus:outline-none">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('toast-success')?.remove(), 4000);</script>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-1">
                <a href="/admin/dashboard" class="hover:underline">Hệ thống</a>
                <span>&rsaquo;</span>
                <span class="text-gray-600 font-medium">Mã giảm giá</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 font-serif">Quản lý Mã giảm giá</h2>
            <p class="text-sm text-gray-500 mt-0.5">Thiết lập và theo dõi các chương trình ưu đãi của cửa hàng.</p>
        </div>
        <button onclick="openVoucherModal()" class="flex items-center gap-2 bg-[#1e3e36] text-white font-bold text-xs px-5 py-3 rounded-lg shadow-sm hover:opacity-95 transition tracking-wide uppercase">
            <span>+ Tạo mã giảm giá mới</span>
        </button>
    </div>

    <div class="bg-white p-4 rounded-t-xl border border-gray-150 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.vouchers.index') }}" method="GET" class="w-full flex flex-col md:flex-row items-center gap-3 flex-1">
            <div class="relative w-full md:max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm mã hoặc tên chương trình..." class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
            </div>
            
            <select name="type" onchange="this.form.submit()" class="w-full md:w-44 px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white text-gray-700 focus:outline-none">
                <option value="">Loại giảm giá</option>
                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Giảm phần trăm</option>
                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm số tiền</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="w-full md:w-36 px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white text-gray-700 focus:outline-none">
                <option value="">Trạng thái</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang chạy</option>
                <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-b-xl border border-t-0 border-gray-150 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase border-b border-gray-150">
                    <th class="px-6 py-4">Mã / Chương trình</th>
                    <th class="px-4 py-4">Loại</th>
                    <th class="px-4 py-4 text-center">Giá trị</th>
                    <th class="px-6 py-4">Thời hạn</th>
                    <th class="px-6 py-4">Lượt dùng</th>
                    <th class="px-4 py-4 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-700">
                @forelse($vouchers as $voucher)
                <tr class="hover:bg-gray-50/60 transition">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-950 tracking-wide text-base">{{ $voucher->code }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $voucher->name }}</div>
                    </td>
                    <td class="px-4 py-4 text-gray-500 font-medium">
                        {{ $voucher->type == 'percentage' ? 'Giảm phần trăm' : 'Giảm số tiền' }}
                    </td>
                    <td class="px-4 py-4 text-center font-bold text-gray-900">
                        {{ $voucher->type == 'percentage' ? number_format($voucher->value, 0) . '%' : number_format($voucher->value, 0, ',', '.') . 'đ' }}
                    </td>
                    <td class="px-6 py-4 text-gray-500 font-medium">
                        @if($voucher->start_date || $voucher->end_date)
                            {{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('d/m') : '??' }} - 
                            {{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('d/m/y') : 'Vô thời hạn' }}
                        @else
                            <span class="text-gray-400 italic">Vô thời hạn</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 max-w-[120px]">
                            <div class="text-gray-800 font-semibold">{{ $voucher->uses }} <span class="text-gray-400 font-normal">/ {{ $voucher->max_uses > 0 ? $voucher->max_uses : '∞' }}</span></div>
                        </div>
                        @if($voucher->max_uses > 0)
                            @php $percent = min(($voucher->uses / $voucher->max_uses) * 100, 100); @endphp
                            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
                                <div class="h-full bg-gray-400 rounded-full" @style(['width' => $percent . '%'])></div>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($voucher->status == 'active')
                            <span class="inline-block px-3 py-1 rounded bg-green-50 text-green-700 font-bold text-xs uppercase tracking-wider border border-green-100">Đang chạy</span>
                        @elseif($voucher->status == 'paused')
                            <span class="inline-block px-3 py-1 rounded bg-amber-50 text-amber-700 font-bold text-xs uppercase tracking-wider border border-amber-100">Tạm dừng</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded bg-red-50 text-red-600 font-bold text-xs uppercase tracking-wider border border-red-100">Đã hết hạn</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-4 text-gray-400">
                            <button onclick="openEditModal(this)" data-voucher="{{ json_encode($voucher) }}" class="hover:text-[#1e3e36] transition p-1" title="Chỉnh sửa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                            </button>
                            <button onclick="submitToggleStatus(this)" data-id="{{ $voucher->id }}" class="transition p-1 {{ $voucher->status === 'paused' ? 'text-green-600 hover:text-green-800' : 'hover:text-amber-600' }}" title="{{ $voucher->status === 'paused' ? 'Kích hoạt lại' : 'Tạm dừng' }}">
                                @if($voucher->status === 'paused')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                                @endif
                            </button>
                            <button onclick="submitDeleteVoucher(this)" data-id="{{ $voucher->id }}" class="hover:text-red-600 transition p-1" title="Xóa mã">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.8c0-.632-.425-1.18-.074-1.18h-4.2c-.65 0-1.074.549-1.074 1.18v1.8m4.5 0H9"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 bg-white">
                        <span class="text-2xl block mb-2">🎟️</span>
                        Chưa tìm thấy mã giảm giá nào phù hợp với bộ lọc.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($vouchers->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-150 flex items-center justify-between">
            <span class="text-sm text-gray-500">Hiển thị {{ $vouchers->count() }} trong tổng số {{ $vouchers->total() }} mã giảm giá</span>
            <div>{{ $vouchers->links() }}</div>
        </div>
        @endif
    </div>

</div>

<div id="voucherModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeVoucherModal()"></div>
    <div class="bg-white rounded-xl shadow-2xl z-10 w-full max-w-lg overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-150 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 font-serif"> Thêm mã giảm giá mới</h3>
            <button onclick="closeVoucherModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold focus:outline-none">&times;</button>
        </div>
        <form action="{{ route('admin.vouchers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Mã giảm giá *</label>
                    <input type="text" name="code" required placeholder="Ví dụ: QUY2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm uppercase focus:outline-none focus:border-[#1e3e36]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Tên chương trình *</label>
                    <input type="text" name="name" required placeholder="Ví dụ: Ưu đãi quý 2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Loại chiết khấu *</label>
                    <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3e36]">
                        <option value="percentage">Giảm theo phần禅 (%)</option>
                        <option value="fixed">Giảm số tiền mặt (đ)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Giá trị giảm *</label>
                    <input type="number" name="value" required min="0" placeholder="Ví dụ: 10 hoặc 50000" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Ngày hết hạn</label>
                    <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Giới hạn số lượt dùng (Để trống = Vô hạn)</label>
                <input type="number" name="max_uses" min="0" placeholder="Ví dụ: 500" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeVoucherModal()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-500 font-semibold hover:bg-gray-50 transition">Hủy bỏ</button>
                <button type="submit" class="px-5 py-2 bg-[#1e3e36] text-white font-bold rounded-lg text-sm hover:opacity-95 transition">Xác nhận tạo</button>
            </div>
        </form>
    </div>
</div>

<div id="editVoucherModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="bg-white rounded-xl shadow-2xl z-10 w-full max-w-lg overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-150 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 font-serif"> Chỉnh sửa mã giảm giá</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold focus:outline-none">&times;</button>
        </div>
        <form id="editVoucherForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Mã giảm giá *</label>
                    <input type="text" id="edit_code" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm uppercase focus:outline-none focus:border-[#1e3e36]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Tên chương trình *</label>
                    <input type="text" id="edit_name" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Loại chiết khấu *</label>
                    <select id="edit_type" name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3e36]">
                        <option value="percentage">Giảm theo phần trăm (%)</option>
                        <option value="fixed">Giảm số tiền mặt (đ)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Giá trị giảm *</label>
                    <input type="number" id="edit_value" name="value" required min="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Ngày bắt đầu</label>
                    <input type="date" id="edit_start_date" name="start_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Ngày hết hạn</label>
                    <input type="date" id="edit_end_date" name="end_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">Giới hạn số lượt dùng (0 = Vô hạn)</label>
                <input type="number" id="edit_max_uses" name="max_uses" min="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3e36]">
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-500 font-semibold hover:bg-gray-50 transition">Hủy bỏ</button>
                <button type="submit" class="px-5 py-2 bg-[#1e3e36] text-white font-bold rounded-lg text-sm hover:opacity-95 transition">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<form id="sharedActionForm" method="POST" class="hidden">
    @csrf
    <div id="formMethodContainer"></div>
</form>

<script>
    function openVoucherModal() {
        document.getElementById('voucherModal').classList.remove('hidden');
    }
    function closeVoucherModal() {
        document.getElementById('voucherModal').classList.add('hidden');
    }

    // ĐÃ CHUẨN HÓA: JS đọc dữ liệu động an toàn từ data-attribute của thẻ HTML
    function openEditModal(button) {
        const voucher = JSON.parse(button.getAttribute('data-voucher'));
        document.getElementById('editVoucherForm').action = `/admin/vouchers/${voucher.id}`;
        document.getElementById('edit_code').value = voucher.code;
        document.getElementById('edit_name').value = voucher.name;
        document.getElementById('edit_type').value = voucher.type;
        document.getElementById('edit_value').value = Math.floor(voucher.value);
        document.getElementById('edit_start_date').value = voucher.start_date || '';
        document.getElementById('edit_end_date').value = voucher.end_date || '';
        document.getElementById('edit_max_uses').value = voucher.max_uses;
        
        document.getElementById('editVoucherModal').classList.remove('hidden');
    }
    
    function closeEditModal() {
        document.getElementById('editVoucherModal').classList.add('hidden');
    }

    function submitToggleStatus(button) {
        const id = button.getAttribute('data-id');
        const form = document.getElementById('sharedActionForm');
        form.action = `/admin/vouchers/${id}/toggle-status`;
        document.getElementById('formMethodContainer').innerHTML = ''; 
        form.submit();
    }

    function submitDeleteVoucher(button) {
        const id = button.getAttribute('data-id');
        if (confirm('Bro có chắc chắn muốn xóa vĩnh viễn mã giảm giá này không?')) {
            const form = document.getElementById('sharedActionForm');
            form.action = `/admin/vouchers/${id}`;
            document.getElementById('formMethodContainer').innerHTML = '@method("DELETE")';
            form.submit();
        }
    }
</script>
@endsection