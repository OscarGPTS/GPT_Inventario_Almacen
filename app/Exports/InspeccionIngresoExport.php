<?php

namespace App\Exports;

use App\Models\InspeccionIngreso;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class InspeccionIngresoExport implements WithEvents, WithTitle
{
    public function __construct(private InspeccionIngreso $inspeccion) {}

    public function title(): string
    {
        return $this->inspeccion->folio;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $ins   = $this->inspeccion;

                // ── Tamaños de columna ──────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(28);
                $sheet->getColumnDimension('B')->setWidth(32);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(32);

                // ── Colores constantes ──────────────────────────────────────
                $cMarca    = '4A568D';
                $cSol      = '3A8FC0';
                $cCal      = '4E8030';
                $cHdrLight = 'F3F4F9';
                $cBlueBg   = 'EBF5FB';
                $cGreenBg  = 'EBF5EB';
                $cNoConf   = 'DC2626';
                $cConf     = '16A34A';
                $cRevision = 'EAB308';

                // Helper: celda con texto, bold, color fondo, color texto, alineación
                $set = function (string $cell, mixed $value, bool $bold = false,
                                  string $bg = '', string $fg = '000000',
                                  string $align = 'left', int $wrapRows = 1) use ($sheet) {
                    $sheet->setCellValue($cell, $value);
                    $style = $sheet->getStyle($cell);
                    $style->getFont()->setBold($bold)->setSize(9);
                    $style->getAlignment()
                          ->setHorizontal($align === 'center'
                              ? Alignment::HORIZONTAL_CENTER
                              : Alignment::HORIZONTAL_LEFT)
                          ->setVertical(Alignment::VERTICAL_CENTER)
                          ->setWrapText($wrapRows > 1);
                    if ($bg) {
                        $style->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setRGB($bg);
                    }
                    if ($fg !== '000000') {
                        $style->getFont()->getColor()->setRGB($fg);
                    }
                };

                $border = function (string $range, string $color = 'D1D5DB') use ($sheet) {
                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => $color],
                            ],
                        ],
                    ]);
                };

                $outline = function (string $range, string $color = '9CA3AF') use ($sheet) {
                    $sheet->getStyle($range)->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color'       => ['rgb' => $color],
                            ],
                        ],
                    ]);
                };

                // ════════════════════════════════════════════════════════════
                // BLOQUE 1 – Encabezado institucional
                // ════════════════════════════════════════════════════════════
                $sheet->getRowDimension(1)->setRowHeight(14);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(14);
                $sheet->getRowDimension(4)->setRowHeight(14);

                // Fila 1 – nombre empresa (A1:D1)
                $sheet->mergeCells('A1:D1');
                $set('A1', 'TECH ENERGY CONTROL S.A. DE C.V.', true, $cHdrLight, '374151', 'center');

                // Fila 2 – título formato (A2:D2)
                $sheet->mergeCells('A2:D2');
                $set('A2', 'INSPECCIONES PARA INGRESO A INVENTARIO', true, $cMarca, 'FFFFFF', 'center');
                $sheet->getStyle('A2')->getFont()->setSize(11);

                // Fila 3 – cabeceras de meta
                foreach (['A3' => 'TIPO DE DOCUMENTO', 'B3' => 'REVISIÓN / FECHA APROB.',
                          'C3' => 'DEPARTAMENTO', 'D3' => 'CÓDIGO / PÁGINA'] as $cell => $txt) {
                    $set($cell, $txt, true, $cHdrLight, '6B7280', 'center');
                }

                // Fila 4 – valores de meta
                $set('A4', 'Formato', false, 'FFFFFF', '111827', 'center');
                $set('B4', '0  /  Nov-25', false, 'FFFFFF', '111827', 'center');
                $set('C4', 'Almacén', false, 'FFFFFF', '111827', 'center');
                $set('D4', 'FO-GPT-ALM-01-E  /  1 de 1', false, 'FFFFFF', '111827', 'center');

                $border('A1:D4');
                $outline('A1:D4', '9CA3AF');

                // ════════════════════════════════════════════════════════════
                // BLOQUE 2 – Datos generales
                // ════════════════════════════════════════════════════════════
                $sheet->getRowDimension(5)->setRowHeight(6);   // spacer
                $sheet->getRowDimension(6)->setRowHeight(14);
                $sheet->getRowDimension(7)->setRowHeight(16);

                // Etiquetas
                $set('A6', 'FECHA DE RECEPCIÓN', true, $cHdrLight, '6B7280');
                $set('B6', 'REQUISICIÓN', true, $cHdrLight, '6B7280');
                $set('C6', 'O.C.', true, $cHdrLight, '6B7280');
                $set('D6', 'DN / NP / CP / OTRO', true, $cHdrLight, '6B7280');

                // Valores
                $set('A7', $ins->fecha_recepcion?->format('d/m/Y') ?? '—', false, 'FFFFFF');
                $set('B7', $ins->requisicion ?? '—', false, 'FFFFFF');
                $set('C7', $ins->orden_compra ?? '—', false, 'FFFFFF');
                $tipoDoc = $ins->tipo_documento === 'Otro'
                    ? ($ins->tipo_documento_otro ?? 'Otro')
                    : ($ins->tipo_documento ?? '—');
                $set('D7', $tipoDoc, false, 'FFFFFF');

                $border('A6:D7');
                $outline('A6:D7', '9CA3AF');

                // ════════════════════════════════════════════════════════════
                // BLOQUE 3 – Encabezados de columnas Solicitante / Calidad
                // ════════════════════════════════════════════════════════════
                $sheet->getRowDimension(8)->setRowHeight(6);   // spacer
                $sheet->getRowDimension(9)->setRowHeight(18);

                $sheet->mergeCells('A9:B9');
                $set('A9', 'SOLICITANTE', true, $cSol, 'FFFFFF', 'center');

                $sheet->mergeCells('C9:D9');
                $set('C9', 'CONTROL DE CALIDAD', true, $cCal, 'FFFFFF', 'center');

                $sheet->getStyle('A9:D9')->getFont()->setSize(10);

                // ════════════════════════════════════════════════════════════
                // BLOQUE 4 – Campos de inspección (filas 10-17)
                // ════════════════════════════════════════════════════════════
                $rows = [
                    10 => ['FECHA DE INSPECCIÓN', $ins->fecha_inspeccion_solicitante?->format('d/m/Y') ?? '—',
                            'FECHA DE INSPECCIÓN', $ins->fecha_inspeccion_calidad?->format('d/m/Y') ?? '—'],
                    11 => ['INSPECCIONÓ (NOMBRE)', $ins->inspeccionado_solicitante ?? '—',
                            'INSPECCIONÓ (NOMBRE)', $ins->inspeccionado_calidad ?? '—'],
                    12 => ['DEPARTAMENTO', $ins->departamento_solicitante ?? '—',
                            'DEPARTAMENTO', $ins->departamento_calidad ?? '—'],
                ];

                foreach ($rows as $r => $data) {
                    $sheet->getRowDimension($r)->setRowHeight(14);
                    $set("A{$r}", $data[0], true, $cBlueBg,  '374151');
                    $set("B{$r}", $data[1], false, 'FFFFFF');
                    $set("C{$r}", $data[2], true, $cGreenBg, '374151');
                    $set("D{$r}", $data[3], false, 'FFFFFF');
                }

                // Observaciones (filas 13-16, fusionadas verticalmente a 4 filas)
                $sheet->getRowDimension(13)->setRowHeight(14);
                $sheet->getRowDimension(14)->setRowHeight(40);

                $set('A13', 'OBSERVACIONES', true, $cBlueBg, '374151');
                $set('C13', 'OBSERVACIONES', true, $cGreenBg, '374151');

                $sheet->mergeCells('B13:B14');
                $set('B13', $ins->observaciones_solicitante ?? '—', false, 'FFFFFF');
                $sheet->getStyle('B13')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

                $sheet->mergeCells('D13:D14');
                $set('D13', $ins->observaciones_calidad ?? '—', false, 'FFFFFF');
                $sheet->getStyle('D13')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

                $sheet->mergeCells('A13:A14');
                $sheet->mergeCells('C13:C14');

                $border('A9:D14');
                $outline('A9:D14', '9CA3AF');

                // ════════════════════════════════════════════════════════════
                // BLOQUE 5 – Datos inferiores
                // ════════════════════════════════════════════════════════════
                $sheet->getRowDimension(15)->setRowHeight(6);  // spacer
                $sheet->getRowDimension(16)->setRowHeight(14);
                $sheet->getRowDimension(17)->setRowHeight(18);

                $set('A16', 'REQUIERE CTRL. CALIDAD', true, $cHdrLight, '6B7280');
                $set('B16', 'NO. SOLICITUD', true, $cHdrLight, '6B7280');
                $set('C16', 'FECHA INGRESO A INVENTARIO', true, $cHdrLight, '6B7280', 'center');
                $sheet->mergeCells('C16:D16');

                $ctrlVal = $ins->requiere_ctrl_calidad ? 'SÍ' : 'NO';
                $ctrlBg  = $ins->requiere_ctrl_calidad ? 'DBEAFE' : 'F3F4F6';
                $ctrlFg  = $ins->requiere_ctrl_calidad ? '1E3A8A' : '4B5563';
                $set('A17', $ctrlVal, true, $ctrlBg, $ctrlFg, 'center');
                $set('B17', $ins->no_solicitud ?? '—', false, 'FFFFFF');
                $set('C17', $ins->fecha_ingreso_inventario?->format('d/m/Y') ?? '—', false, 'FFFFFF', '000000', 'center');
                $sheet->mergeCells('C17:D17');

                $border('A16:D17');
                $outline('A16:D17', '9CA3AF');

                // ════════════════════════════════════════════════════════════
                // BLOQUE 6 – Resultados de inspección
                // ════════════════════════════════════════════════════════════
                $sheet->getRowDimension(18)->setRowHeight(6);  // spacer
                $sheet->getRowDimension(19)->setRowHeight(14);
                $sheet->getRowDimension(20)->setRowHeight(20);
                $sheet->getRowDimension(21)->setRowHeight(14);

                $sheet->mergeCells('A19:B19');
                $set('A19', 'RESULTADO DE INSPECCIONES — SOLICITANTE', true, $cHdrLight, '374151', 'center');

                $sheet->mergeCells('C19:D19');
                $set('C19', 'RESULTADO DE INSPECCIONES — CALIDAD', true, $cHdrLight, '374151', 'center');

                $resultadoMap = [
                    'no_conforme' => ['NO CONFORME', $cNoConf],
                    'conforme'    => ['CONFORME',    $cConf],
                    'a_revision'  => ['A REVISIÓN',  $cRevision],
                    null          => ['SIN RESULTADO', 'D1D5DB'],
                    ''            => ['SIN RESULTADO', 'D1D5DB'],
                ];

                [$lblSol, $bgSol] = $resultadoMap[$ins->resultado_solicitante] ?? $resultadoMap[null];
                [$lblCal, $bgCal] = $resultadoMap[$ins->resultado_calidad]     ?? $resultadoMap[null];

                $sheet->mergeCells('A20:B20');
                $set('A20', $lblSol, true, $bgSol, 'FFFFFF', 'center');
                $sheet->getStyle('A20')->getFont()->setSize(11);

                $sheet->mergeCells('C20:D20');
                $set('C20', $lblCal, true, $bgCal, 'FFFFFF', 'center');
                $sheet->getStyle('C20')->getFont()->setSize(11);

                $border('A19:D20');
                $outline('A19:D20', '9CA3AF');

                // ════════════════════════════════════════════════════════════
                // BLOQUE 7 – Pie con folio y fecha de registro
                // ════════════════════════════════════════════════════════════
                $sheet->getRowDimension(21)->setRowHeight(14);
                $sheet->mergeCells('A21:B21');
                $sheet->mergeCells('C21:D21');
                $set('A21', 'Folio: ' . $ins->folio . '  ·  Registrado por: ' . ($ins->registradoPor?->name ?? '—'), false, $cHdrLight, '6B7280');
                $set('C21', 'Fecha de registro: ' . $ins->created_at->format('d/m/Y H:i'), false, $cHdrLight, '6B7280', 'center');

                $border('A21:D21');
            },
        ];
    }
}
