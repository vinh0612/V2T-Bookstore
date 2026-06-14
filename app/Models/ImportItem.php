<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ImportItem extends Model
{
    protected $fillable = ['import_id', 'book_id', 'quantity', 'import_price'];

    public function import() {
        return $this->belongsTo(Import::class);
    }

    public function book() {
        return $this->belongsTo(Book::class);
    }
}