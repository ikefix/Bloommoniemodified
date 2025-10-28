<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\LowStockAlert;
use App\Models\User;

class Product extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'category_id',
        'shop_id',
        'name',
        'description',
        'barcode', // ✅ Added this
        'price',
        'cost_price',
        'stock_quantity',
        'stock_limit',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shop() // ⬅️ Relation added
    {
        return $this->belongsTo(Shop::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function checkStockAndNotify()
    {
        if ($this->stock_quantity <= $this->stock_limit) {
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $admin->notify(new LowStockAlert($this));
            }
        }
    }
    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_shop_id');
    }
}
