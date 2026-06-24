<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filling;
use App\Models\Grind;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $products = Product::when(
            $search,
            fn($q) => $q->where('name', 'like', "%{$search}%")
        )->latest()->paginate(30)->withQueryString();

        $fillings = Filling::latest()->get();
        $grinds   = Grind::latest()->get();

        return view('admin.products.index', compact('products', 'fillings', 'grinds', 'search'));
    }

    // ── Products ────────────────────────────────────────────────────────────

    public function storeProduct(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:100|unique:products,name']);
        Product::create(['name' => $validated['name']]);

        return back()->with('success', "Product \"{$validated['name']}\" added.");
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:products,name,' . $product->id,
        ]);
        $product->update($validated);

        return back()->with('success', "Product updated.");
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();

        return back()->with('success', "Product \"{$product->name}\" removed.");
    }

    public function restoreProduct(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return back()->with('success', "Product \"{$product->name}\" restored.");
    }

    // ── Fillings ─────────────────────────────────────────────────────────────

    public function storeFilling(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:100|unique:fillings,name']);
        Filling::create(['name' => $validated['name']]);

        return back()->with('success', "Filling \"{$validated['name']}\" added.");
    }

    public function updateFilling(Request $request, Filling $filling)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:fillings,name,' . $filling->id,
        ]);
        $filling->update($validated);

        return back()->with('success', "Filling updated.");
    }

    public function destroyFilling(Filling $filling)
    {
        $filling->delete();

        return back()->with('success', "Filling \"{$filling->name}\" removed.");
    }

    // ── Grinds ────────────────────────────────────────────────────────────────

    public function storeGrind(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:100|unique:grinds,name']);
        Grind::create(['name' => $validated['name']]);

        return back()->with('success', "Grind option \"{$validated['name']}\" added.");
    }

    public function updateGrind(Request $request, Grind $grind)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:grinds,name,' . $grind->id,
        ]);
        $grind->update($validated);

        return back()->with('success', "Grind option updated.");
    }

    public function destroyGrind(Grind $grind)
    {
        $grind->delete();

        return back()->with('success', "Grind option \"{$grind->name}\" removed.");
    }
}
