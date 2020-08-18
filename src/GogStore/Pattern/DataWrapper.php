<?php

namespace GogStore\Pattern;

use GogStore\Pattern\Collection\Map;

class DataWrapper extends Map implements Observable
{
    use ObservableTrait;

    public function __construct(array $data = [])
    {
        $this->initObservable();
        parent::__construct($data);
    }

    public static function empty()
    {
        return new static([]);
    }

    public function add($element): void
    {
        parent::add($element);
        $this->notifyChanged();
    }

    public function remove($element): void
    {
        parent::remove($element);
        $this->notifyChanged();
    }

    public function removeAt($position): void
    {
        parent::removeAt($position);
        $this->notifyChanged();
    }

    public function clear(): void
    {
        parent::clear();
        $this->notifyChanged();
    }

    public function replace(array $data): void
    {
        parent::replace($data);
        $this->notifyChanged();
    }

    public function patch(array $data): void
    {
        parent::patch($data);
        $this->notifyChanged();
    }

    public function offsetSet($offset, $element): void
    {
        parent::offsetSet($offset, $element);
        $this->notifyChanged();
    }

    public function offsetUnset($offset): void
    {
        parent::offsetUnset($offset);
        $this->notifyChanged();
    }
}