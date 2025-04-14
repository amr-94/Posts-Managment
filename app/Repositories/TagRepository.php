<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function getAllWithPostsCount()
    {
        return $this->model->withCount('posts')->get();
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getMostUsed($limit = 10)
    {
        return $this->model->withCount('posts')->orderByDesc('posts_count')->limit($limit)->get();
    }
}
