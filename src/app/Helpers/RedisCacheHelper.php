<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisCacheHelper
{
    public static function get($key)
    {
        $cachedData = Redis::get($key);

        return $cachedData ? json_decode($cachedData, true) : null;
    }

    public static function put($key, $data, $expirationInSeconds = null)
    {
        $jsonData = json_encode($data);

        if ($expirationInSeconds !== null) {
            Redis::setex($key, $expirationInSeconds, $jsonData);
        } else {
            Redis::set($key, $jsonData);
        }
    }

    public static function forget($key)
    {
        Redis::del($key);
    }

    private function logAction($key, $action, $data)
    {
        $logKey = $key;
        $logEntry = [
            'action' => $action,
            'timestamp' => now(),
            'message' => $data,
        ];

        $this->redisModel->pushToList($logKey, json_encode($logEntry));
    }
}