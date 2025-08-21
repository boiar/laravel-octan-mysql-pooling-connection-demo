<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\PostService;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function postsCountWithoutPool(): JsonResponse
    {
        $count = $this->postService->countPostsNoPool();
        return response()->json([
            'mode' => 'no-pool',
            'count' => $count
        ]);
    }

    public function postsCountPool(): JsonResponse
    {
        $count = $this->postService->countPostsWithPool();
        return response()->json([
            'mode' => 'pool',
            'count' => $count
        ]);
    }
}
