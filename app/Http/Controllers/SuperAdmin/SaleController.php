<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\User;

class SaleController extends Controller
{
    /**
     * Display a listing of all sales across all businesses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Sale::with(['business', 'user', 'purchase.product']);
        // Apply filters
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->get();
        $businesses = Business::orderBy('name')->get();
        $sellers = User::orderBy('name')->get();

        return view('super-admin.sales.index', compact('sales', 'businesses', 'sellers'));
    }

    /**
     * Export sales data to Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $filters = [
            'business_id' => $request->business_id,
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        return Excel::download(new SalesExport($filters), 'sales-export-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Display the specified sale.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        $sale->load(['business', 'user', 'purchase.product', 'assignment']);
        
        return view('sales.show', compact('sale'));
    }
}
