<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Completada</title>
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
        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700; background: #dcfce7; color: #15803d; }
        .observaciones-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; font-size: 13px; color: #374151; margin-top: 4px; }
        .survey-box { background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; padding: 20px; margin: 24px 0; text-align: center; }
        .survey-box h3 { margin: 0 0 8px; font-size: 15px; color: #92400e; }
        .survey-box p { margin: 0 0 16px; font-size: 13px; color: #78350f; }
        .survey-btn { background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .cta { text-align: center; margin: 28px 0 8px; }
        .cta a { background-color: #4A568D; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>¡Solicitud Completada!</h1>
        <p>Tu solicitud de movimiento ha sido atendida exitosamente. Ticket <strong>#{{ $ticket->formatted_code }}</strong></p>
    </div>

    <div class="body">

        <div class="section-title">Información de la Solicitud</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Ticket</span>
                <span class="info-value">#{{ $ticket->formatted_code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Título</span>
                <span class="info-value">{{ $ticket->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Procesado por</span>
                <span class="info-value">{{ $ticket->assignedTo->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Completado</span>
                <span class="info-value">{{ $ticket->completed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado</span>
                <span class="info-value"><span class="status-badge">Completado</span></span>
            </div>
        </div>

        @if($ticket->producto)
        <hr class="divider">
        <div class="section-title">Producto Relacionado</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Código</span>
                <span class="info-value">{{ $ticket->producto->codigo }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Descripción</span>
                <span class="info-value">{{ $ticket->producto->descripcion }}</span>
            </div>
        </div>
        @endif

        @if($ticket->work_evidence)
        <hr class="divider">
        <div class="section-title">Evidencia del Trabajo</div>
        <div class="observaciones-box">{{ $ticket->work_evidence }}</div>
        @endif

        <div class="survey-box">
            <h3>📋 Tu opinión es importante</h3>
            <p>Por favor, califica el servicio recibido para ayudarnos a mejorar.</p>
            <a href="{{ url('/tickets/' . $ticket->id) }}" class="survey-btn">Calificar Servicio</a>
        </div>

    </div>

    <div class="footer">
        © {{ date('Y') }} Sistema de Inventario de Almacén — Este mensaje fue generado automáticamente.
    </div>
</div>
</body>
</html>
