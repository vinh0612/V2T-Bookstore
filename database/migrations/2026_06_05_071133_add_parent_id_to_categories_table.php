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
        Schema::table('categories', function (Blueprint $table) {
            // Tạo cột parent_id nằm sau cột id, cho phép null (vì danh mục cha lớn sẽ không có parent_id)
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            
            // Tạo khóa ngoại liên kết ngược lại bảng categories
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
