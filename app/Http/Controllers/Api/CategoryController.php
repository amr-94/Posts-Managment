<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Traits\AdminAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use AdminAuthorization;

    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->getAllWithPostsCount();

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        $category = $this->categoryRepository->create($validated);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء التصنيف بنجاح',
            'data' => $category
        ], 201);
    }

    public function update(CategoryRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        $category = $this->categoryRepository->update($id, $validated);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث التصنيف بنجاح',
            'data' => $category
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        $this->categoryRepository->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'تم حذف التصنيف بنجاح'
        ]);
    }

    public function show($id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }
}
