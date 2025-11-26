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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // Add the store_id column (nullable)
            $table->unsignedBigInteger('shop_id')->nullable(); // ⬅️ Added this

            // Add role column (enum)
            $table->enum('role', ['admin', 'cashier', 'manager'])->default('cashier');

            // Add foreign key constraint for store_id if the stores table exists
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
        Schema::dropIfExists('users');
    }
};
