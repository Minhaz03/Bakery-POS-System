<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'brand', 'unit', 'tax'])
            ->where('is_pos_enabled', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low_stock') {
                $query->whereColumn('stock_qty', '<=', 'alert_qty');
            } elseif ($request->status === 'out_of_stock') {
                $query->where('stock_qty', '<=', 0);
            } elseif ($request->status === 'in_stock') {
                $query->where('stock_qty', '>', 0);
            }
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('dashboard.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $taxes = Tax::orderBy('name')->get();

        return view('dashboard.products.create', compact('categories', 'brands', 'units', 'taxes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $activeSubscription = $tenant ? $tenant->activeSubscription() : null;
        $limit = $activeSubscription ? $activeSubscription->plan->limit_products : 0;
        
        if (Product::count() >= $limit) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Your plan limit has been reached! You can only have up to {$limit} products. Please upgrade your plan.");
        }

        $deletedErrors = $this->checkDeletedUnique($request);
        if (!empty($deletedErrors)) {
            return redirect()->back()->withInput()->withErrors($deletedErrors);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'mrp_price' => 'required|numeric|min:0',
            'stock_qty' => 'required|numeric|min:0',
            'alert_qty' => 'required|numeric|min:0',
            'reorder_qty' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'is_pos_enabled' => 'nullable|boolean',
            'product_type' => 'required|in:raw_material,ready_made,finished_product',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_pos_enabled'] = $request->has('is_pos_enabled');

        if (empty($validated['sku'])) {
            $nextId = (Product::withTrashed()->max('id') ?? 0) + 1;
            $validated['sku'] = 'SKU-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }

        if (empty($validated['barcode'])) {
            $validated['barcode'] = '880' . str_pad(mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product = Product::create($validated);

        if ($product->stock_qty > 0) {
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => 'Initial Stock (+)',
                'quantity'   => $product->stock_qty,
                'user_id'    => auth()->id(),
                'notes'      => 'Product created with initial stock',
            ]);
        }

        return redirect()->route('dashboard.products')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $taxes = Tax::orderBy('name')->get();

        return view('dashboard.products.edit', compact('product', 'categories', 'brands', 'units', 'taxes'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $deletedErrors = $this->checkDeletedUnique($request, $product->id);
        if (!empty($deletedErrors)) {
            return redirect()->back()->withInput()->withErrors($deletedErrors);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'mrp_price' => 'required|numeric|min:0',
            'stock_qty' => 'required|numeric|min:0',
            'alert_qty' => 'required|numeric|min:0',
            'reorder_qty' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'is_pos_enabled' => 'nullable|boolean',
            'product_type' => 'required|in:raw_material,ready_made,finished_product',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_pos_enabled'] = $request->has('is_pos_enabled');

        if (empty($validated['sku'])) {
            $validated['sku'] = $product->sku ?? 'SKU-' . str_pad($product->id, 6, '0', STR_PAD_LEFT);
        }

        if (empty($validated['barcode'])) {
            $validated['barcode'] = $product->barcode ?? '880' . str_pad(mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $oldStock = $product->stock_qty;
        $product->update($validated);
        $newStock = $product->stock_qty;

        if ($oldStock != $newStock) {
            $diff = $newStock - $oldStock;
            $type = $diff > 0 ? 'Manual Edit (+)' : 'Manual Edit (-)';
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => $type,
                'quantity'   => $diff,
                'user_id'    => auth()->id(),
                'notes'      => 'Product stock manually updated from ' . $oldStock . ' to ' . $newStock,
            ]);
        }

        return redirect()->route('dashboard.products')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        if ($product->stock_qty > 0) {
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => 'Product Deleted (-)',
                'quantity'   => -$product->stock_qty,
                'user_id'    => auth()->id(),
                'notes'      => 'Product deleted, stock removed',
            ]);
        }

        $product->delete();

        return redirect()->route('dashboard.products')->with('success', 'Product deleted successfully!');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'unit', 'tax']);
        $stockLedger = \App\Models\StockLedger::with('user')->where('product_id', $product->id)->orderBy('id', 'desc')->paginate(10);
        return view('dashboard.products.show', compact('product', 'stockLedger'));
    }

    public function posItems(Request $request): View
    {
        $query = Product::with(['category', 'brand', 'unit'])
            ->where('is_pos_enabled', true)
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low_stock') {
                $query->whereColumn('stock_qty', '<=', 'alert_qty');
            } elseif ($request->status === 'out_of_stock') {
                $query->where('stock_qty', '<=', 0);
            } elseif ($request->status === 'in_stock') {
                $query->where('stock_qty', '>', 0);
            }
        }

        $posItems = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('dashboard.posItems.pos-items', compact('posItems', 'categories'));
    }

    // ── POS-Item dedicated create/store/edit/update ──

    public function posItemCreate(): View
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        $units      = Unit::orderBy('name')->get();
        $taxes      = Tax::orderBy('name')->get();

        return view('dashboard.posItems.pos-item-create', compact('categories', 'brands', 'units', 'taxes'));
    }

    public function posItemStore(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $activeSubscription = $tenant ? $tenant->activeSubscription() : null;
        $limit = $activeSubscription ? $activeSubscription->plan->limit_products : 0;

        if (Product::count() >= $limit) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Your plan limit has been reached! You can only have up to {$limit} products. Please upgrade your plan.");
        }

        $deletedErrors = $this->checkDeletedUnique($request);
        if (!empty($deletedErrors)) {
            return redirect()->back()->withInput()->withErrors($deletedErrors);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'sku'          => 'nullable|string|max:100|unique:products,sku',
            'barcode'      => 'nullable|string|max:100|unique:products,barcode',
            'category_id'  => 'nullable|exists:categories,id',
            'brand_id'     => 'nullable|exists:brands,id',
            'unit_id'      => 'nullable|exists:units,id',
            'tax_id'       => 'nullable|exists:taxes,id',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'cost_price'   => 'required|numeric|min:0',
            'sale_price'   => 'required|numeric|min:0',
            'mrp_price'    => 'required|numeric|min:0',
            'stock_qty'    => 'required|numeric|min:0',
            'alert_qty'    => 'required|numeric|min:0',
            'reorder_qty'  => 'required|numeric|min:0',
            'is_active'    => 'nullable|boolean',
            'product_type' => 'required|in:ready_made,finished_product',
        ]);

        // Always force POS-enabled
        $validated['is_pos_enabled'] = true;
        $validated['is_active']      = $request->has('is_active');

        if (empty($validated['sku'])) {
            $nextId = (Product::withTrashed()->max('id') ?? 0) + 1;
            $validated['sku'] = 'SKU-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = '880' . str_pad(mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        }
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        if ($product->stock_qty > 0) {
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => 'Initial Stock (+)',
                'quantity'   => $product->stock_qty,
                'user_id'    => auth()->id(),
                'notes'      => 'POS Item created with initial stock',
            ]);
        }

        return redirect()->route('dashboard.pos-items')->with('success', 'POS Item created successfully!');
    }

    public function posItemEdit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        $units      = Unit::orderBy('name')->get();
        $taxes      = Tax::orderBy('name')->get();

        return view('dashboard.posItems.pos-item-edit', compact('product', 'categories', 'brands', 'units', 'taxes'));
    }

    public function posItemUpdate(Request $request, Product $product): RedirectResponse
    {
        $deletedErrors = $this->checkDeletedUnique($request, $product->id);
        if (!empty($deletedErrors)) {
            return redirect()->back()->withInput()->withErrors($deletedErrors);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'sku'          => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode'      => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category_id'  => 'nullable|exists:categories,id',
            'brand_id'     => 'nullable|exists:brands,id',
            'unit_id'      => 'nullable|exists:units,id',
            'tax_id'       => 'nullable|exists:taxes,id',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'cost_price'   => 'required|numeric|min:0',
            'sale_price'   => 'required|numeric|min:0',
            'mrp_price'    => 'required|numeric|min:0',
            'stock_qty'    => 'required|numeric|min:0',
            'alert_qty'    => 'required|numeric|min:0',
            'reorder_qty'  => 'required|numeric|min:0',
            'is_active'    => 'nullable|boolean',
            'product_type' => 'required|in:ready_made,finished_product',
        ]);

        // Always keep POS-enabled
        $validated['is_pos_enabled'] = true;
        $validated['is_active']      = $request->has('is_active');

        if (empty($validated['sku'])) {
            $validated['sku'] = $product->sku ?? 'SKU-' . str_pad($product->id, 6, '0', STR_PAD_LEFT);
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $product->barcode ?? '880' . str_pad(mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        }
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $oldStock = $product->stock_qty;
        $product->update($validated);
        $newStock = $product->stock_qty;

        if ($oldStock != $newStock) {
            $diff = $newStock - $oldStock;
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => $diff > 0 ? 'Manual Edit (+)' : 'Manual Edit (-)',
                'quantity'   => $diff,
                'user_id'    => auth()->id(),
                'notes'      => 'POS Item stock updated from ' . $oldStock . ' to ' . $newStock,
            ]);
        }

        return redirect()->route('dashboard.pos-items')->with('success', 'POS Item updated successfully!');
    }

    public function toggleStock(Product $product): RedirectResponse
    {
        $oldStock = $product->stock_qty;
        if ($product->stock_qty > 0) {
            $product->update(['stock_qty' => 0]);
            $message = 'Product marked as Out of Stock.';
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => 'Stock Toggled (-)',
                'quantity'   => -$oldStock,
                'user_id'    => auth()->id(),
                'notes'      => 'Product marked as Out of Stock via toggle',
            ]);
        } else {
            $newQty = max(10, $product->alert_qty + 5);
            $product->update(['stock_qty' => $newQty]);
            $message = 'Product marked as In Stock.';
            \App\Models\StockLedger::create([
                'product_id' => $product->id,
                'type'       => 'Stock Toggled (+)',
                'quantity'   => $newQty,
                'user_id'    => auth()->id(),
                'notes'      => 'Product marked as In Stock via toggle',
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
    private function checkDeletedUnique(Request $request, $ignoreId = null): array
    {
        $errors = [];
        if ($request->filled('sku')) {
            $query = Product::onlyTrashed()->where('sku', $request->sku);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if ($query->exists()) {
                $errors['sku'] = 'This SKU is already taken by a deleted product. Please restore it or use another SKU.';
            }
        }
        if ($request->filled('barcode')) {
            $query = Product::onlyTrashed()->where('barcode', $request->barcode);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if ($query->exists()) {
                $errors['barcode'] = 'This Barcode is already taken by a deleted product. Please restore it or use another Barcode.';
            }
        }
        return $errors;
    }
}
