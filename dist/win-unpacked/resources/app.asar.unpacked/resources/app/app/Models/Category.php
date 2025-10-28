<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Define the relationship between Category and Product
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Fetch products by category
     */
    public function getProductsByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)->get();
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_shop_id');
    }
}
