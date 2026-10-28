<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with('currency')->paginate(15);

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency_id' => [
                'required',
                'integer',
                Rule::exists('currencies', 'id'),
            ],
            'tax_cost' => ['required', 'numeric', 'min:0'],
            'manufacturing_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $product = Product::create($validated);
        $product->load('currency');

        return response()->json($product, 201);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with(['currency', 'prices.currency'])
            ->findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('currencies', 'id'),
            ],
            'tax_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'manufacturing_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);
        $product->load('currency');

        return response()->json($product);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ]);
    }
}
