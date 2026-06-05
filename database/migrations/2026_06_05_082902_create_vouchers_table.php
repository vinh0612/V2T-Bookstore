<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // Mã giảm giá (Ví dụ: INKWELL10)
            $table->string('name');                    // Tên chương trình (Ví dụ: Giảm giá hè 2024)
            $table->enum('type', ['percentage', 'fixed']); // Loại giảm giá: phần trăm hoặc số tiền cố định
            $table->decimal('value', 15, 2);           // Giá trị giảm (Ví dụ: 10.00 hoặc 50000.00)
            $table->date('start_date')->nullable();    // Ngày bắt đầu
            $table->date('end_date')->nullable();      // Ngày hết hạn (Null tương đương vô thời hạn)
            $table->integer('max_uses')->default(0);   // Giới hạn lượt dùng tối đa (0 nghĩa là vô hạn)
            $table->integer('uses')->default(0);       // Số lượt đã thực tế sử dụng
            $table->enum('status', ['active', 'paused', 'expired'])->default('active'); // Trạng thái mã
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
