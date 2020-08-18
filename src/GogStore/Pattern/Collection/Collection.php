<?php

namespace GogStore\Pattern\Collection;

interface Collection
{
    /**
     * Return an array representation of an underlying data.
     *
     * @return array
     */
    function toArray(): array;

    /**
     * Add an element to a collection.
     *
     * @param mixed $element
     */
    function add($element): void;

    /**
     * Set an element at a given position.
     *
     * @param mixed $position
     * @param mixed $element
     */
    function set($position, $element): void;

    /**
     * Get an element from a given position.
     *
     * @param mixed $position
     * @return mixed
     */
    function get($position);

    /**
     * Returns true if a collection has an element on a specific position.
     *
     * @param $position
     * @return bool
     */
    function has($position): bool;

    /**
     * Returns true if a collection contains an element.
     *
     * @param $element
     * @return bool
     */
    function contains($element): bool;

    /**
     * Remove an element from a collection.
     *
     * @param mixed $element
     */
    function remove($element): void;

    /**
     * Remove an element from a collection at a given position.
     *
     * @param mixed $position
     */
    function removeAt($position): void;

    /**
     * Returns a position of a given element.
     *
     * @param mixed $element
     * @return mixed
     */
    function positionOf($element);

    /**
     * Clear a collection.
     */
    function clear(): void;

    /**
     * Replace a whole content of a collection.
     *
     * @param array $data
     */
    function replace(array $data): void;

    /**
     * Add many elements to a collection possibly replacing existing ones.
     *
     * @param array $data
     */
    function patch(array $data): void;

    function count(): int;
}