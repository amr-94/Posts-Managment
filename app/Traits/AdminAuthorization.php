<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait AdminAuthorization
{
    protected function authorizeAdmin(): JsonResponse|bool
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return true;
    }
}
