<?php

namespace GogStore\Pattern;

use GogStore\Pattern\Collection\Map;

class Registry
{
    private static ?Map $data = null;

    public static function set($key, $value): void
    {
        if (null === static::$data)
            static::$data = new Map();

        static::$data->set($key, $value);
    }

    public static function get($key, $default = null)
    {
        if (null === static::$data || !static::$data->has($key))
            return $default;

        return static::$data->get($key);
    }

    public static function has($key): bool
    {
        if (null === static::$data || !static::$data->has($key))
            return false;

        return static::$data->has($key);
    }

    public static function unset($key): void
    {
        if (null === static::$data || !static::$data->has($key))
            return;

        static::$data->remove($key);
    }
}