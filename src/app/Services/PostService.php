<?php

namespace App\Services;

use App\Services\MySQLPool;
use Illuminate\Support\Facades\DB;

class PostService
{
    protected $pool;

    public function __construct(MySQLPool $pool)
    {
        $this->pool = $pool;
    }


    /**
     * Classic Laravel DB (no pool)
     */
    public function countPostsNoPool(): int
    {
        $conn = DB::connection('mysql')->getPdo();
        $stmt = $conn->query("SELECT COUNT(*) as c FROM posts");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Using Swoole MySQL pool
     */
    public function countPostsWithPool(): int
    {
        $mysql = $this->pool->get(); // get connection from pool
        $result = $mysql->query("SELECT COUNT(*) as c FROM posts");
        $this->pool->put($mysql); // return connection to pool

        return (int) ($result[0]['c'] ?? 0);
    }


}
