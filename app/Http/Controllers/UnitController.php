<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::with('baseUnit')->orderBy('name')->paginate(15);
        $allUnits = Unit::whereNull('base_unit_id')->orderBy('name')->get();
        return view('dashboard.units.index', compact('units', 'allUnits'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('units')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })
            ],
            'short_name' => [
                'required', 'string', 'max:20',
                \Illuminate\Validation\Rule::unique('units')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })
            ],
            'base_unit_id' => 'nullable|exists:units,id',
            'operator' => 'required|in:*,/',
            'conversion_rate' => 'required|numeric|min:0.0001',
        ]);

        $unit = Unit::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully!',
                'data' => $unit
            ]);
        }

        return redirect()->back()->with('success', 'Unit created successfully!');
    }

    public function update(Request $request, Unit $unit): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('units')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })->ignore($unit->id)
            ],
            'short_name' => [
                'required', 'string', 'max:20',
                \Illuminate\Validation\Rule::unique('units')->where(function ($query) {
                    return $query->where('tenant_id', auth()->user()->tenant_id);
                })->ignore($unit->id)
            ],
            'base_unit_id' => 'nullable|exists:units,id',
            'operator' => 'required|in:*,/',
            'conversion_rate' => 'required|numeric|min:0.0001',
        ]);

        $unit->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully!',
                'data' => $unit
            ]);
        }

        return redirect()->back()->with('success', 'Unit updated successfully!');
    }

    public function destroy(Unit $unit): RedirectResponse|JsonResponse
    {
        $unit->delete();

        return redirect()->back()->with('success', 'Unit deleted successfully!');
    }
}
