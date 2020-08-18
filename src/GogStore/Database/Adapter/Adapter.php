<?php

namespace GogStore\Database\Adapter;

use GogStore\Database\Record\Record;
use GogStore\Pattern\Collection\Collection;

interface Adapter
{
    function getById(string $schema, string $id, string $type = null): Record;

    function find(string $schema, string $type = null, array $filter = [], int $limit = 0, int $offset = 0): Collection;

    function findJoin(string $schema, array $join, array $projection = [], string $type = null, array $filter = [], int $limit = 0, int $offset = 0): Collection;

    function delete(string $schema, array $filter, int $limit = 0, int $offset = 0): void;

    function getLastInsertedId(): string;

    function insert(string $schema, array $data): self;

    function updateById(string $schema, string $id, array $data): self;

    function deleteById(string $schema, string $id): self;

}