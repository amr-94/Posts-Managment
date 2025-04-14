<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Traits\AdminAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TagController extends Controller
{
    use AdminAuthorization;

    public function __construct(
        private TagRepositoryInterface $tagRepository
    ) {}

    public function index(): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        $tags = $this->tagRepository->getAllWithPostsCount();

        return response()->json([
            'status' => true,
            'data' => $tags
        ]);
    }

    /**
     * create tag by admin
     */
    public function store(TagRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        $tag = $this->tagRepository->create($validated);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الوسم بنجاح',
            'data' => $tag
        ], 201);
    }

    /**
     * update tag by admin
     */
    public function update(TagRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        $tag = $this->tagRepository->update($id, $validated);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الوسم بنجاح',
            'data' => $tag
        ]);
    }

    /**
     * delete tag by admin
     */
    public function destroy(int $id): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        $this->tagRepository->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الوسم بنجاح'
        ]);
    }
}