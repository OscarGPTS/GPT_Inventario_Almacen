<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique(); // TSL0160053
            $table->foreignId('componente_id')->constrained('componentes');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('familia_id')->constrained('familias');
            $table->string('consecutivo', 10); // 0053
            $table->text('descripcion');
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida');
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones');
            $table->integer('cantidad_entrada')->default(0);
            $table->integer('cantidad_salida')->default(0);
            $table->integer('cantidad_fisica')->default(0);
            $table->date('fecha_entrada')->nullable();
            $table->date('fecha_salida')->nullable();
            $table->decimal('precio_unitario', 12, 2)->nullable();
            $table->enum('moneda', ['MXN', 'USD'])->default('MXN');
            $table->string('factura', 50)->nullable();
            $table->text('observaciones')->nullable(); // DN/NP/Observaciones
            $table->date('fecha_vencimiento')->nullable();
            $table->string('hoja_seguridad', 255)->nullable(); // Puede ser URL o nombre de archivo
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
