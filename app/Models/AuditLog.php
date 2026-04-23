<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar una acción de auditoría.
     */
    public static function registrar(string $action, string $table, ?int $recordId, ?array $old, ?array $new): self
    {
        return self::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'table_name' => $table,
            'record_id'  => $recordId,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
        ]);
    }
}
