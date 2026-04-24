<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>{{ $inspeccion->folio }} – Inspección de Ingreso</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 8.5pt;
        color: #111827;
        background: #fff;
    }

    /* ── Utilidades ── */
    .text-center { text-align: center; }
    .text-right  { text-align: right; }
    .font-bold   { font-weight: bold; }
    .text-white  { color: #ffffff; }
    .text-gray   { color: #6B7280; }
    .text-small  { font-size: 7.5pt; }
    .uppercase   { text-transform: uppercase; }
    .spacer      { height: 6pt; }

    /* ── Layout general ── */
    .page {
        width: 100%;
        padding: 12pt 14pt;
    }

    /* ── Tabla base para secciones ── */
    table { width: 100%; border-collapse: collapse; }
    td, th { padding: 4pt 6pt; vertical-align: middle; }

    /* ── Encabezado institucional ── */
    .hdr-outer { border: 1.2pt solid #9CA3AF; }
    .hdr-logo {
        width: 58pt;
        text-align: center;
        border-right: 1pt solid #9CA3AF;
        vertical-align: middle;
        background: #F9FAFB;
    }
    .hdr-logo img { width: 46pt; }
    .hdr-logo .logo-text { font-size: 10pt; font-weight: bold; color: #D97706; line-height: 1.1; }
    .hdr-logo .logo-sub  { font-size: 6.5pt; color: #6B7280; }

    .hdr-empresa {
        text-align: center;
        font-weight: bold;
        font-size: 8pt;
        text-transform: uppercase;
        color: #374151;
        background: #F3F4F9;
        padding: 3pt 6pt;
        border-bottom: 1pt solid #D1D5DB;
    }
    .hdr-titulo {
        text-align: center;
        font-weight: bold;
        font-size: 9.5pt;
        text-transform: uppercase;
        color: #fff;
        background: #4A568D;
        padding: 4pt 6pt;
        border-bottom: 1pt solid #9CA3AF;
    }
    .hdr-meta td {
        font-size: 7pt;
        padding: 2pt 5pt;
        border-right: 1pt solid #D1D5DB;
        background: #fff;
    }
    .hdr-meta .lbl { color: #9CA3AF; font-weight: bold; text-transform: uppercase; display: block; }
    .hdr-meta .val { color: #111827; font-weight: bold; font-size: 7.5pt; }

    /* ── Sección datos generales ── */
    .section { border: 1pt solid #9CA3AF; margin-top: 6pt; }
    .section-header td {
        font-size: 7.5pt;
        font-weight: bold;
        text-transform: uppercase;
        background: #F3F4F9;
        color: #6B7280;
        border-bottom: 1pt solid #D1D5DB;
        border-right: 1pt solid #D1D5DB;
    }
    .section-body td {
        background: #fff;
        border-right: 1pt solid #D1D5DB;
        font-size: 8.5pt;
    }

    /* ── Columnas Solicitante / Calidad ── */
    .col-header-sol {
        text-align: center;
        font-weight: bold;
        font-size: 9pt;
        text-transform: uppercase;
        color: #fff;
        background: #3A8FC0;
        padding: 4pt 6pt;
    }
    .col-header-cal {
        text-align: center;
        font-weight: bold;
        font-size: 9pt;
        text-transform: uppercase;
        color: #fff;
        background: #4E8030;
        padding: 4pt 6pt;
    }

    .col-sol { background: #EBF5FB; border-right: 1pt solid #D1D5DB; }
    .col-cal { background: #EBF5EB; }
    .col-lbl { font-weight: bold; font-size: 7.5pt; text-transform: uppercase; color: #6B7280; }
    .col-val { font-size: 8.5pt; color: #111827; }
    .col-obs {
        font-size: 8.5pt;
        color: #111827;
        background: #fff;
        padding: 5pt 6pt;
        min-height: 36pt;
        vertical-align: top;
    }
    .two-cols { border: 1pt solid #9CA3AF; margin-top: 6pt; border-collapse: collapse; }
    .two-cols td { padding: 3pt 6pt; vertical-align: top; }
    .two-cols .inner-lbl {
        font-size: 7.5pt; font-weight: bold; text-transform: uppercase;
        color: #6B7280; padding: 2pt 6pt; border-bottom: 0.5pt solid #D1D5DB;
    }
    .two-cols .inner-val { padding: 3pt 6pt; font-size: 8.5pt; }

    /* ── Sección inferior ── */
    .bottom-section { border: 1pt solid #9CA3AF; margin-top: 6pt; }
    .bottom-lbl {
        font-size: 7.5pt; font-weight: bold; text-transform: uppercase;
        color: #6B7280; background: #F3F4F9;
        border-right: 1pt solid #D1D5DB;
        border-bottom: 1pt solid #D1D5DB;
    }
    .bottom-val {
        font-size: 8.5pt; background: #fff;
        border-right: 1pt solid #D1D5DB;
    }

    .ctrl-si  { background: #DBEAFE; color: #1E3A8A; font-weight: bold; text-align: center; font-size: 9pt; }
    .ctrl-no  { background: #F3F4F6; color: #4B5563; font-weight: bold; text-align: center; font-size: 9pt; }

    /* ── Resultados ── */
    .results-section { border: 1pt solid #9CA3AF; margin-top: 6pt; }
    .results-hdr {
        font-size: 7.5pt; font-weight: bold; text-transform: uppercase;
        color: #374151; background: #F3F4F9;
        text-align: center;
        border-bottom: 1pt solid #D1D5DB;
        padding: 3pt 6pt;
    }
    .result-box {
        text-align: center;
        font-weight: bold;
        font-size: 10pt;
        color: #fff;
        padding: 7pt 6pt;
    }
    .res-no-conf { background: #DC2626; }
    .res-conf    { background: #16A34A; }
    .res-rev     { background: #EAB308; }
    .res-none    { background: #D1D5DB; color: #6B7280; font-size: 8pt; }

    /* ── Pie ── */
    .footer {
        margin-top: 6pt;
        border-top: 1pt solid #D1D5DB;
        padding-top: 3pt;
        font-size: 7pt;
        color: #9CA3AF;
    }
</style>
</head>
<body>
<div class="page">

    {{-- ═══════════════════════════════ ENCABEZADO ═══════════════════════════════ --}}
    <table class="hdr-outer" style="border-collapse:collapse;">
        <tr>
            {{-- Logo --}}
            <td class="hdr-logo" rowspan="3" style="width:60pt;">
                @if(file_exists(public_path('storage/img/logo_gpt.svg')))
                    <img src="{{ public_path('storage/img/logo_gpt.svg') }}" alt="GPT">
                @else
                    <div class="logo-text">GPT</div>
                    <div class="logo-sub">SERVICES</div>
                @endif
            </td>
            {{-- Nombre empresa --}}
            <td colspan="6" class="hdr-empresa">TECH ENERGY CONTROL S.A. DE C.V.</td>
        </tr>
        <tr>
            <td colspan="6" class="hdr-titulo">INSPECCIONES PARA INGRESO A INVENTARIO</td>
        </tr>
        <tr class="hdr-meta">
            <td style="width:14%"><span class="lbl">Tipo Documento</span><span class="val">Formato</span></td>
            <td style="width:10%"><span class="lbl">Revisión</span><span class="val">0</span></td>
            <td style="width:16%"><span class="lbl">Fecha Aprobación</span><span class="val">Nov-25</span></td>
            <td style="width:14%"><span class="lbl">Departamento</span><span class="val">Almacén</span></td>
            <td style="width:18%; border-right:none;"><span class="lbl">Código</span><span class="val">FO-GPT-ALM-01-E</span></td>
            <td style="width:10%; border-right:none;"><span class="lbl">Página</span><span class="val">1 de 1</span></td>
        </tr>
    </table>

    {{-- ═══════════════════════════════ DATOS GENERALES ═══════════════════════════════ --}}
    <table class="section" style="margin-top:6pt;">
        <tr class="section-header">
            <td style="width:25%">Fecha de recepción</td>
            <td style="width:25%">Requisición</td>
            <td style="width:25%">O.C.</td>
            <td style="width:25%; border-right:none;">DN / NP / CP / Otro</td>
        </tr>
        <tr class="section-body">
            <td>{{ $inspeccion->fecha_recepcion?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $inspeccion->requisicion ?? '—' }}</td>
            <td>{{ $inspeccion->orden_compra ?? '—' }}</td>
            <td style="border-right:none;">
                {{ $inspeccion->tipo_documento === 'Otro'
                    ? ($inspeccion->tipo_documento_otro ?? 'Otro')
                    : ($inspeccion->tipo_documento ?? '—') }}
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════ DOS COLUMNAS ═══════════════════════════════ --}}
    <table class="two-cols" style="margin-top:6pt; table-layout:fixed;">
        {{-- Cabeceras --}}
        <tr>
            <td colspan="2" class="col-header-sol" style="width:50%; border-right:1pt solid #9CA3AF;">Solicitante</td>
            <td colspan="2" class="col-header-cal" style="width:50%;">Control de Calidad</td>
        </tr>
        {{-- Fecha inspección --}}
        <tr>
            <td class="inner-lbl col-sol" style="width:25%; border-right:1pt solid #D1D5DB; border-bottom:1pt solid #D1D5DB;">Fecha de inspección</td>
            <td class="inner-val" style="width:25%; border-right:1pt solid #9CA3AF; border-bottom:1pt solid #D1D5DB;">{{ $inspeccion->fecha_inspeccion_solicitante?->format('d/m/Y') ?? '—' }}</td>
            <td class="inner-lbl col-cal" style="width:25%; border-right:1pt solid #D1D5DB; border-bottom:1pt solid #D1D5DB;">Fecha de inspección</td>
            <td class="inner-val" style="width:25%; border-bottom:1pt solid #D1D5DB;">{{ $inspeccion->fecha_inspeccion_calidad?->format('d/m/Y') ?? '—' }}</td>
        </tr>
        {{-- Inspeccionó --}}
        <tr>
            <td class="inner-lbl col-sol" style="border-right:1pt solid #D1D5DB; border-bottom:1pt solid #D1D5DB;">Inspeccionó (Nombre)</td>
            <td class="inner-val" style="border-right:1pt solid #9CA3AF; border-bottom:1pt solid #D1D5DB;">{{ $inspeccion->inspeccionado_solicitante ?? '—' }}</td>
            <td class="inner-lbl col-cal" style="border-right:1pt solid #D1D5DB; border-bottom:1pt solid #D1D5DB;">Inspeccionó (Nombre)</td>
            <td class="inner-val" style="border-bottom:1pt solid #D1D5DB;">{{ $inspeccion->inspeccionado_calidad ?? '—' }}</td>
        </tr>
        {{-- Departamento --}}
        <tr>
            <td class="inner-lbl col-sol" style="border-right:1pt solid #D1D5DB; border-bottom:1pt solid #D1D5DB;">Departamento</td>
            <td class="inner-val" style="border-right:1pt solid #9CA3AF; border-bottom:1pt solid #D1D5DB;">{{ $inspeccion->departamento_solicitante ?? '—' }}</td>
            <td class="inner-lbl col-cal" style="border-right:1pt solid #D1D5DB; border-bottom:1pt solid #D1D5DB;">Departamento</td>
            <td class="inner-val" style="border-bottom:1pt solid #D1D5DB;">{{ $inspeccion->departamento_calidad ?? '—' }}</td>
        </tr>
        {{-- Observaciones --}}
        <tr>
            <td class="inner-lbl col-sol" style="border-right:1pt solid #D1D5DB; vertical-align:top;">Observaciones</td>
            <td class="col-obs" style="border-right:1pt solid #9CA3AF; min-height:40pt;">{{ $inspeccion->observaciones_solicitante ?? '—' }}</td>
            <td class="inner-lbl col-cal" style="border-right:1pt solid #D1D5DB; vertical-align:top;">Observaciones</td>
            <td class="col-obs" style="min-height:40pt;">{{ $inspeccion->observaciones_calidad ?? '—' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════ SECCIÓN INFERIOR ═══════════════════════════════ --}}
    <table class="bottom-section" style="margin-top:6pt; table-layout:fixed;">
        <tr>
            <td class="bottom-lbl" style="width:25%;">Requiere Ctrl. Calidad</td>
            <td class="bottom-lbl" style="width:25%;">No. Solicitud</td>
            <td class="bottom-lbl" colspan="2" style="width:50%; border-right:none;">Fecha de ingreso a inventario</td>
        </tr>
        <tr>
            <td class="bottom-val {{ $inspeccion->requiere_ctrl_calidad ? 'ctrl-si' : 'ctrl-no' }}">
                {{ $inspeccion->requiere_ctrl_calidad ? 'SÍ' : 'NO' }}
            </td>
            <td class="bottom-val">{{ $inspeccion->no_solicitud ?? '—' }}</td>
            <td class="bottom-val" colspan="2" style="border-right:none; text-align:center;">
                {{ $inspeccion->fecha_ingreso_inventario?->format('d/m/Y') ?? '—' }}
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════ RESULTADOS ═══════════════════════════════ --}}
    <table class="results-section" style="margin-top:6pt; table-layout:fixed;">
        <tr>
            <td colspan="2" class="results-hdr" style="width:50%; border-right:1pt solid #9CA3AF;">
                Resultado de Inspecciones — Solicitante
            </td>
            <td colspan="2" class="results-hdr" style="width:50%;">
                Resultado de Inspecciones — Calidad
            </td>
        </tr>
        <tr>
            {{-- Resultado Solicitante --}}
            <td colspan="2" style="width:50%; padding:5pt; border-right:1pt solid #9CA3AF;">
                @php
                    $rsSol = $inspeccion->resultado_solicitante;
                    $classSol = match($rsSol) {
                        'no_conforme' => 'res-no-conf',
                        'conforme'    => 'res-conf',
                        'a_revision'  => 'res-rev',
                        default       => 'res-none',
                    };
                    $lblSol = match($rsSol) {
                        'no_conforme' => 'NO CONFORME',
                        'conforme'    => 'CONFORME',
                        'a_revision'  => 'A REVISIÓN',
                        default       => 'SIN RESULTADO',
                    };
                @endphp
                <div class="result-box {{ $classSol }}">{{ $lblSol }}</div>
            </td>
            {{-- Resultado Calidad --}}
            <td colspan="2" style="width:50%; padding:5pt;">
                @php
                    $rsCal = $inspeccion->resultado_calidad;
                    $classCal = match($rsCal) {
                        'no_conforme' => 'res-no-conf',
                        'conforme'    => 'res-conf',
                        'a_revision'  => 'res-rev',
                        default       => 'res-none',
                    };
                    $lblCal = match($rsCal) {
                        'no_conforme' => 'NO CONFORME',
                        'conforme'    => 'CONFORME',
                        'a_revision'  => 'A REVISIÓN',
                        default       => 'SIN RESULTADO',
                    };
                @endphp
                <div class="result-box {{ $classCal }}">{{ $lblCal }}</div>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════ PIE ═══════════════════════════════ --}}
    <table class="footer" style="margin-top:8pt;">
        <tr>
            <td>Folio: <strong>{{ $inspeccion->folio }}</strong>
                &nbsp;·&nbsp; Registrado por: {{ $inspeccion->registradoPor?->name ?? '—' }}
                &nbsp;·&nbsp; Fecha: {{ $inspeccion->created_at->format('d/m/Y H:i') }}
            </td>
            <td class="text-right">GPT Services · Tech Energy Control S.A. de C.V.</td>
        </tr>
    </table>

</div>
</body>
</html>
