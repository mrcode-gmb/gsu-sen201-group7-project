<?php

namespace App\Exports;

use App\Models\Expenses;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExpensesPdfExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $expenses;
    protected $filters;

    public function __construct($expenses, $filters = [])
    {
        $this->expenses = $expenses;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.expenses-pdf', [
            'expenses' => $this->expenses,
            'filters' => $this->filters
        ]);
    }

    public function title(): string
    {
        return 'Expenses Report';
    }
}
