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
            $table->string('code')->unique();          
            $table->string('name');                    
            $table->enum('type', ['percentage', 'fixed']); 
            $table->decimal('value', 15, 2);           
            $table->integer('min_order_value')->default(0);       
            $table->date('start_date')->nullable();    
            $table->date('end_date')->nullable();      
            $table->integer('max_uses')->default(0);   
            $table->integer('uses')->default(0);       
            $table->enum('status', ['active', 'paused', 'expired'])->default('active'); 
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
