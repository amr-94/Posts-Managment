<?php

namespace App\Repositories\Interfaces;

interface AdminDashboardRepositoryInterface
{
    /**
     * Get admin dashboard statistics
     */
    public function getStats(): array;

    /**
     * Get most commented posts
     */
    public function getMostCommentedPosts(int $limit = 5): array;

    /**
     * Get most used tags
     */
    public function getMostUsedTags(int $limit = 5): array;
}
