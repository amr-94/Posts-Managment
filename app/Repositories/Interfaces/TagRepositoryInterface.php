<?php

namespace App\Repositories\Interfaces;

interface TagRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithPostsCount();
    public function findBySlug($slug);
    public function getMostUsed($limit = 10);
}
