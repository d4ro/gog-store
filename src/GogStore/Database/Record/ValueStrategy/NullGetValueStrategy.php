<?php

namespace GogStore\Database\Record\ValueStrategy;

class NullGetValueStrategy extends AbstractGetValueStrategy
{
    public function getValue(string $field, $default = null)
    {
        return null;
    }

    public function hasValue(string $field): bool
    {
        return false;
    }
}