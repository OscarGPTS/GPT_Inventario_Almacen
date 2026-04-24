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
        Schema::table('inspecciones_ingreso', function (Blueprint $table) {
            $table->unsignedBigInteger('articulo_id')->nullable()->after('folio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspecciones_ingreso', function (Blueprint $table) {
            $table->dropColumn('articulo_id');
        });
    }
};
