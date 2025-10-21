<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable(); // if multi-shop system
            $table->string('title');                           // e.g., Fuel, Maintenance
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('date')->default(now());
            $table->string('added_by')->nullable();             // store cashier or admin name
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
