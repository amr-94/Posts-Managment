<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Repositories\Interfaces\AdminDashboardRepositoryInterface;

class AdminDashboardRepository implements AdminDashboardRepositoryInterface
{
    public function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
        ];
    }

    public function getMostCommentedPosts(int $limit = 5): array
    {
        return Post::withCount('comments')
            ->with(['user:id,name', 'category:id,name'])
            ->orderByDesc('comments_count')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'user_id', 'category_id', 'created_at'])
            ->toArray();
    }

    public function getMostUsedTags(int $limit = 5): array
    {
        return Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get(['id', 'name', 'slug'])
            ->toArray();
    }
}