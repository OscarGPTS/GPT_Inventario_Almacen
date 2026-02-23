<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Flag para identificar productos con incidencia (No Conforme)
            $table->boolean('no_conforme')->default(false)->after('hoja_seguridad');

            // Observación específica de la incidencia (separada de observaciones = DN/NP)
            $table->text('observacion_nc')->nullable()->after('no_conforme')
                  ->comment('Estatus o descripción de la no conformidad');

            // Fecha en que se identificó la incidencia
            $table->date('fecha_nc')->nullable()->after('observacion_nc')
                  ->comment('Fecha en que se registró la no conformidad');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['no_conforme', 'observacion_nc', 'fecha_nc']);
        });
    }
};
