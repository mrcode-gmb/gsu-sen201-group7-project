<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\BusinessScoped;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryCategoryController extends Controller
{
    use BusinessScoped;

    public function index(): View
    {
        $categories = $this->scopeToCurrentBusiness(Category::class)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('inventory.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Category::create($this->addBusinessId($validated));

        return redirect()
            ->route('inventory.categories.index')
            ->with('success', 'Inventory category created successfully!');
    }

    public function edit(Category $category): View
    {
        $this->ensureBusinessOwnership($category);
        $category->loadCount('products');

        return view('inventory.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->ensureBusinessOwnership($category);

        $validated = $request->validate($this->rules($category));
        $category->update($validated);

        return redirect()
            ->route('inventory.categories.index')
            ->with('success', 'Inventory category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->ensureBusinessOwnership($category);

        if ($category->products()->exists()) {
            return redirect()
                ->route('inventory.categories.index')
                ->withErrors([
                    'category' => 'This category is already assigned to inventory items and cannot be deleted yet.',
                ]);
        }

        $category->delete();

        return redirect()
            ->route('inventory.categories.index')
            ->with('success', 'Inventory category deleted successfully!');
    }

    private function rules(?Category $category = null): array
    {
        $businessId = $this->getBusinessId();
        $uniqueName = Rule::unique('categories', 'name');

        if ($businessId) {
            $uniqueName->where(fn ($query) => $query->where('business_id', $businessId));
        } else {
            $uniqueName->whereNull('business_id');
        }

        if ($category) {
            $uniqueName->ignore($category->id);
        }

        return [
            'name' => ['required', 'string', 'max:100', $uniqueName],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
