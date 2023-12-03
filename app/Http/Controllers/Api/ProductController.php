<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::get();
        $response = ProductResource::make($products);
        return response()->json($response);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:191',
            'rank' => 'required|integer|min:1',
        ]);

        $product = Product::create($validated);
        $response = ProductResource::make($product);
        return response()->json($response);
    }

    public function show(Product $product): JsonResponse
    {
        $response = ProductResource::make($product);
        return response()->json($response);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:191',
            'rank' => 'required|integer|min:1',
        ]);

        $product->update($validated);
        $product->refresh();

        $response = ProductResource::make($product);
        return response()->json($response);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
