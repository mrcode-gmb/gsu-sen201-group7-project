<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function superAdminIndex(Request $request)
    {
        $activities = $this->activityQuery($request)
            ->paginate(50)
            ->withQueryString();

        $businesses = Business::orderBy('name')->get(['id', 'name']);
        $actions = $this->availableActions();
        $businessLookup = $businesses->pluck('name', 'id');

        return view('super-admin.activity-log.index', [
            'activities' => $activities,
            'actions' => $actions,
            'businesses' => $businesses,
            'businessLookup' => $businessLookup,
            'routeName' => 'super-admin.activity-log',
            'showBusinessFilter' => true,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $business = auth()->user()->business;
        $businessId = $business?->id;

        $activities = $this->activityQuery($request, $businessId)
            ->paginate(50)
            ->withQueryString();

        $actions = $this->availableActions($businessId);
        $businesses = collect($business ? [['id' => $business->id, 'name' => $business->name]] : []);
        $businessLookup = $business ? collect([$business->id => $business->name]) : collect();

        return view('admin.activity-log.index', [
            'activities' => $activities,
            'actions' => $actions,
            'businesses' => $businesses,
            'businessLookup' => $businessLookup,
            'routeName' => 'admin.activity-log',
            'showBusinessFilter' => false,
        ]);
    }

    private function activityQuery(Request $request, ?int $forcedBusinessId = null)
    {
        $query = Activity::query()
            ->with('causer')
            ->latest();

        $businessId = $forcedBusinessId ?? ($request->filled('business_id') ? (int) $request->input('business_id') : null);
        if ($businessId) {
            $query->where('properties->business_id', $businessId);
        }

        if ($request->filled('action')) {
            $action = $request->input('action');
            $query->where(function ($activityQuery) use ($action) {
                $activityQuery->where('event', $action)
                    ->orWhere('description', $action);
            });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($activityQuery) use ($search) {
                $activityQuery->where('description', 'like', '%' . $search . '%')
                    ->orWhere('log_name', 'like', '%' . $search . '%')
                    ->orWhere('subject_type', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        return $query;
    }

    private function availableActions(?int $businessId = null)
    {
        $query = Activity::query()->whereNotNull('event');

        if ($businessId) {
            $query->where('properties->business_id', $businessId);
        }

        return $query->distinct()->orderBy('event')->pluck('event');
    }
}
