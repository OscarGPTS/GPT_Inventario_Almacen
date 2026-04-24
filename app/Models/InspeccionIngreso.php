<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspeccionIngreso extends Model
{
    protected $table = 'inspecciones_ingreso';

    protected $fillable = [
        'folio',
        'fecha_recepcion',
        'requisicion',
        'orden_compra',
        'tipo_documento',
        'tipo_documento_otro',
        'requiere_ctrl_calidad',
        'no_solicitud',
        'fecha_ingreso_inventario',
        'fecha_inspeccion_solicitante',
        'inspeccionado_solicitante',
        'departamento_solicitante',
        'observaciones_solicitante',
        'resultado_solicitante',
        'fecha_inspeccion_calidad',
        'inspeccionado_calidad',
        'departamento_calidad',
        'observaciones_calidad',
        'resultado_calidad',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_recepcion'              => 'date',
        'fecha_ingreso_inventario'     => 'date',
        'fecha_inspeccion_solicitante' => 'date',
        'fecha_inspeccion_calidad'     => 'date',
        'requiere_ctrl_calidad'        => 'boolean',
    ];

    const RESULTADO_NO_CONFORME = 'no_conforme';
    const RESULTADO_CONFORME    = 'conforme';
    const RESULTADO_A_REVISION  = 'a_revision';

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public static function generarFolio(): string
    {
        $year   = now()->format('Y');
        $ultimo = static::whereYear('created_at', $year)->count();
        return 'INS-' . $year . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getResultadoSolicitanteTextAttribute(): string
    {
        return match ($this->resultado_solicitante) {
            self::RESULTADO_NO_CONFORME => 'No Conforme',
            self::RESULTADO_CONFORME    => 'Conforme',
            self::RESULTADO_A_REVISION  => 'A Revisión',
            default                     => '—',
        };
    }

    public function getResultadoCalidadTextAttribute(): string
    {
        return match ($this->resultado_calidad) {
            self::RESULTADO_NO_CONFORME => 'No Conforme',
            self::RESULTADO_CONFORME    => 'Conforme',
            self::RESULTADO_A_REVISION  => 'A Revisión',
            default                     => '—',
        };
    }

    public function getResultadoBadgeClass(string $resultado = null): string
    {
        return match ($resultado) {
            self::RESULTADO_NO_CONFORME => 'bg-red-100 text-red-800 border border-red-200',
            self::RESULTADO_CONFORME    => 'bg-green-100 text-green-800 border border-green-200',
            self::RESULTADO_A_REVISION  => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            default                     => 'bg-gray-100 text-gray-600 border border-gray-200',
        };
    }
}
