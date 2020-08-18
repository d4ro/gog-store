<?php

namespace GogStore\Database\Adapter;

use GogStore\Config;

abstract class AbstractAdapter implements Adapter
{
    /**
     * Factory method to create instance of a database adapter.
     * Which adapter to use depends on the connection type in the config.
     * In this example only MySQL is supported.
     * Any unsupported types throw an exception.
     *
     * @param Config $config
     * @return static
     * @throws DatabaseAdapterException
     */
    public static function createAdapter(Config $config): self
    {
        switch ($config->getValue('DB_TYPE')) {
            case 'mysql':
                return new MySQL($config);
            default:
                throw new DatabaseAdapterException("Database connection type '{$config->getValue('DB_TYPE')}' is not supported.");
        }
    }
}