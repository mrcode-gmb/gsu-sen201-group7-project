<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Sale::with(['business', 'user', 'purchase.product']);

        if (!empty($this->filters['business_id'])) {
            $query->where('business_id', $this->filters['business_id']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Sale ID',
            'Date',
            'Business',
            'Product',
            'Unit Type',
            'Quantity',
            'Unit Price',
            'Total Amount',
            'Seller',
            'Sale Status',
            'Notes'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->unique_id,
            $sale->created_at->format('Y-m-d H:i:s'),
            $sale->business->name ?? 'N/A',
            $sale->purchase->product->name ?? 'N/A',
            $sale->purchase->product->unit_type ?? 'N/A',
            $sale->quantity,
            $sale->unit_price,
            $sale->total_amount,
            $sale->user->name,
            $sale->sale_status,
            $sale->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
