<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Asignada</title>
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
        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700; background: #dbeafe; color: #1e40af; }
        .cta { text-align: center; margin: 28px 0 8px; }
        .cta a { background-color: #4A568D; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Solicitud Asignada</h1>
        <p>Tu solicitud de movimiento ha sido asignada a un almacenista. Ticket <strong>#{{ $ticket->formatted_code }}</strong></p>
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
                <span class="info-label">Estado</span>
                <span class="info-value"><span class="status-badge">En Proceso</span></span>
            </div>
        </div>

        <hr class="divider">

        <div class="section-title">Asignación</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Asignado a</span>
                <span class="info-value">{{ $ticket->assignedTo->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha asignación</span>
                <span class="info-value">{{ $ticket->assigned_at?->format('d/m/Y H:i') ?? '—' }}</span>
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

        <div class="cta">
            <a href="{{ url('/tickets/' . $ticket->id) }}">Ver solicitud en el sistema</a>
        </div>

    </div>

    <div class="footer">
        © {{ date('Y') }} Sistema de Inventario de Almacén — Este mensaje fue generado automáticamente.
    </div>
</div>
</body>
</html>
