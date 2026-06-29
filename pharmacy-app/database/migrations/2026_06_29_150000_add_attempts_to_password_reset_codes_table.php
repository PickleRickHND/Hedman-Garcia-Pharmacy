<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_codes', function (Blueprint $table) {
            // Intentos fallidos de verificación por código: tras N el código se invalida,
            // acotando la fuerza bruta del OTP independientemente del rate-limit por IP.
            $table->unsignedTinyInteger('attempts')->default(0)->after('code_hash');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_codes', function (Blueprint $table) {
            $table->dropColumn('attempts');
        });
    }
};
