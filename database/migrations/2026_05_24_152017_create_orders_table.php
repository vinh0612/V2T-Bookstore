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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Khách hàng đặt mua
            $table->decimal('total_price', 10, 2); // Tổng tiền của toàn đơn hàng
            // Trạng thái đơn hàng
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->string('phone'); // Số điện thoại nhận hàng
            $table->text('shipping_address'); // Địa chỉ nhận hàng
            $table->string('payment_method'); // Phương thức thanh toán (COD, Chuyển khoản...)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
