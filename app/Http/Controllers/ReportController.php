<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\User;
use App\Traits\BusinessScoped;
use Carbon\Carbon;
use TCPDF;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use BusinessScoped;
    /**
     * Show reports dashboard
     */
    public function index()
    {
        $lowStockProducts = $this->scopeToCurrentBusiness(Product::class)
            ->withSum('purchase as warehouse_stock', 'quantity')
            ->get()
            ->filter(function (Product $product) {
                return (float) ($product->warehouse_stock ?? 0) <= $product->effective_low_stock_threshold;
            });

        // Get today's stats scoped to business
        $todaySales = $this->scopeToCurrentBusiness(Sale::class)
            ->whereDate('created_at', today())
            ->sum('total_amount');
        
        $todayProfit = $this->scopeToCurrentBusiness(Sale::class)
            ->whereDate('created_at', today())
            ->sum('profit');
        
        $monthlySales = $this->scopeToCurrentBusiness(Sale::class)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        
        $lowStockItems = $lowStockProducts->count();
        
        // Top selling products this month
        $topProducts = $this->scopeToCurrentBusiness(Sale::class)
            ->with('purchase.product')
            ->selectRaw('purchase_id, SUM(quantity) as total_sold, SUM(total_amount) as total_revenue')
            ->whereMonth('created_at', now()->month)
            ->groupBy('purchase_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        // Top performers this month
        $topSalespeople = $this->scopeToCurrentBusiness(Sale::class)
            ->with('user')
            ->selectRaw('user_id, SUM(total_amount) as total_sales, SUM(profit) as total_profit, COUNT(*) as sales_count')
            ->whereMonth('created_at', now()->month)
            ->groupBy('user_id')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();
        
        return view('reports.index', compact(
            'todaySales',
            'todayProfit',
            'monthlySales',
            'lowStockItems',
            'topProducts',
            'topSalespeople'
        ));
    }

    /**
     * Sales Report
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $userId = $request->input('user_id');

        $query = $this->scopeToCurrentBusiness(Sale::class)
            ->with(['purchase.product', 'user'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);


        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        // Calculate totals
        $totalSales = $sales->sum('total_amount');
        $totalProfit = $sales->sum('profit');
        $totalQuantity = $sales->sum('quantity');

        // Group by product
        $productSales = $sales->groupBy('purchase_id')->map(function ($group) {
            return [
                'purchase' => $group->first()->purchase,
                'quantity' => $group->sum('quantity'),
                'total_amount' => $group->sum('total_amount'),
                'profit' => $group->sum('profit'),
            ];
        });

        // Group by salesperson
        $salespersonSales = $sales->groupBy('user_id')->map(function ($group) {
            return [
                'user' => $group->first()->user,
                'quantity' => $group->sum('quantity'),
                'total_amount' => $group->sum('total_amount'),
                'profit' => $group->sum('profit'),
                'sales_count' => $group->count(),
            ];
        });

        // return $productSales;

        $salespeople = $this->scopeToCurrentBusiness(User::class)
            ->where('role', 'salesperson')
            ->get();

        return view('reports.sales', compact(
            'sales',
            'startDate',
            'endDate',
            'totalSales',
            'totalProfit',
            'totalQuantity',
            'productSales',
            'salespersonSales',
            'salespeople'
        ));
    }

    /**
     * Profit Report
     */
    public function profitReport(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', Carbon::now()->year);

        $profits = collect();

        if ($period === 'monthly') {
            for ($month = 1; $month <= 12; $month++) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();

                $monthlyProfit = $this->scopeToCurrentBusiness(Sale::class)
                    ->whereDate('sale_date', '>=', $startDate)
                    ->whereDate('sale_date', '<=', $endDate)
                    ->sum('profit');

                $monthlySales = $this->scopeToCurrentBusiness(Sale::class)
                    ->whereDate('sale_date', '>=', $startDate)
                    ->whereDate('sale_date', '<=', $endDate)
                    ->sum('total_amount');

                $profits->push([
                    'period' => $startDate->format('F Y'),
                    'sales' => $monthlySales,
                    'profit' => $monthlyProfit,
                    'margin' => $monthlySales > 0 ? ($monthlyProfit / $monthlySales) * 100 : 0,
                ]);
            }
        } else {
            // Daily for current month
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dailyProfit = $this->scopeToCurrentBusiness(Sale::class)
                    ->whereDate('sale_date', $date)
                    ->sum('profit');

                $dailySales = $this->scopeToCurrentBusiness(Sale::class)
                    ->whereDate('sale_date', $date)
                    ->sum('total_amount');

                $profits->push([
                    'period' => $date->format('M d, Y'),
                    'sales' => $dailySales,
                    'profit' => $dailyProfit,
                    'margin' => $dailySales > 0 ? ($dailyProfit / $dailySales) * 100 : 0,
                ]);
            }
        }

        return view('reports.profit', compact('profits', 'period', 'year'));
    }

    /**
     * Inventory Report
     */
    public function inventoryReport()
    {
        $products = $this->scopeToCurrentBusiness(Product::class)
            ->with(['category', 'purchase.sales', 'purchaseHistory'])
            ->withSum('purchase as warehouse_stock', 'quantity')
            ->orderBy('name')
            ->get();

        $inventoryData = $products->map(function (Product $product) {
            $totalPurchased = (float) $product->purchaseHistory->sum('quantity');
            $totalSold = (float) $product->sales->sum('quantity');
            $averageCostPrice = (float) $product->getAverageCostPrice();
            $currentStock = (float) ($product->warehouse_stock ?? 0);
            $sellingPrice = (float) $product->purchase->sortByDesc('purchase_date')->first()?->selling_price;
            $threshold = $product->effective_low_stock_threshold;
            $status = $currentStock <= 0
                ? 'out'
                : ($currentStock <= $threshold ? 'low' : 'healthy');

            return [
                'product' => $product,
                'current_stock' => $currentStock,
                'total_purchased' => $totalPurchased,
                'total_sold' => $totalSold,
                'stock_value' => $currentStock * $averageCostPrice,
                'average_cost_price' => $averageCostPrice,
                'selling_price' => $sellingPrice,
                'potential_profit' => $sellingPrice > 0 ? $currentStock * ($sellingPrice - $averageCostPrice) : 0,
                'low_stock_threshold' => $threshold,
                'status' => $status,
            ];
        });

        $totalStockValue = $inventoryData->sum('stock_value');
        $totalPotentialProfit = $inventoryData->sum('potential_profit');

        return view('reports.inventory', compact('inventoryData', 'totalStockValue', 'totalPotentialProfit'));
    }

    /**
     * Export Sales Report to PDF
     */
    public function exportSalesPDF(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $sales = $this->scopeToCurrentBusiness(Sale::class)
            ->with(['purchase.product', 'user'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->orderBy('sale_date', 'desc')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalProfit = $sales->sum('profit');

        // Create PDF
        $pdf = new TCPDF();
        $pdf->SetCreator('Palm Oil Shop');
        $pdf->SetAuthor('Palm Oil Shop');
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('Sales Report');

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Palm Oil Shop - Sales Report', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "Period: {$startDate} to {$endDate}", 0, 1, 'C');
        $pdf->Ln(5);

        // Summary
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Summary', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, "Total Sales: N" . number_format($totalSales, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, "Total Profit: N" . number_format($totalProfit, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, "Number of Transactions: " . $sales->count(), 0, 1, 'L');
        $pdf->Ln(10);

        // Table header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(20, 8, 'Date', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Product', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Quantity', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Unit Price', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Total', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Profit', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Salesperson', 1, 1, 'C');

        // Table data
        $pdf->SetFont('helvetica', '', 8);
        foreach ($sales as $sale) {
            $pdf->Cell(20, 8, $sale->sale_date->format('M d'), 1, 0, 'C');
            $pdf->Cell(40, 8, substr($sale->purchase->product->name, 0, 25), 1, 0, 'L');
            $pdf->Cell(20, 8, number_format($sale->quantity, 1), 1, 0, 'C');
            $pdf->Cell(25, 8, 'N' . number_format($sale->selling_price_per_unit), 1, 0, 'R');
            $pdf->Cell(25, 8, 'N' . number_format($sale->total_amount), 1, 0, 'R');
            $pdf->Cell(25, 8, 'N' . number_format($sale->net_profit_per_unit), 1, 0, 'R');
            $pdf->Cell(35, 8, substr($sale->user->name, 0, 20), 1, 1, 'L');
        }

        $filename = "sales_report_{$startDate}_to_{$endDate}.pdf";
        $pdf->Output($filename, 'D');
    }

    /**
     * Export Profit Report to PDF
     */
    public function exportProfitPDF(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', Carbon::now()->year);

        // Get the same data as the profit report
        $profits = $this->getProfitData($period, $year);
        
        $totalSales = $profits->sum('sales');
        $totalProfit = $profits->sum('profit');
        $averageMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        // Create PDF
        $pdf = new TCPDF();
        $pdf->SetCreator('Palm Oil Shop');
        $pdf->SetAuthor('Palm Oil Shop');
        $pdf->SetTitle('Profit Analysis Report');
        $pdf->SetSubject('Profit Analysis Report');

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Palm Oil Shop - Profit Analysis Report', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $periodText = $period == 'monthly' ? 
            'Monthly Report for ' . $year : 
            'Daily Report for ' . Carbon::now()->format('F Y');
        $pdf->Cell(0, 10, $periodText, 0, 1, 'C');
        $pdf->Ln(5);

        // Summary
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Summary', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, "Total Sales: N" . number_format($totalSales, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, "Total Profit: N" . number_format($totalProfit, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, "Average Margin: " . number_format($averageMargin, 1) . '%', 0, 1, 'L');
        $pdf->Ln(10);

        // Table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 8, 'Period', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Sales', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Cost', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Profit', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Margin %', 1, 1, 'C');

        // Table data
        $pdf->SetFont('helvetica', '', 9);
        foreach ($profits as $profit) {
            $margin = $profit['sales'] > 0 ? ($profit['profit'] / $profit['sales']) * 100 : 0;
            
            $pdf->Cell(40, 8, $profit['period'], 1, 0, 'L');
            $pdf->Cell(40, 8, 'N' . number_format($profit['sales'], 2), 1, 0, 'R');
            $pdf->Cell(40, 8, 'N' . number_format($profit['cost'], 2), 1, 0, 'R');
            $pdf->Cell(40, 8, 'N' . number_format($profit['profit'], 2), 1, 0, 'R');
            $pdf->Cell(30, 8, number_format($margin, 1) . '%', 1, 1, 'R');
        }

        $filename = "profit_report_{$period}_{$year}.pdf";
        $pdf->Output($filename, 'D');
    }

    /**
     * Helper method to get profit data
     */
    protected function getProfitData($period, $year)
    {
        if ($period === 'daily') {
            $startDate = Carbon::createFromDate($year, Carbon::now()->month, 1);
            $endDate = $startDate->copy()->endOfMonth();
            
            $sales = $this->scopeToCurrentBusiness(Sale::class)
                ->select(
                    DB::raw('DATE(sale_date) as date'),
                    DB::raw('SUM(total_amount) as sales'),
                    DB::raw('SUM(quantity * cost_price_per_unit) as cost'),
                    DB::raw('SUM(profit) as profit')
                )
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $sales->map(function($item) {
                return [
                    'period' => Carbon::parse($item->date)->format('M d'),
                    'sales' => $item->sales,
                    'cost' => $item->cost,
                    'profit' => $item->profit
                ];
            });
        } else {
            $sales = $this->scopeToCurrentBusiness(Sale::class)
                ->select(
                    DB::raw('YEAR(sale_date) as year'),
                    DB::raw('MONTH(sale_date) as month'),
                    DB::raw('SUM(total_amount) as sales'),
                    DB::raw('SUM(quantity * cost_price_per_unit) as cost'),
                    DB::raw('SUM(profit) as profit')
                )
                ->whereYear('sale_date', $year)
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            return $sales->map(function($item) {
                return [
                    'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'sales' => $item->sales,
                    'cost' => $item->cost,
                    'profit' => $item->profit
                ];
            });
        }
    }

    public function exportExpensivePDF(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $expenses = $this->scopeToCurrentBusiness(Expenses::class)
            ->with(['user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        // Create PDF
        $pdf = new TCPDF();
        $pdf->SetCreator('Palm Oil Shop');
        $pdf->SetAuthor('Palm Oil Shop');
        $pdf->SetTitle('Expenses Report');
        $pdf->SetSubject('Expenses Report');

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Palm Oil Shop - Expenses Report', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "Period: {$startDate} to {$endDate}", 0, 1, 'C');
        $pdf->Ln(5);

        // Summary
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Summary', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, "Total Expenses: ₦" . number_format($totalExpenses, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, "Number of Expenses: " . $expenses->count(), 0, 1, 'L');
        $pdf->Ln(10);

        // Table header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(25, 8, 'S/N', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Date', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Expenses Title', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Amount', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Net Profit', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Name', 1, 1, 'C');

        // Table data
        $pdf->SetFont('helvetica', '', 8);
        foreach ($expenses as $key => $sale) {
            $pdf->Cell(25, 8, number_format($key + 1), 1, 0, 'C');
            $pdf->Cell(20, 8, $sale->created_at->format('M d'), 1, 0, 'C');
            $pdf->Cell(40, 8, substr($sale->name, 0, 25), 1, 0, 'L');
            $pdf->Cell(25, 8, '₦' . number_format($sale->amount), 1, 0, 'R');
            $pdf->Cell(25, 8, '₦' . number_format($sale->today_net_profit), 1, 0, 'R');
            $pdf->Cell(35, 8, substr($sale->user->name, 0, 20), 1, 1, 'L');
        }

        $filename = "expenses_report_{$startDate}_to_{$endDate}.pdf";
        $pdf->Output($filename, 'D');
    }
}
