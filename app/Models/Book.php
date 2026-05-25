<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Đảm bảo dòng này đã được lưu để bẻ khóa hoàn toàn quyền nạp ID từ Seeder
    protected $guarded = [];

    public function reviews() { return $this->hasMany(Review::class); }
    public function orders() { return $this->belongsToMany(Order::class, 'order_items', 'book_id', 'order_id'); }
}