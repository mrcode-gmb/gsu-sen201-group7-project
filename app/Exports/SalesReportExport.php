<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $sales;
    protected $filters;
    protected $paymentTypes = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'pos' => 'POS',
        'mobile_money' => 'Mobile Money',
        'credit' => 'Credit'
    ];

    public function __construct($sales, $filters = [])
    {
        $this->sales = $sales;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->sales;
    }

    public function title(): string
    {
        return 'Sales Report';
    }

    public function headings(): array
    {
        return [
            'Date',
            'Invoice #',
            'Product',
            'Quantity',
            'Unit Price (₦)',
            'Total Amount (₦)',
            'Payment Method',
            'Salesperson',
            'Customer',
            'Profit (₦)',
            'Status'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->sale_date->format('M d, Y'),
            $sale->invoice_number,
            $sale->purchase->product->name,
            number_format($sale->quantity, 1),
            number_format($sale->selling_price_per_unit, 2),
            number_format($sale->total_amount, 2),
            $this->paymentTypes[$sale->payment_type] ?? ucfirst($sale->payment_type),
            $sale->user->name,
            $sale->customer_name ?: 'N/A',
            number_format($sale->profit, 2),
            ucfirst($sale->status)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add filter information
        $filters = [];
        if (!empty($this->filters['start_date'])) {
            $filters[] = 'From: ' . Carbon::parse($this->filters['start_date'])->format('M d, Y');
        }
        if (!empty($this->filters['end_date'])) {
            $filters[] = 'To: ' . Carbon::parse($this->filters['end_date'])->format('M d, Y');
        }
        if (!empty($this->filters['payment_type']) && $this->filters['payment_type'] !== 'all') {
            $filters[] = 'Payment Method: ' . ($this->paymentTypes[$this->filters['payment_type']] ?? ucfirst($this->filters['payment_type']));
        }

        // Add title and filters
        $sheet->insertNewRowBefore(1, 3);
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'SALES REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        if (!empty($filters)) {
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A2', 'Filters: ' . implode(' | ', $filters));
            $sheet->getStyle('A2')->getFont()->setItalic(true);
        }

        // Style the headers
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f3f4f6']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'd1d5db']
                ]
            ]
        ]);

        // Format numbers
        $lastRow = $this->sales->count() + 3; // +3 for header rows
        $sheet->getStyle("D4:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("J4:J{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Auto-size columns
        foreach(range('A','K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Add total row
        $totalRow = $lastRow + 1;
        $sheet->setCellValue("E{$totalRow}", 'TOTAL:');
        $sheet->setCellValue("F{$totalRow}", '=SUM(F4:F' . $lastRow . ')');
        $sheet->setCellValue("J{$totalRow}", '=SUM(J4:J' . $lastRow . ')');
        
        // Style total row
        $sheet->getStyle("E{$totalRow}:J{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("J{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    }
}
