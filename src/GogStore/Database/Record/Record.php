<?php

namespace GogStore\Database\Record;

use GogStore\Database\Adapter\Adapter;
use GogStore\Pattern\Collection\Collection;

interface Record
{
    function getDbAdapter(): Adapter;

    function setDbAdapter(Adapter $db): void;

    function getSchema(): string;

    function getData(bool $withId = true): array;

    function getId();

    function setId($id): void;

    function isNew(): bool;

    function getNew(): Record;

    function getImmutable(): Record;

    function getAsType(string $type): Record;

    function getById($id): Record;

    function find(array $filter = [], int $limit = 0, int $offset = 0): Collection;

    function delete(array $filter, int $limit = 0, int $offset = 0): void;

    function getFields(): array;

    function hasValue($field): bool;

    function getValue($field, $default = null);

    function setValue($field, $value): void;

    function removeValue($field): void;

    function patch(array $data): void;

    function replace(array $data): void;

    function validate(): void;

    function save(): void;

    function insert(): void;

    function update(): void;

    function remove(): void;
}