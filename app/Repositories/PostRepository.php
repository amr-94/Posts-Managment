<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    /**
     * Get published posts with filters
     */
    public function getPublishedPosts($filters = [])
    {
        $query = $this->model->query()
            ->with(['category:id,name', 'user:id,name', 'tags:id,name'])
            ->whereNotNull('published_at');

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by tag
        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag_id']);
            });
        }

        // Filter by keyword in title or body
        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['keyword']}%")
                    ->orWhere('body', 'like', "%{$filters['keyword']}%");
            });
        }

        return $query->latest('published_at')->paginate(10);
    }

    /**
     * Find a published post by ID with its relationships
     */
    public function findPublishedPost($id)
    {
        return $this->model
            ->with(['category:id,name', 'user:id,name', 'tags:id,name'])
            ->whereNotNull('published_at')
            ->findOrFail($id);
    }

    public function getMostCommentedPosts($limit = 5)
    {
        return $this->model
            ->withCount('allComments')
            ->whereNotNull('published_at')
            ->orderByDesc('all_comments_count')
            ->limit($limit)
            ->get();
    }

    public function getTotalPosts()
    {
        return $this->model->whereNotNull('published_at')->count();
    }

    /**
     * Sync tags for a post
     */
    public function syncTags($postId, $tagIds)
    {
        $post = $this->find($postId);
        $post->tags()->sync($tagIds);
        return $post;
    }

    public function updateImage($id, $image)
    {
        $post = $this->find($id);
        $this->deleteOldImage($post);
        return $image->store('posts', 'public');
    }

    protected function deleteOldImage($post): void
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
    }

    /**
     * Delete a post and its image
     */
    public function delete($id)
    {
        $post = $this->find($id);
        $this->deleteOldImage($post);
        return $post->delete();
    }

    public function createPost(array $data, ?array $tags = null)
    {
        // Prepare post data
        $postData = [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'body' => $data['body'],
            'category_id' => $data['category_id'],
            'user_id' => $data['user_id'],
            'published_at' => now()
        ];

        // Handle featured image if exists
        if (isset($data['featured_image'])) {
            $postData['featured_image'] = $data['featured_image'];
        }

        // Create post
        $post = $this->create($postData);

        // Sync tags if provided
        if ($tags) {
            $this->syncTags($post->id, $tags);
        }

        return $post->load(['category', 'tags']);
    }

    public function updatePost(int $id, array $data, ?array $tags = null)
    {
        // post data
        $postData = [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'body' => $data['body'],
            'category_id' => $data['category_id']
        ];

        // Handle featured image if exists
        if (isset($data['featured_image'])) {
            $postData['featured_image'] = $data['featured_image'];
        }

        // Update post
        $post = $this->update($id, $postData);

        // Sync tags if provided
        if ($tags) {
            $this->syncTags($post->id, $tags);
        }

        return $post->load(['category', 'tags']);
    }
}