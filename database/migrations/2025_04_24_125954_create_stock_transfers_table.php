<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products'); // Foreign key to products
            $table->foreignId('shop_id')->constrained('shops'); // Foreign key to the source shop
            $table->foreignId('to_shop_id')->constrained('shops'); // Foreign key to the destination shop
            $table->integer('quantity'); // Quantity being transferred
            $table->decimal('cost_price', 10, 2); // Cost price of the product
            $table->decimal('selling_price', 10, 2); // Selling price of the product
            $table->timestamps(); // To store created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
}
