<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique();
            $table->date('fecha');
            $table->string('solicitante', 100);
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->foreignId('producto_id')->constrained('productos');
            $table->integer('cantidad');
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida');
            $table->text('observaciones')->nullable(); // DN/NP
            $table->enum('estado', ['pendiente', 'aprobada', 'entregada', 'cancelada'])->default('pendiente');
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
