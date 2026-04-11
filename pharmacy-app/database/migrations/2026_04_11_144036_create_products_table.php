<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 30)->unique();
            $table->string('name', 100);
            $table->string('description', 500)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('price', 10, 2);
            $table->date('expiration_date')->nullable();
            $table->string('presentation', 50)->nullable();
            $table->string('administration_form', 50)->nullable();
            $table->string('storage', 100)->nullable();
            $table->string('packaging', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('expiration_date');
            $table->index('stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
