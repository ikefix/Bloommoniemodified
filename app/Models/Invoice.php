<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'shop_id',
        'invoice_number',
        'invoice_date',
        'goods',
        'discount',
        'tax',
        'total',
        'payment_type',    // ✅ add this
        'amount_paid',     // ✅ add this
        'balance',         // ✅ add this
        'payment_status',  // ✅ add this
    ];
    

    protected $casts = [
        'goods' => 'array',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop() {
        return $this->belongsTo(Shop::class);
    }
}

