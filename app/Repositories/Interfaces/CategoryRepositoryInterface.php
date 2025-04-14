<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithPostsCount();
    public function findBySlug($slug);
}
