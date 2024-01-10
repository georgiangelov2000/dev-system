<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;

class RedisModel
{
    /**
     * Задава стойността на даден ключ в Redis.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $expiration Време за изтичане в секунди (необязателно)
     */
    public function set($key, $value, $expiration = null)
    {
        if ($expiration) {
            Redis::setex($key, $expiration, $value);
        } else {
            Redis::set($key, $value);
        }
    }

    /**
     * Връща стойността, асоциирана с даден ключ в Redis.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return Redis::get($key);
    }

    /**
     * Добавя стойност към множество в Redis.
     *
     * @param string $setKey
     * @param mixed $value
     * @return int Броят на успешно добавените стойности
     */
    public function addToSet($setKey, $value)
    {
        return Redis::sadd($setKey, $value);
    }

    /**
     * Връща всички членове на множеството в Redis.
     *
     * @param string $setKey
     * @return array
     */
    public function getSetMembers($setKey)
    {
        return Redis::smembers($setKey);
    }

    /**
     * Добавя стойност към края на списъка в Redis.
     *
     * @param string $listKey
     * @param mixed $value
     * @return int Новата дължина на списъка
     */
    public function pushToList($listKey, $value)
    {
        return Redis::rpush($listKey, $value);
    }

    /**
     * Връща всички елементи от списъка в Redis.
     *
     * @param string $listKey
     * @return array
     */
    public function getList($listKey)
    {
        return Redis::lrange($listKey, 0, -1);
    }

    // Други методи по ваш избор...
}
