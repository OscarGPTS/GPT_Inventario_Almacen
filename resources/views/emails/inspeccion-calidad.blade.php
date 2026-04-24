<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Inspección — Control de Calidad</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.10); }
        .header { background-color: #4E8030; color: #ffffff; padding: 28px 32px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; }
        .header p { margin: 6px 0 0; font-size: 13px; opacity: .85; }
        .body { padding: 28px 32px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; margin: 22px 0 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-row td { padding: 7px 0; font-size: 13px; vertical-align: top; }
        .info-label { width: 38%; color: #6b7280; }
        .info-value { color: #111827; font-weight: 600; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700; background: #dcfce7; color: #15803d; }
        .folio { font-family: monospace; font-size: 18px; font-weight: 800; color: #4A568D; }
        .cta { text-align: center; margin: 28px 0 8px; }
        .cta a { background-color: #4E8030; color: #ffffff; text-decoration: none; padding: 12px 32px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .alert-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #166534; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>Inspección Requiere Control de Calidad</h1>
        <p>Se ha registrado una nueva inspección en el sistema que requiere tu revisión.</p>
    </div>

    <div class="body">

        <div class="alert-box">
            Se requiere que el equipo de <strong>Control de Calidad</strong> complete la Fase 2 de la siguiente inspección.
            Folio: <span class="folio">{{ $inspeccion->folio }}</span>
        </div>

        <div class="section-title">Datos de la Inspección</div>
        <table class="info-grid">
            <tr class="info-row">
                <td class="info-label">Folio</td>
                <td class="info-value">{{ $inspeccion->folio }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Fecha de recepción</td>
                <td class="info-value">{{ $inspeccion->fecha_recepcion?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            @if($inspeccion->articulo)
            <tr class="info-row">
                <td class="info-label">Artículo vinculado</td>
                <td class="info-value">{{ $inspeccion->articulo->codigo }} — {{ $inspeccion->articulo->descripcion }}</td>
            </tr>
            @endif
            <tr class="info-row">
                <td class="info-label">Requisición</td>
                <td class="info-value">{{ $inspeccion->requisicion ?? '—' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Orden de compra</td>
                <td class="info-value">{{ $inspeccion->orden_compra ?? '—' }}</td>
            </tr>
            @if($inspeccion->tipo_documento)
            <tr class="info-row">
                <td class="info-label">Documento</td>
                <td class="info-value">
                    {{ $inspeccion->tipo_documento === 'Otro' ? ($inspeccion->tipo_documento_otro ?? 'Otro') : $inspeccion->tipo_documento }}
                    {{ $inspeccion->numero_documento ? '— ' . $inspeccion->numero_documento : '' }}
                </td>
            </tr>
            @endif
        </table>

        <div class="section-title">Fase 1 — Solicitante</div>
        <table class="info-grid">
            <tr class="info-row">
                <td class="info-label">Inspeccionó</td>
                <td class="info-value">{{ $inspeccion->inspeccionado_solicitante ?? '—' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Departamento</td>
                <td class="info-value">{{ $inspeccion->departamento_solicitante ?? '—' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Fecha inspección</td>
                <td class="info-value">{{ $inspeccion->fecha_inspeccion_solicitante?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Resultado</td>
                <td class="info-value">{{ $inspeccion->resultado_solicitante_text }}</td>
            </tr>
            @if($inspeccion->observaciones_solicitante)
            <tr class="info-row">
                <td class="info-label">Observaciones</td>
                <td class="info-value">{{ $inspeccion->observaciones_solicitante }}</td>
            </tr>
            @endif
        </table>

        <div class="cta">
            <a href="{{ route('inspecciones.edit', $inspeccion) }}">
                Completar Control de Calidad →
            </a>
        </div>

    </div>

    <div class="footer">
        Este correo fue generado automáticamente por el sistema de Inventario de Almacén.<br>
        Tech Energy Control S.A. de C.V.
    </div>
</div>
</body>
</html>
