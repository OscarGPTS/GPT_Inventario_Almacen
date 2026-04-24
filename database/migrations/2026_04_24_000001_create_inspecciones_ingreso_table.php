<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspecciones_ingreso', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->date('fecha_recepcion')->nullable();
            $table->string('requisicion', 100)->nullable();
            $table->string('orden_compra', 100)->nullable();
            $table->string('tipo_documento', 10)->nullable(); // DN, NP, CP, Otro
            $table->string('tipo_documento_otro', 100)->nullable();
            $table->boolean('requiere_ctrl_calidad')->default(false);
            $table->string('no_solicitud', 100)->nullable();
            $table->date('fecha_ingreso_inventario')->nullable();

            // Solicitante
            $table->date('fecha_inspeccion_solicitante')->nullable();
            $table->string('inspeccionado_solicitante', 200)->nullable();
            $table->string('departamento_solicitante', 100)->nullable();
            $table->text('observaciones_solicitante')->nullable();
            $table->string('resultado_solicitante', 20)->nullable(); // no_conforme, conforme, a_revision

            // Control de Calidad
            $table->date('fecha_inspeccion_calidad')->nullable();
            $table->string('inspeccionado_calidad', 200)->nullable();
            $table->string('departamento_calidad', 100)->nullable();
            $table->text('observaciones_calidad')->nullable();
            $table->string('resultado_calidad', 20)->nullable(); // no_conforme, conforme, a_revision

            $table->foreignId('registrado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspecciones_ingreso');
    }
};
