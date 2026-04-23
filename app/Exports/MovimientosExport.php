<?php

namespace App\Exports;

use App\Models\Movimiento;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MovimientosExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(
        private ?string $tipoMovimiento,
        private ?string $fuente,
        private ?string $fechaDesde,
        private ?string $fechaHasta,
        private ?string $search
    ) {}

    public function query()
    {
        $query = Movimiento::with(['producto', 'usuario', 'solicitud', 'ticket'])
            ->orderBy('created_at', 'desc');

        if ($this->tipoMovimiento) {
            $query->where('tipo_movimiento', $this->tipoMovimiento);
        }

        if ($this->fuente) {
            $query->where('fuente', $this->fuente);
        }

        if ($this->fechaDesde) {
            $query->whereDate('created_at', '>=', $this->fechaDesde);
        }

        if ($this->fechaHasta) {
            $query->whereDate('created_at', '<=', $this->fechaHasta);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('referencia', 'like', "%{$search}%")
                  ->orWhereHas('producto', function ($pq) use ($search) {
                      $pq->where('codigo', 'like', "%{$search}%")
                         ->orWhere('descripcion', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Hora',
            'Tipo',
            'Fuente',
            'Código Producto',
            'Descripción Producto',
            'Cant. Anterior',
            'Cantidad',
            'Cant. Nueva',
            'Descripción / Detalle',
            'Referencia',
            'Solicitante / Ticket',
            'Usuario',
        ];
    }

    public function map($m): array
    {
        $fuenteLabels = [
            'excel'               => 'Excel',
            'json'                => 'JSON',
            'barras'              => 'Barras',
            'solicitud_material'  => 'Req. Material',
            'solicitud_movimiento'=> 'Sol. Movimiento',
            'manual'              => 'Manual',
        ];

        $tipoLabels = [
            'entrada'       => 'Entrada',
            'salida'        => 'Salida',
            'transferencia' => 'Transferencia',
            'ajuste'        => 'Ajuste',
        ];

        $fuente = $m->fuente ?? 'manual';
        $trazabilidad = '';

        if ($fuente === 'solicitud_material' && $m->solicitud) {
            $folio = $m->solicitud->folio ?? ('SOL-' . $m->solicitud->id);
            $solicitante = $m->solicitud->solicitante ?? '';
            $trazabilidad = $folio . ($solicitante ? ' — ' . $solicitante : '');
        } elseif ($fuente === 'solicitud_movimiento' && $m->ticket) {
            $trazabilidad = $m->ticket->formatted_code ?? ('TKT-' . $m->ticket->id);
        }

        return [
            $m->created_at->format('d/m/Y'),
            $m->created_at->format('H:i'),
            $tipoLabels[$m->tipo_movimiento] ?? $m->tipo_movimiento,
            $fuenteLabels[$fuente] ?? $fuente,
            $m->producto->codigo ?? '',
            $m->producto->descripcion ?? '',
            $m->cantidad_anterior,
            $m->cantidad,
            $m->cantidad_nueva,
            $m->descripcion ?? '',
            $m->referencia ?? '',
            $trazabilidad,
            $m->usuario->name ?? 'Sistema',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'M';

        // Encabezado
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A568D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Filas de datos con alternancia de color
        for ($row = 2; $row <= $lastRow; $row++) {
            $bg = ($row % 2 === 0) ? 'FFFFFF' : 'F3F4F9';
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'font' => ['size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        // Centrar columnas numéricas y de fecha
        $sheet->getStyle("A2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Borde exterior en tabla
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
            ],
        ]);

        // Freeze primera fila
        $sheet->freezePane('A2');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Fecha
            'B' => 8,   // Hora
            'C' => 14,  // Tipo
            'D' => 16,  // Fuente
            'E' => 14,  // Código
            'F' => 36,  // Descripción producto
            'G' => 13,  // Cant. Anterior
            'H' => 11,  // Cantidad
            'I' => 11,  // Cant. Nueva
            'J' => 34,  // Descripción/detalle
            'K' => 18,  // Referencia
            'L' => 22,  // Solicitante/Ticket
            'M' => 20,  // Usuario
        ];
    }

    public function title(): string
    {
        return 'Movimientos';
    }
}
