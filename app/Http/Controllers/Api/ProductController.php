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
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'nullable|exists:categories,id',
        ]);
        $products = Product::when(isset($validated['category']), function ($q) use ($validated) {
            $q->where('category_id', $validated['category']);
        })
            ->orderBy('products.rank', 'ASC')
            ->get();

        $response = ProductResource::collection($products);
        return response()->json([
            'data' => $response,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'max:191', new UniqueForTheCategory($request->category_id)],
            'rank' => 'required|integer|min:1',
            'description' => 'nullable',
            'price' => 'required|min:0|max:999',
            'type' => 'required|in:vegan,veg,non-veg',
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'rank' => $validated['rank'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'type' => $validated['type'],
        ]);

        $product->addMedia($validated['image'])
            ->usingName('image')
            ->toMediaCollection();

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
            'description' => 'nullable',
            'price' => 'required|min:0|max:999',
            'type' => 'required|in:vegan,veg,non-veg',
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'rank' => $validated['rank'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'type' => $validated['type'],
        ]);

        if (isset($validated['image'])) {
            $media = $product->getMedia()->where('name', 'image')->first();
            if ($media) $media->delete();

            $product->addMedia($validated['image'])
                ->usingName('image')
                ->toMediaCollection();
        }
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

    public function getTypes(): JsonResponse
    {
        $response = ['vegan', 'veg', 'non-veg'];

        return response()->json([
            'data' => $response,
        ], 200);
    }
}
