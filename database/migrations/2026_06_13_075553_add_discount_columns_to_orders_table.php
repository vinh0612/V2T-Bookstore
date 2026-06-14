<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thêm cột mã giảm giá (nullable vì khách có thể không xài mã)
            $table->string('discount_code')->nullable()->after('payment_method');
            
            // Thêm cột số tiền được giảm (mặc định là 0đ)
            $table->integer('discount_amount')->default(0)->after('discount_code');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_code', 'discount_amount']);
        });
    }
};
