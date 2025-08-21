<?php

namespace App\Services;

use Swoole\Coroutine\MySQL;
use Swoole\Coroutine\Channel;

class MySQLPool
{
    private static ?Channel $pool = null;

    public static function getPool(int $size = 10): Channel
    {
        // Only create the pool if it hasn't been created yet
        if (self::$pool === null) {

            // Check if Swoole Coroutine\MySQL class exists
            if (!class_exists(MySQL::class)) {
                throw new \Exception('Swoole\Coroutine\MySQL not found. Are you running this in a Swoole context?');
            }

            self::$pool = new Channel($size);

            for ($i = 0; $i < $size; $i++) {
                // Use `go` to create coroutines for connections
                go(function () {
                    $mysql = new MySQL();
                    $mysql->connect([
                                        'host' => env('DB_HOST'),
                                        'user' => env('DB_USERNAME'),
                                        'password' => env('DB_PASSWORD'),
                                        'database' => env('DB_DATABASE'),
                                    ]);
                    self::$pool->push($mysql);
                });
            }
        }

        return self::$pool;
    }

    public static function get(): MySQL
    {
        return self::getPool()->pop();
    }

    public static function put(MySQL $mysql): void
    {
        self::getPool()->push($mysql);
    }
}
