@extends('layouts.admin')

@section('title', 'Lập phiếu nhập kho - ' . $supplier->name)

@section('content')
<style>
    .import-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .v2t-form-label { display: block; font-size: 0.75rem; font-weight: 700; color: #4b5563; text-transform: uppercase; margin-bottom: 6px; tracking: 0.05em; }
    .v2t-form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem; outline: none; background: #fff; }
    .v2t-form-control:focus { border-color: #1e3e36; box-shadow: 0 0 0 3px rgba(30,62,54,0.1); }
    .import-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    .import-table th { background: #f9fafb; padding: 12px 16px; color: #6b7280; font-size: 0.8rem; font-weight: 700; border-bottom: 2px solid #e5e7eb; }
    .import-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .btn-add-row { padding: 8px 16px; background: #fff; color: #1e3e36; border: 1px dashed #1e3e36; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
    .btn-add-row:hover { background: rgba(30,62,54,0.05); }
    .btn-remove-row { background: none; border: none; color: #dc2626; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
</style>

<div style="width: 100%; max-width: 1100px; margin: 0 auto;">
    {{-- Thanh tiêu đề --}}
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('admin.suppliers.index') }}" style="color: #6b7280; text-decoration: none; font-weight: 600; font-size: 0.9rem;">&larr; Quay lại danh sách</a>
            <h1 style="font-size: 1.6rem; font-weight: 700; color: #1e3e36; margin: 6px 0 0 0;">Lập Phiếu Nhập Kho Sách</h1>
            <p style="color: #6b7280; font-size: 0.85rem; margin: 2px 0 0 0;">Nhập hàng từ đối tác: <strong style="color: #111827;">{{ $supplier->name }}</strong></p>
        </div>
    </div>

    <form action="{{ route('admin.suppliers.import.store', $supplier->id) }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 24px; align-items: start;">
            
            {{-- Khối trái: Chọn sách và số lượng --}}
            <div>
                <div class="import-card">
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 16px 0; color: #111827;">Danh sách sách nhập</h3>
                    
                    <table class="import-table" id="import-items-table">
                        <thead>
                            <tr>
                                <th style="text-align: left; width: 45%;">TÊN SÁCH</th>
                                <th style="text-align: center; width: 20%;">SỐ LƯỢNG</th>
                                <th style="text-align: right; width: 25%;">GIÁ NHẬP (Đ/CUỐN)</th>
                                <th style="text-align: center; width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Dòng mặc định đầu tiên --}}
                            <tr>
                                <td>
                                    <select name="book_ids[]" class="v2t-form-control select-book" required>
                                        <option value="">-- Chọn cuốn sách --</option>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}">
                                                {{ $book->title }} (Kho còn: {{ $book->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantities[]" min="1" value="10" class="v2t-form-control input-qty" style="text-align: center;" required>
                                </td>
                                <td>
                                    <input type="number" name="import_prices[]" min="0" placeholder="0" class="v2t-form-control input-price" style="text-align: right;" required>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="margin-top: 16px;">
                        <button type="button" class="btn-add-row" id="btn-add-item">+ Thêm dòng sản phẩm</button>
                    </div>
                </div>
            </div>

            {{-- Khối phải: Thông tin phiếu và Tổng tiền --}}
            <div>
                <div class="import-card">
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 16px 0; color: #111827;">Thông tin phiếu nhập</h3>
                    
                    <div class="v2t-input-group" style="margin-bottom: 16px;">
                        <label class="v2t-form-label">Người lập phiếu</label>
                        <input type="text" class="v2t-form-control" value="{{ Auth::user()->name }}" style="background: #f3f4f6; color: #4b5563; font-weight: 600;" readonly>
                    </div>

                    <div class="v2t-input-group" style="margin-bottom: 20px;">
                        <label class="v2t-form-label">Ghi chú nhập kho</label>
                        <textarea name="note" rows="3" class="v2t-form-control" placeholder="Ví dụ: Nhập bổ sung sách hội sách tháng 6, hàng nguyên vẹn..."></textarea>
                    </div>

                    <div style="background: #f9fafb; padding: 16px; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.85rem; font-weight: 700; color: #4b5563;">TỔNG TIỀN NHẬP:</span>
                            <span id="txt-total-amount" style="font-size: 1.4rem; font-weight: 800; color: #059669;">0đ</span>
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; padding: 12px; background: #1e3e36; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(30,62,54,0.2);">
                        📥 Xác nhận nhập kho
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- Javascript tính toán thời gian thực --}}
<script>
    const tableBody = document.querySelector('#import-items-table tbody');
    const btnAddItem = document.getElementById('btn-add-item');
    const txtTotal = document.getElementById('txt-total-amount');

    // Lưu lại khuôn mẫu dòng để clone bằng JS
    const rowTemplate = tableBody.querySelector('tr').cloneNode(true);
    // Reset giá trị dòng template
    rowTemplate.querySelector('.select-book').value = '';
    rowTemplate.querySelector('.input-qty').value = '10';
    rowTemplate.querySelector('.input-price').value = '';

    // Sự kiện bấm nút thêm dòng
    btnAddItem.addEventListener('click', () => {
        const newRow = rowTemplate.cloneNode(true);
        tableBody.appendChild(newRow);
        bindRowEvents(newRow);
        calculateTotal();
    });

    function removeRow(btn) {
        const rows = tableBody.querySelectorAll('tr');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotal();
        } else {
            alert('Phiếu nhập kho phải có ít nhất một dòng sản phẩm bro ơi!');
        }
    }

    // Hàm tính tổng tiền tự động
    function calculateTotal() {
        let total = 0;
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const price = parseFloat(row.querySelector('.input-price').value) || 0;
            total += qty * price;
        });

        // Format tiền VND
        txtTotal.innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total).replace('₫', 'đ');
    }

    // Gắn sự kiện lắng nghe thay đổi số lượng/giá của từng dòng
    function bindRowEvents(row) {
        row.querySelector('.input-qty').addEventListener('input', calculateTotal);
        row.querySelector('.input-price').addEventListener('input', calculateTotal);
    }

    // Chạy kích hoạt cho dòng đầu tiên có sẵn
    bindRowEvents(tableBody.querySelector('tr'));
</script>
@endsection