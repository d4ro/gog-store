<?php

namespace GogStore\Database\Record\ValueStrategy;

class MutableSetValueStrategy extends AbstractSetValueStrategy
{
    public function setValue(string $field, $value): void
    {
        $this->data[$field] = $value;
    }

    public function removeValue(string $field): void
    {
        unset ($this->data[$field]);
    }
}