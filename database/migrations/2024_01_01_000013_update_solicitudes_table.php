<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            // Hacer folio nullable y eliminar restricción unique
            $table->dropUnique('solicitudes_folio_unique');
            $table->string('folio', 50)->nullable()->change();

            // Prioridad de la solicitud
            $table->enum('prioridad', ['urgente', 'alta', 'normal', 'baja'])
                  ->default('normal')
                  ->after('estado');

            // Fecha en que se requiere el material
            $table->date('fecha_requerida')->nullable()->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('folio', 50)->nullable(false)->change();
            $table->unique('folio');
            $table->dropColumn(['prioridad', 'fecha_requerida']);
        });
    }
};
