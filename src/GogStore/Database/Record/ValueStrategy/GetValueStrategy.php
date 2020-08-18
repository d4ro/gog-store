<?php

namespace GogStore\Database\Record\ValueStrategy;

interface GetValueStrategy
{
    public function getValue(string $field, $default = null);

    public function hasValue(string $field): bool;
}