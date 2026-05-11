<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessCapitalController extends Controller
{
    public function create(Request $request, Business $business)
    {
        $type = $request->query('type', 'deposit');
        return view('super-admin.capital.create', compact('business', 'type'));
    }

    public function store(Request $request, Business $business)
    {
        // return $business;
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:deposit,withdrawal',
        ]);

        if ($validated['type'] === 'deposit') {
            $business->businessCapital->credit($validated['amount'], $validated['description']);
        } else {
            try {
                $business->businessCapital->debit($validated['amount'], $validated['description']);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return redirect()->route('super-admin.businesses.show', $business)->with('success', 'Capital transaction successful.');
    }

}
