<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesPdfExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $sales;
    protected $totalSales;
    protected $totalProfit;
    protected $paymentSummary;
    protected $paymentTypes;
    protected $filters;

    public function __construct($sales, $totalSales, $totalProfit, $paymentSummary, $paymentTypes, $filters = [])
    {
        $this->sales = $sales;
        $this->totalSales = $totalSales;
        $this->totalProfit = $totalProfit;
        $this->paymentSummary = $paymentSummary;
        $this->paymentTypes = $paymentTypes;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.sales-pdf', [
            'sales' => $this->sales,
            'totalSales' => $this->totalSales,
            'totalProfit' => $this->totalProfit,
            'paymentSummary' => $this->paymentSummary,
            'paymentTypes' => $this->paymentTypes,
            'filters' => $this->filters
        ]);
    }

    public function title(): string
    {
        return 'Sales Report';
    }
}
