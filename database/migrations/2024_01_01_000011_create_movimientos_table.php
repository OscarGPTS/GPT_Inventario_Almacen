<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('usuario_id')->constrained('users');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'transferencia']);
            $table->integer('cantidad');
            $table->integer('cantidad_anterior');
            $table->integer('cantidad_nueva');
            $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes');
            $table->text('descripcion')->nullable();
            $table->string('referencia', 100)->nullable(); // Factura, folio, etc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
