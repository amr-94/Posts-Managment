<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\PostFilterRequest;
use App\Http\Requests\Posts\StorePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Traits\AdminAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use AdminAuthorization;

    public function __construct(private PostRepositoryInterface $postRepository) {}

    /**
     * Get posts list with filters
     *
     * @param PostFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PostFilterRequest $request): JsonResponse
    {
        $posts = $this->postRepository->getPublishedPosts($request->validated());

        return response()->json([
            'status' => true,
            'data' => $posts
        ]);
    }

    /**
     * Display a specific post with its comments
     */
    public function show($id): JsonResponse
    {
        $post = $this->postRepository->findPublishedPost($id);

        return response()->json([
            'status' => true,
            'data' => $post
        ]);
    }

    /**
     * Store a new post
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        $data = array_merge($request->validated(), [
            'user_id' => Auth::id(),
            'published_at' => now()
        ]);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = $this->postRepository->create($data);

        if ($request->has('tags')) {
            $this->postRepository->syncTags($post->id, $request->tags);
        }

        return response()->json([
            'status' => true,
            'message' => 'the post has been created successfully',
            'data' => $post->load(['category', 'tags'])
        ], 201);
    }

    /**
     * Update an existing post
     */
    public function update(UpdatePostRequest $request, $id): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->postRepository->updateImage($id, $request->file('featured_image'));
        }

        $post = $this->postRepository->update($id, $data);

        if ($request->has('tags')) {
            $this->postRepository->syncTags($post->id, $request->tags);
        }

        return response()->json([
            'status' => true,
            'message' => 'the post has been updated successfully',
            'data' => $post->load(['category', 'tags'])
        ]);
    }

    /**
     * Delete a post
     */
    public function destroy($id): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        $this->postRepository->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'the post has been deleted successfully'
        ]);
    }
}
