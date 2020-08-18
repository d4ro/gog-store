<?php

namespace GogStore\Pattern\Collection;

class Set extends AbstractCollection
{
    public function add($element): void
    {
        if ($this->contains($element))
            return;

        parent::add($element);
    }

    public function set($position, $element): void
    {
        $oldPosition = array_search($element, $this->data, true);
        if (null !== $oldPosition)
            unset($this->data[$oldPosition]);

        parent::set($position, $element);
    }

    public function remove($element): void
    {
        $position = array_search($element, $this->data, true);
        if (null !== $position)
            unset($this->data[$position]);
    }

    public function getValues(): array
    {
        return array_values($this->data);
    }
}