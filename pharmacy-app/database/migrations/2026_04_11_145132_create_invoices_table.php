<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 20)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_rtn', 20)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('status', 20)->default('emitted');
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->index('issued_at');
            $table->index('status');
            $table->index('customer_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
