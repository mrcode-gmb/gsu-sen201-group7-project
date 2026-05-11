<?php

namespace App\Exports;

use App\Models\ProductAssignment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssignmentsPdfExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $assignments;
    protected $filters;

    public function __construct($assignments, $filters = [])
    {
        $this->assignments = $assignments;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.assignments-pdf', [
            'assignments' => $this->assignments,
            'filters' => $this->filters
        ]);
    }

    public function title(): string
    {
        return 'Assignments Report';
    }
}
