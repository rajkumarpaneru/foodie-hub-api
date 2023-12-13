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
        $categories = Category::query()->orderBy('created_at', 'ASC')->get();
        $response = CategoryResource::collection($categories);
        return response()->json($response);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:191|unique:categories,name',
            'rank' => 'required|integer|min:1',
            'description' => 'nullable',
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'rank' => $validated['rank'],
            'description' => $validated['description']
        ]);

        $category->addMedia($validated['image'])
            ->usingName('image')
            ->toMediaCollection();

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
            'description' => 'nullable',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'rank' => $validated['rank'],
            'description' => $validated['description']
        ]);

        if (isset($validated['image'])) {
            $media = $category->getMedia()->where('name', 'image')->first();
            if ($media) $media->delete();

            $category->addMedia($validated['image'])
                ->usingName('image')
                ->toMediaCollection();
        }
        $category->refresh();
        $response = CategoryResource::make($category);
        return response()->json($response);
    }

    public function destroy(Category $category): JsonResponse
    {
//        $media = $category->getMedia()->where('name', 'image')->first();
//        if ($media) $media->delete();

        $category->delete();

        return response()->json([
            'id' => $category->id,
        ], 200);
    }
}
