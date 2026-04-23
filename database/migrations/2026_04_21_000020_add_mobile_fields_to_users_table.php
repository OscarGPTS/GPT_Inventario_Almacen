<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_id')->nullable()->after('google_id')->comment('Firebase UID para autenticación móvil');
            $table->string('provider')->nullable()->after('provider_id')->comment('Proveedor de auth: google, firebase, etc.');
            $table->timestamp('last_login_at')->nullable()->after('provider');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider_id', 'provider', 'last_login_at']);
        });
    }
};
