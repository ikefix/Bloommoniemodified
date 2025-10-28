<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shop_id',
        'to_shop_id',
        'quantity',
        'cost_price',
        'selling_price',
    ];

    // Product being transferred
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // From shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // To shop
    public function toShop()
    {
        return $this->belongsTo(Shop::class, 'to_shop_id');
    }

    // Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
