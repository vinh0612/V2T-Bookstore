<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = ['supplier_id', 'user_id', 'total_amount', 'note'];

    // Phiếu nhập này thuộc về Nhà cung cấp nào?
    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    // Nhân viên nào tạo phiếu này?
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Các cuốn sách trong phiếu nhập này
    public function items() {
        return $this->hasMany(ImportItem::class);
    }
}