<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['user_id', 'name', 'location'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_shop_id');
    }

    // app/Models/Shop.php
        public function user()
        {
            return $this->belongsTo(User::class);
        }

            public function purchases()
    {
        return $this->hasMany(PurchaseItem::class);
    }


}

