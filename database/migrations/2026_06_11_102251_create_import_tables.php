
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng Phiếu nhập (Imports)
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id'); // Nhập của ông nào
            $table->unsignedBigInteger('user_id');     // Nhân viên/Admin nào tạo phiếu
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng tiền nhập
            $table->text('note')->nullable();          // Ghi chú thêm
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Bảng Chi tiết Phiếu nhập (Import Items)
        Schema::create('import_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_id');
            $table->unsignedBigInteger('book_id');     // Nhập cuốn sách nào
            $table->integer('quantity');               // Số lượng bao nhiêu
            $table->decimal('import_price', 15, 2);    // Giá nhập vào (Khác với giá bán ra nhé)
            $table->timestamps();

            $table->foreign('import_id')->references('id')->on('imports')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_items');
        Schema::dropIfExists('imports');
    }
};