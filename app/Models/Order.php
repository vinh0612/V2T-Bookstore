<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Cập nhật chuẩn theo các cột trong file Migration 
    protected $fillable = [
        'user_id', 
        'total_price', 
        'status', 
        'phone', 
        'shipping_address', 
        'payment_method',
        'discount_code',
        'discount_amount',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Thêm phương thức này để định nghĩa mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        // Thiết lập quan hệ Nhiều - Nhiều đảo ngược với Book qua bảng trung gian order_items
        return $this->belongsToMany(Book::class, 'order_items', 'order_id', 'book_id');
    }
}