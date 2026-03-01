<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
    ) {}

    public function query()
    {
        $query = Transaction::query()
            ->with(['user', 'payment', 'items'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No. Invoice',
            'Tanggal',
            'Kasir',
            'Jumlah Item',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Grand Total',
            'Metode Pembayaran',
            'Status Pembayaran',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->invoice_number,
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->user->name ?? '-',
            $transaction->items->sum('quantity'),
            $transaction->subtotal,
            $transaction->discount_amount,
            $transaction->tax_amount,
            $transaction->grand_total,
            strtoupper($transaction->payment->method ?? '-'),
            $transaction->payment->status ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Transaksi';
    }
}
