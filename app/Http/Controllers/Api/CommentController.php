<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(private CommentRepositoryInterface $commentRepository) {}

    public function index($postId): JsonResponse
    {
        $comments = $this->commentRepository->getPostComments($postId);

        return response()->json([
            'status' => true,
            'data' => $comments
        ]);
    }

    public function store(CommentRequest $request, $postId): JsonResponse
    {
        $validated = $request->validated();

        $comment = $this->commentRepository->create([
            'content' => $validated['content'],
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة التعليق بنجاح',
            'data' => $comment->load('user:id,name')
        ], 201);
    }

    public function update(CommentRequest $request, $id): JsonResponse
    {
        if (!$this->commentRepository->isOwner($id, Auth::id())) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بتعديل هذا التعليق'
            ], 403);
        }

        $validated = $request->validated();

        $comment = $this->commentRepository->update($id, [
            'content' => $validated['content']
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث التعليق بنجاح',
            'data' => $comment
        ]);
    }

    public function destroy($id): JsonResponse
    {
        if (!$this->commentRepository->isOwner($id, Auth::id())) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بحذف هذا التعليق'
            ], 403);
        }

        $this->commentRepository->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'تم حذف التعليق بنجاح'
        ]);
    }

    public function replies($commentId): JsonResponse
    {
        $replies = $this->commentRepository->getReplies($commentId);

        return response()->json([
            'status' => true,
            'data' => $replies
        ]);
    }
}
