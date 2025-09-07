<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PricingTierController extends Controller
{
    public function index()
    {
        $tiers = Auth::user()->pricingTiers()->get();
        return Inertia::render('Owner/PricingTier/Index', [
            'pricingTiers' => $tiers,
        ]);
    }

    public function list()
    {
        $tiers = Auth::user()->pricingTiers()->get();
        return response()->json(['pricingTiers' => $tiers]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'duration_from' => 'required|integer|min:1',
            'duration_unit' => 'required|in:minutes,hours,days',
            'price' => 'required|numeric|min:0',
        ]);
        Auth::user()->pricingTiers()->create($request->only(['duration_from', 'duration_unit', 'price']));
        return back();
    }

    public function edit($id)
    {
        $tier = Auth::user()->pricingTiers()->findOrFail($id);
        return response()->json(['tier' => $tier]);
    }

    public function update(Request $request, $id)
    {
        $tier = Auth::user()->pricingTiers()->findOrFail($id);
        $request->validate([
            'duration_from' => 'required|integer|min:1',
            'duration_unit' => 'required|in:minutes,hours,days',
            'price' => 'required|numeric|min:0',
        ]);
        $tier->update($request->only(['duration_from', 'duration_unit', 'price']));
        
        // Return back to the pricing tiers page
        return back()->with('success', 'Pricing tier updated successfully.');
    }

    public function destroy($id)
    {
        $tier = Auth::user()->pricingTiers()->findOrFail($id);
        $tier->delete();
        return back();
    }
}
