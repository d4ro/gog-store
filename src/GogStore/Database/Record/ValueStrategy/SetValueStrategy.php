<?php

namespace GogStore\Database\Record\ValueStrategy;

interface SetValueStrategy
{
    public function setValue(string $field, $value): void;

    public function removeValue(string $field): void;
}