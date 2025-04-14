<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }

    public function getPostComments($postId)
    {
        return $this->model
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->with(['user:id,name', 'replies.user:id,name'])
            ->get();
    }

    public function getReplies($commentId)
    {
        return $this->model->where('parent_id', $commentId)->with('user:id,name')->get();
    }

    public function isOwner($commentId, $userId)
    {
        return $this->model->where('id', $commentId)->where('user_id', $userId)->exists();
    }
}
