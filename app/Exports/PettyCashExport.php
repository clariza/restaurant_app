<?php

namespace App\Exports;

use App\Models\PettyCash;
use Illuminate\Support\Collection;

/**
 * @method Collection collection()
 * @method array headings()
 * @method array map($pettyCash)
 * @method array styles($sheet)
 */

// Tus use statements normales
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Barryvdh\DomPDF\Facade\Pdf;

class PettyCashExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PettyCash::with('user');

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Cajero',
            'Ventas Efectivo',
            'Ventas QR',
            'Ventas Tarjeta',
            'Total Ventas',
            'Total Gastos',
            'Saldo Final',
            'Estado'
        ];
    }

    public function map($pettyCash): array
    {
        $totalVentas = $pettyCash->total_sales_cash + $pettyCash->total_sales_qr + $pettyCash->total_sales_card;
        $saldoFinal = $totalVentas - $pettyCash->total_expenses;

        return [
            $pettyCash->date,
            $pettyCash->user->name ?? 'N/A',
            '$' . number_format($pettyCash->total_sales_cash, 2),
            '$' . number_format($pettyCash->total_sales_qr, 2),
            '$' . number_format($pettyCash->total_sales_card, 2),
            '$' . number_format($totalVentas, 2),
            '$' . number_format($pettyCash->total_expenses, 2),
            '$' . number_format($saldoFinal, 2),
            $pettyCash->status === 'open' ? 'Abierta' : 'Cerrada'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Reporte Caja Chica';
    }
}
