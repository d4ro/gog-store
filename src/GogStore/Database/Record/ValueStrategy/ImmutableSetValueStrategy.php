<?php

namespace GogStore\Database\Record\ValueStrategy;

class ImmutableSetValueStrategy extends AbstractSetValueStrategy
{
    /**
     * Denies setting a value of an immutable strategy.
     * Throws an exception.
     *
     * @param string $field
     * @param mixed $value
     * @throws ValueStrategyException
     */
    public function setValue(string $field, $value): void
    {
        throw new ValueStrategyException('Cannot set value. This object is immutable.');
    }

    /**
     * Denies removing a value from an immutable strategy.
     * Throws an exception.
     *
     * @param string $field
     * @throws ValueStrategyException
     */
    public function removeValue(string $field): void
    {
        throw new ValueStrategyException('Cannot remove value. This object is immutable.');
    }
}