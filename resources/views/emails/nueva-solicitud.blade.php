<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Material</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.10); }
        .header { background-color: #4A568D; color: #ffffff; padding: 28px 32px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: .85; }
        .body { padding: 28px 32px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; margin: 22px 0 10px; }
        .info-grid { display: table; width: 100%; border-collapse: collapse; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 38%; padding: 7px 10px 7px 0; font-size: 13px; color: #6b7280; vertical-align: top; }
        .info-value { display: table-cell; padding: 7px 0; font-size: 13px; color: #111827; font-weight: 600; vertical-align: top; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 20px 0; }
        .priority-badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700; }
        .priority-alta    { background:#fee2e2; color:#b91c1c; }
        .priority-media   { background:#fef3c7; color:#b45309; }
        .priority-baja    { background:#dcfce7; color:#15803d; }
        .observaciones-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; font-size: 13px; color: #374151; margin-top: 4px; }
        .cta { text-align: center; margin: 28px 0 8px; }
        .cta a { background-color: #4A568D; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    {{-- Header --}}
    <div class="header">
        <h1>Nueva Solicitud de Material</h1>
        <p>
            Se ha registrado una nueva solicitud en el sistema de Inventario de Almacén.
            @if($solicitud->folio) Folio: <strong>{{ $solicitud->folio }}</strong> @endif
        </p>
    </div>

    {{-- Body --}}
    <div class="body">

        {{-- Solicitante --}}
        <div class="section-title">Datos del Solicitante</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Solicitante</span>
                <span class="info-value">{{ $solicitud->solicitante }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Departamento</span>
                <span class="info-value">{{ $solicitud->departamento->nombre ?? '—' }}</span>
            </div>
            @if($solicitud->usuarioRegistro)
            <div class="info-row">
                <span class="info-label">Registrado por</span>
                <span class="info-value">{{ $solicitud->usuarioRegistro->name }} ({{ $solicitud->usuarioRegistro->email }})</span>
            </div>
            @endif
        </div>

        <hr class="divider">

        {{-- Material --}}
        <div class="section-title">Material Solicitado</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Código</span>
                <span class="info-value">{{ $solicitud->producto->codigo ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Descripción</span>
                <span class="info-value">{{ $solicitud->producto->descripcion ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cantidad</span>
                <span class="info-value">
                    {{ number_format($solicitud->cantidad, 2) }}
                    {{ $solicitud->unidadMedida->nombre ?? '' }}
                </span>
            </div>
        </div>

        <hr class="divider">

        {{-- Fechas y prioridad --}}
        <div class="section-title">Fechas y Prioridad</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Fecha solicitud</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($solicitud->fecha)->format('d/m/Y') }}</span>
            </div>
            @if($solicitud->fecha_requerida)
            <div class="info-row">
                <span class="info-label">Fecha requerida</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($solicitud->fecha_requerida)->format('d/m/Y') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Prioridad</span>
                <span class="info-value">
                    <span class="priority-badge priority-{{ $solicitud->prioridad }}">
                        {{ ucfirst($solicitud->prioridad) }}
                    </span>
                </span>
            </div>
        </div>

        {{-- Observaciones --}}
        @if($solicitud->observaciones)
        <hr class="divider">
        <div class="section-title">Observaciones</div>
        <div class="observaciones-box">{{ $solicitud->observaciones }}</div>
        @endif

        {{-- CTA --}}
        <div class="cta">
            <a href="{{ url('/reportes/requisiciones') }}">Ver requisiciones en el sistema</a>
        </div>

    </div>

    <div class="footer">
        © {{ date('Y') }} Sistema de Inventario de Almacén — Este mensaje fue generado automáticamente.
    </div>
</div>
</body>
</html>
