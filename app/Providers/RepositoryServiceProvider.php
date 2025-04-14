<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\{PostRepositoryInterface, CategoryRepositoryInterface, TagRepositoryInterface, CommentRepositoryInterface, AdminDashboardRepositoryInterface};
use App\Repositories\{PostRepository, CategoryRepository, TagRepository, CommentRepository, AdminDashboardRepository};

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(AdminDashboardRepositoryInterface::class, AdminDashboardRepository::class);
    }
}