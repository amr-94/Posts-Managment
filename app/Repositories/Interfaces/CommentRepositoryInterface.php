<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getPostComments($postId);
    public function getReplies($commentId);
    public function isOwner($commentId, $userId);
}