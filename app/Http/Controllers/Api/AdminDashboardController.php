<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\AdminDashboardRepositoryInterface;
use App\Traits\AdminAuthorization;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    use AdminAuthorization;

    public function __construct(
        private AdminDashboardRepositoryInterface $dashboardRepository
    ) {}

    public function stats(): JsonResponse
    {
        $authCheck = $this->authorizeAdmin();
        if ($authCheck !== true) {
            return $authCheck;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'stats' => $this->dashboardRepository->getStats(),
                'most_commented_posts' => $this->dashboardRepository->getMostCommentedPosts(),
                'most_used_tags' => $this->dashboardRepository->getMostUsedTags()
            ]
        ]);
    }
}
