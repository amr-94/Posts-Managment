<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function getPublishedPosts(array $filters = []);
    public function findPublishedPost($id);
    public function syncTags($postId, array $tagIds);
    public function getMostCommentedPosts($limit = 5);
    public function getTotalPosts();
    public function updateImage($id, $image);
}
