<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\CreditorTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Display Super Admin Dashboard
     */
    public function dashboard()
    {
        $totalCreditorPayments = CreditorTransaction::where("type", "credit")->sum('amount');
        // Overall statistics across all businesses
        // return $totalCreditorPayments;
        $total_expenses = \App\Models\Expenses::sum('amount');
        $total_commission = \App\Models\ProductAssignment::sum('commission_amount');
        $total_profit = Sale::sum('profit');
        $net_profit = $total_profit - $total_expenses - $total_commission;
        $total_business_capital = \App\Models\BusinessCapital::sum('balance');
        $totalcostInventory = Purchase::get()->sum(function($purchase){
            return $purchase->quantity * $purchase->purchase_price;
        });

        $stats = [
            'total_businesses' => Business::count(),
            'active_businesses' => Business::where('status', 'active')->count(),
            'total_users' => User::where('role', '!=', 'super_admin')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_salespeople' => User::where('role', 'salesperson')->count(),
            'total_products' => Product::count(),
            'total_sales_amount' => Sale::where("payment_type", "!=", "credit")->sum('total_amount') + $totalCreditorPayments,
            'total_profit' => $total_profit,
            'total_purchases' => Purchase::sum('total_cost'),
            'total_expenses' => $total_expenses,
            'total_commission' => $total_commission,
            'net_profit' => $net_profit,
            'total_business_capital' => $total_business_capital,
        ];

        // Recent businesses
        $recentBusinesses = Business::withCount(['users', 'products', 'sales'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Business performance (top 5 by sales)
        $topBusinesses = Business::withSum('sales', 'total_amount')
            ->withSum('sales', 'profit')
            ->orderBy('sales_sum_total_amount', 'desc')
            ->limit(5)
            ->get();

        // Monthly revenue trend (last 6 months)
        $monthlyRevenue = Sale::selectRaw('YEAR(sale_date) as year, MONTH(sale_date) as month, SUM(total_amount) as total, SUM(profit) as profit')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        // Recent activities (last 10 sales across all businesses)
        $recentSales = Sale::with(['user', 'purchase.product', 'business'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('super-admin.dashboard', compact(
            'stats',
            'recentBusinesses',
            'topBusinesses',
            'monthlyRevenue',
            'recentSales',
            'totalcostInventory',
        ));
    }

    /**
     * Display all users across all businesses
     */
    public function allUsers()
    {
        $users = User::with('business')
            ->where('role', '!=', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.users.index', compact('users'));
    }

    /**
     * Display global reports
     */
    public function reports()
    {
        // Overall statistics
        $totalSales = Sale::sum('total_amount');
        $totalProfit = Sale::sum('profit');
        $totalPurchases = Purchase::sum('total_cost');
        $totalExpenses = \App\Models\Expenses::sum('amount');
        
        // Sales by business
        $salesByBusiness = Business::withSum('sales', 'total_amount')
            ->withSum('sales', 'profit')
            ->withCount('sales')
            ->orderBy('sales_sum_total_amount', 'desc')
            ->get();
        
        // Monthly sales trend (last 12 months)
        $monthlySales = Sale::selectRaw('YEAR(sale_date) as year, MONTH(sale_date) as month, SUM(total_amount) as total, SUM(profit) as profit')
            ->where('sale_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Top products across all businesses
        $topProducts = Sale::with('purchase.product')
            ->selectRaw('purchase_id, SUM(quantity) as total_sold, SUM(total_amount) as total_revenue')
            ->groupBy('purchase_id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
        
        // Top performers across all businesses
        $topPerformers = Sale::with(['user', 'user.business'])
            ->selectRaw('user_id, SUM(total_amount) as total_sales, SUM(profit) as total_profit, COUNT(*) as sales_count')
            ->groupBy('user_id')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();
        
        // Recent sales across all businesses
        $recentSales = Sale::with(['user', 'purchase.product', 'business'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        return view('super-admin.reports.index', compact(
            'totalSales',
            'totalProfit',
            'totalPurchases',
            'totalExpenses',
            'salesByBusiness',
            'monthlySales',
            'topProducts',
            'topPerformers',
            'recentSales'
        ));
    }

    /**
     * Display system settings
     */
    public function settings()
    {
        return view('super-admin.settings.index');
    }

    /**
     * Display activity log
     */
    public function activityLog(Request $request)
    {
        $query = \App\Models\ActivityLog::with(['user', 'business'])
            ->orderBy('created_at', 'desc');

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $query->paginate(50);
        $businesses = Business::all();
        $actions = \App\Models\ActivityLog::distinct()->pluck('action');

        return view('super-admin.activity-log.index', compact('activities', 'businesses', 'actions'));
    }

    public function allDocuments()
    {
        $documents = \App\Models\Document::with(['business', 'user'])->latest()->paginate(20);
        return view('super-admin.documents.index', compact('documents'));
    }
}
