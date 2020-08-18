<?php

namespace GogStore\Pattern\Collection;

/**
 * It meant to be List, but "list" is a keyword and cannot be user unfortunately.
 *
 * @package GogStore\Pattern\Collection
 */
class Lst extends AbstractCollection
{
    public function insert(int $position, $element): void
    {
        $this->data = array_splice($this->data, $position, 0, [$element]);
    }

    public function set($position, $element): void
    {
        $position = (int)$position;
        parent::set($position, $element);
    }

    public function get($position)
    {
        $position = (int)$position;
        return parent::get($position);
    }

    public function has($position): bool
    {
        $position = (int)$position;
        return parent::has($position);
    }

    public function removeAt($position): void
    {
        $position = (int)$position;
        parent::removeAt($position);
    }
}