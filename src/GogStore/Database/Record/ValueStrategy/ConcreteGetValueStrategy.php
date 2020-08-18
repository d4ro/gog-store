<?php

namespace GogStore\Database\Record\ValueStrategy;

class ConcreteGetValueStrategy extends AbstractGetValueStrategy
{
    public function getValue(string $field, $default = null)
    {
        return $this->data[$field] ?? $default;
    }

    public function hasValue(string $field): bool
    {
        return isset ($this->data[$field]);
    }
}