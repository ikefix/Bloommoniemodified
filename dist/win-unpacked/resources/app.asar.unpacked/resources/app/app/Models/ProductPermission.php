<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPermission extends Model
{
    protected $fillable = ['manager_id'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}

