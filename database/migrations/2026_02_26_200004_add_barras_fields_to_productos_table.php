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
        Schema::table('productos', function (Blueprint $table) {
            $table->string('numero_requisicion', 50)->nullable()->after('codigo');
            $table->string('numero_parte', 100)->nullable()->after('numero_requisicion');
            $table->string('dimensiones', 100)->nullable()->after('numero_parte');
            $table->string('orden_compra', 50)->nullable()->after('factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['numero_requisicion', 'numero_parte', 'dimensiones', 'orden_compra']);
        });
    }
};
