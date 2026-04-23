<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_conformidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->decimal('cantidad', 12, 2)->default(0)->comment('Cantidad afectada');
            $table->enum('estatus', ['abierta', 'en_proceso', 'resuelta', 'cerrada'])->default('abierta');
            $table->text('descripcion')->comment('Descripción de la no conformidad');
            $table->text('resolucion')->nullable()->comment('Descripción de la resolución aplicada');
            $table->date('fecha_deteccion')->comment('Fecha en que se detectó la no conformidad');
            $table->date('fecha_resolucion')->nullable()->comment('Fecha en que se resolvió');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resuelto_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_conformidades');
    }
};
