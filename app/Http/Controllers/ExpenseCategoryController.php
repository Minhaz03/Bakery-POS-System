<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExpenseCategory;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = ExpenseCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        $categories = $query->orderBy('name')->paginate(15);
        
        return view('dashboard.expense-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['tenant_id'] = auth()->user()->tenant_id;

        ExpenseCategory::create($validated);

        return redirect()->route('dashboard.expense-categories.index')
            ->with('success', 'Expense Category created successfully.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $expenseCategory->update($validated);

        return redirect()->route('dashboard.expense-categories.index')
            ->with('success', 'Expense Category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        // Check if there are expenses linked to this category
        if ($expenseCategory->expenses()->exists()) {
            return redirect()->route('dashboard.expense-categories.index')
                ->with('error', 'Cannot delete this category because it is linked to existing expenses.');
        }

        $expenseCategory->delete();

        return redirect()->route('dashboard.expense-categories.index')
            ->with('success', 'Expense Category deleted successfully.');
    }
}
