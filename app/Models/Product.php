<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'cost_price', 'stock_quantity', 'reorder_level', 'barcode'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
