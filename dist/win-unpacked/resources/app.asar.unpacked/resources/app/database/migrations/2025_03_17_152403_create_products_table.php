<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
     public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();

        // 🔗 Foreign Keys
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('shop_id'); // ⬅️ Existing

        // 📦 Product Fields
        $table->string('name');
        $table->string('barcode')->nullable()->unique(); // ⬅️ Added barcode here
        $table->decimal('price', 10, 2);
        $table->decimal('cost_price', 10, 2);
        $table->integer('stock_quantity')->default(0);
        $table->integer('stock_limit')->nullable()->default(0);
        $table->timestamps();

        // 🔐 Constraints
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
    });
}


    public function down()
    {
        Schema::dropIfExists('products');
    }
};
