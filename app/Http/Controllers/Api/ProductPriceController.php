<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductPriceController extends Controller
{
    public function index(string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);

        $prices = $product->prices()
            ->with('currency')
            ->get();

        return response()->json($prices);
    }

    public function store(Request $request, string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'currency_id' => [
                'required',
                'integer',
                Rule::exists('currencies', 'id'),
                Rule::unique('product_prices', 'currency_id')
                    ->where('product_id', $product->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $price = $product->prices()->create($validated);
        $price->load('currency');

        return response()->json($price, 201);
    }
}
