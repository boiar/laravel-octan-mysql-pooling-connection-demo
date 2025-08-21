<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/posts-count-without-pool', [PostController::class, 'postsCountWithoutPool']);
Route::get('/pool', [PostController::class, 'postsCountPool']);
