<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción</title>
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
        .stars { font-size: 24px; letter-spacing: 4px; }
        .star-filled { color: #f59e0b; }
        .star-empty { color: #d1d5db; }
        .rating-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 16px 0; text-align: center; }
        .rating-number { font-size: 36px; font-weight: 700; color: #4A568D; }
        .observaciones-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; font-size: 13px; color: #374151; margin-top: 4px; }
        .cta { text-align: center; margin: 28px 0 8px; }
        .cta a { background-color: #4A568D; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Encuesta de Satisfacción</h1>
        <p>Se ha recibido una calificación para el ticket <strong>#{{ $ticket->formatted_code }}</strong></p>
    </div>

    <div class="body">

        <div class="rating-box">
            <div class="rating-number">{{ $rating }}/5</div>
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= $rating ? 'star-filled' : 'star-empty' }}">★</span>
                @endfor
            </div>
        </div>

        <div class="section-title">Información del Ticket</div>
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
                <span class="info-label">Solicitante</span>
                <span class="info-value">{{ $ticket->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Procesado por</span>
                <span class="info-value">{{ $ticket->assignedTo->name ?? '—' }}</span>
            </div>
        </div>

        @if($comments)
        <hr class="divider">
        <div class="section-title">Comentarios del Solicitante</div>
        <div class="observaciones-box">{{ $comments }}</div>
        @endif

        <div class="cta">
            <a href="{{ url('/tickets/' . $ticket->id) }}">Ver ticket en el sistema</a>
        </div>

    </div>

    <div class="footer">
        © {{ date('Y') }} Sistema de Inventario de Almacén — Este mensaje fue generado automáticamente.
    </div>
</div>
</body>
</html>
