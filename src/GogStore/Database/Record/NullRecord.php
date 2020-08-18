<?php

namespace GogStore\Database\Record;

use GogStore\Database\Record\ValueStrategy\MutableSetValueStrategy;
use GogStore\Database\Record\ValueStrategy\NullGetValueStrategy;

class NullRecord extends AbstractRecord
{
    public function __construct(string $schema)
    {
        // we don't want any data just to be sure
        parent::__construct($schema, []);

        // this time  we want to be sure that a getter returns nothing
        $this->getValueStrategy = new NullGetValueStrategy($this->data);
        $this->setValueStrategy = new MutableSetValueStrategy($this->data);
    }

    public function getData(bool $withId = true): array
    {
        return [];
    }

    public function validate(): void
    {
        // everything is fine
    }

    public function getNew(): NullRecord
    {
        return new NullRecord($this->getSchema());
    }

    public function insert(): void
    {

    }

    public function update(): void
    {

    }

    public function remove(): void
    {

    }

}