<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashierPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $cashierId = null,
    ) {}

    public function collection()
    {
        $query = Transaction::query()
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(grand_total) as total_revenue'),
                DB::raw('AVG(grand_total) as avg_transaction'),
                DB::raw('MAX(created_at) as last_transaction_at')
            )
            ->where('status', 'completed')
            ->groupBy('user_id');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->cashierId) {
            $query->where('user_id', $this->cashierId);
        }

        $data = $query->orderByDesc('total_revenue')->get();

        // Map cashier names
        $cashierIds = $data->pluck('user_id')->toArray();
        $cashiers = User::whereIn('id', $cashierIds)->pluck('name', 'id');

        return $data->map(function ($item) use ($cashiers) {
            $item->cashier_name = $cashiers[$item->user_id] ?? 'Unknown';
            return $item;
        });
    }

    public function headings(): array
    {
        return [
            'Nama Kasir',
            'Total Transaksi',
            'Total Pendapatan (Rp)',
            'Rata-rata Transaksi (Rp)',
            'Transaksi Terakhir',
            'Periode',
        ];
    }

    public function map($row): array
    {
        $period = ($this->startDate ?? '-') . ' s/d ' . ($this->endDate ?? '-');

        return [
            $row->cashier_name,
            (int) $row->total_transactions,
            number_format($row->total_revenue, 0, ',', '.'),
            number_format($row->avg_transaction, 0, ',', '.'),
            $row->last_transaction_at ? \Carbon\Carbon::parse($row->last_transaction_at)->format('d/m/Y H:i') : '-',
            $period,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7C3AED'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Performa Kasir';
    }
}
