<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'producto_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'work_evidence',
        'assigned_at',
        'completed_at',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_PENDIENTE = 'pendiente';
    const STATUS_EN_PROCESO = 'en_proceso';
    const STATUS_FINALIZADO = 'finalizado';
    const STATUS_CANCELADO = 'cancelado';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function images()
    {
        return $this->hasMany(TicketImage::class);
    }

    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    public function solicitudImages()
    {
        return $this->hasMany(TicketImage::class)->where('type', 'solicitud');
    }

    public function evidenciaImages()
    {
        return $this->hasMany(TicketImage::class)->where('type', 'evidencia');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    public function scopeEnProceso($query)
    {
        return $query->where('status', self::STATUS_EN_PROCESO);
    }

    public function scopeFinalizados($query)
    {
        return $query->where('status', self::STATUS_FINALIZADO);
    }

    // State helpers
    public function isPendiente(): bool
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function isEnProceso(): bool
    {
        return $this->status === self::STATUS_EN_PROCESO;
    }

    public function isFinalizado(): bool
    {
        return $this->status === self::STATUS_FINALIZADO;
    }

    public function isCancelado(): bool
    {
        return $this->status === self::STATUS_CANCELADO;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDIENTE, self::STATUS_EN_PROCESO]);
    }

    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => self::STATUS_EN_PROCESO,
            'assigned_at' => now(),
        ]);
    }

    public function complete(string $evidence = null): void
    {
        $this->update([
            'status' => self::STATUS_FINALIZADO,
            'work_evidence' => $evidence,
            'completed_at' => now(),
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELADO,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDIENTE => 'Pendiente',
            self::STATUS_EN_PROCESO => 'En Proceso',
            self::STATUS_FINALIZADO => 'Finalizado',
            self::STATUS_CANCELADO => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDIENTE => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::STATUS_EN_PROCESO => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_FINALIZADO => 'bg-green-100 text-green-800 border-green-200',
            self::STATUS_CANCELADO => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getFormattedCodeAttribute(): string
    {
        $year = $this->created_at?->format('Y') ?? now()->format('Y');
        return $year . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
