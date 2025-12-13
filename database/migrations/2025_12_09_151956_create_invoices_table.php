<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // creator
            $table->foreignId('shop_id')->constrained()->onDelete('cascade'); // shop

            $table->string('invoice_number')->unique();
            $table->date('invoice_date')->default(now());

            $table->text('goods'); // JSON: items list

            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // ⭐ NEW PAYMENT FIELDS ⭐
            $table->enum('payment_type', ['full', 'part'])->default('full');   // full or part payment
            $table->decimal('amount_paid', 10, 2)->default(0);                 // how much customer paid
            $table->decimal('balance', 10, 2)->default(0);                     // total - paid
            $table->enum('payment_status', ['paid', 'owing'])->default('paid');// paid or still owing

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
