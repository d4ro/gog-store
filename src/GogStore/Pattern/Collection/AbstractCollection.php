<?php

namespace GogStore\Pattern\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

class AbstractCollection implements Collection, IteratorAggregate, Countable, ArrayAccess
{
    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function add($element): void
    {
        $this->data[] = $element;
    }

    public function set($position, $element): void
    {
        $this->data[$position] = $element;
    }

    public function get($position)
    {
        return $this->data[$position];
    }

    public function has($position): bool
    {
        return array_key_exists($position, $this->data);
    }

    public function contains($element): bool
    {
        return in_array($element, $this->data, true);
    }

    public function remove($element): void
    {
        while (null !== ($position = array_search($element, $this->data, true)))
            unset($this->data[$position]);
    }

    public function removeAt($position): void
    {
        unset($this->data[$position]);
    }

    public function positionOf($element)
    {
        return array_search($element, $this->data, true);
    }

    public function clear(): void
    {
        $this->data = [];
    }

    public function replace(array $data): void
    {
        $this->data = $data;
    }

    public function patch(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->data);
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $element): void
    {
        $this->data[$offset] = $element;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}