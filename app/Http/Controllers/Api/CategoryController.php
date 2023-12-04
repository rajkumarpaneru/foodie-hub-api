<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::get();
        $response = CategoryResource::make($categories);
        return response()->json($response);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:191|unique:categories,name',
            'rank' => 'required|integer|min:1',
            'description' => 'nullable'
        ]);

        $category = Category::create($validated);
        $response = CategoryResource::make($category);
        return response()->json($response);
    }

    public function show(Category $category): JsonResponse
    {
        $response = CategoryResource::make($category);
        return response()->json($response);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:191|unique:categories,name,' . $category->id,
            'rank' => 'required|integer|min:1',
        ]);

        $category->update($validated);
        $category->refresh();

        $response = CategoryResource::make($category);
        return response()->json($response);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
