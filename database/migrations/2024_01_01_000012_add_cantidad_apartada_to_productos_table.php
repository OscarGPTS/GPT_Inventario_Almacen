<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('cantidad_apartada', 12, 2)->default(0)->after('cantidad_fisica')
                ->comment('Cantidad reservada por solicitudes aprobadas pendientes de entrega');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('cantidad_apartada');
        });
    }
};
