<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable(); // ✅ Corrected type
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id')->nullable(); // Optional
            $table->unsignedBigInteger('shop_id')->nullable(); // 🆕 Add shop_id
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'transfer'])->default('cash');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null'); // 🆕 Foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_items');
    }
};
