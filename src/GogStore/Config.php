<?php

namespace GogStore;

use Dotenv\Dotenv;

class Config
{
    private static ?Config $instance = null;

    public static function getInstance(): Config
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected array $config;

    private function __construct()
    {
        $dotEnv = Dotenv::createArrayBacked(APP_PATH, '.env');
        $this->config = $dotEnv->load();
        $dotEnv->required(['DB_TYPE', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'LOG_METHOD']);
    }

    public function getValue($key)
    {
        return $this->config[$key] ?? null;
    }
}