<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
        'shop_id',
        'quantity',
        'total_price',
        'payment_method',
        'transaction_id',
        'discount_type',      // 🆕 Added
        'discount_value',     // 🆕 Added
        'discount',           // 🆕 Added
    ];

    /**
     * Relationships
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Accessor: Calculate final price after discount
     */
    public function getFinalPriceAttribute()
    {
        return $this->total_price - $this->discount;
    }
}
