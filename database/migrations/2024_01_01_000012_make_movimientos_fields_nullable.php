<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->integer('cantidad')->nullable()->change();
            $table->integer('cantidad_anterior')->nullable()->change();
            $table->integer('cantidad_nueva')->nullable()->change();
            $table->foreignId('usuario_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->integer('cantidad')->nullable(false)->change();
            $table->integer('cantidad_anterior')->nullable(false)->change();
            $table->integer('cantidad_nueva')->nullable(false)->change();
            $table->foreignId('usuario_id')->nullable(false)->change();
        });
    }
};
