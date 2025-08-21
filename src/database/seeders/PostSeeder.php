<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [];

        for ($i = 1; $i <= 100; $i++) {
            $posts[] = [
                'title' => 'Post ' . $i,
                'content' => 'This is the content of post ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('posts')->insert($posts);
    }
}
