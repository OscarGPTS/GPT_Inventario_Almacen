<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->foreignId('ticket_id')->nullable()->after('solicitud_id')->constrained('tickets')->nullOnDelete();
            $table->string('fuente', 50)->nullable()->after('referencia')
                  ->comment('manual | excel | json | barras | solicitud_material | solicitud_movimiento');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropColumn(['ticket_id', 'fuente']);
        });
    }
};
