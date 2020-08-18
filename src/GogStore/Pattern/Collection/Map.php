<?php

namespace GogStore\Pattern\Collection;

use GogStore\GogStoreException;

class Map extends AbstractCollection
{
    /**
     * Gets value of a key.
     * Throws an exception if a key does not exists.
     *
     * @param mixed $key
     * @return mixed
     * @throws GogStoreException
     */
    public function get($key)
    {
        if (!$this->has($key))
            throw new GogStoreException("This map does not contains key '$key'.");

        return $this->data[$key];
    }

    public function getKeys(): array
    {
        return array_keys($this->data);
    }

    public function getValues(): array
    {
        return array_values($this->data);
    }
}