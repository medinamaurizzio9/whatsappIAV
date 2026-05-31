<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Intention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('knowledge.products.index', [
            'products' => Product::with('intentions')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge.products.create', ['product' => new Product(['is_active' => true]), 'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->withFiles($request, $this->validated($request));
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $product = Product::create($data);
        $product->intentions()->sync($intentions);

        return redirect()->route('products.index')->with('status', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('knowledge.products.show', ['product' => $product->load('intentions')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('knowledge.products.edit', ['product' => $product, 'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->withFiles($request, $this->validated($request, false), $product);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $product->update($data);
        $product->intentions()->sync($intentions);

        return redirect()->route('products.index')->with('status', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Producto eliminado correctamente.');
    }

    private function validated(Request $request, bool $creating = true): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'main_image' => ['nullable', 'image', 'max:4096'],
            'catalog_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
            'intentions' => ['nullable', 'array'],
            'intentions.*' => ['exists:intentions,id'],
        ]) + ['is_active' => false];
    }

    private function withFiles(Request $request, array $data, ?Product $product = null): array
    {
        if ($request->hasFile('main_image')) {
            if ($product?->main_image_path) {
                Storage::disk('public')->delete($product->main_image_path);
            }
            $data['main_image_path'] = $request->file('main_image')->store('knowledge/products/images', 'public');
        }

        if ($request->hasFile('catalog_pdf')) {
            if ($product?->catalog_pdf_path) {
                Storage::disk('public')->delete($product->catalog_pdf_path);
            }
            $data['catalog_pdf_path'] = $request->file('catalog_pdf')->store('knowledge/products/catalogs', 'public');
        }

        unset($data['main_image'], $data['catalog_pdf']);

        return $data;
    }
}
