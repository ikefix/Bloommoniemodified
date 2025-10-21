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
            $table->string('transaction_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);

            // 🆕 Discount columns
            $table->enum('discount_type', ['none', 'percentage', 'flat'])->default('none');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0); // Total discount amount applied

            $table->enum('payment_method', ['cash', 'card', 'transfer'])->default('cash');
            $table->timestamps();

            // 🔗 Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');
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
