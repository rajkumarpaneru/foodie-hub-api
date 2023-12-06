<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Rules\UniqueForTheCategory;
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
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'max:191', new UniqueForTheCategory($request->category_id)],
            'rank' => 'required|integer|min:1',
            'description' => 'nullable'
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
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'max:191', new UniqueForTheCategory($request->category_id, $product)],
            'rank' => 'required|integer|min:1',
            'description' => 'nullable'
        ]);

        $product->update($validated);
        $product->refresh();

        $response = ProductResource::make($product);
        return response()->json($response);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json([
            'id' => $product->id,
        ], 200);
    }
}
