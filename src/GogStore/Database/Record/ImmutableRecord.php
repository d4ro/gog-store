<?php

namespace GogStore\Database\Record;

use GogStore\Database\Record\ValueStrategy\ConcreteGetValueStrategy;
use GogStore\Database\Record\ValueStrategy\ImmutableSetValueStrategy;

/**+
 * Class ImmutableRecord represents record, which data cannot be changed.
 * Any try to change its data throws an exception.
 *
 * @package GogStore\Database\Record
 */
class ImmutableRecord extends AbstractRecord
{
    public function __construct(string $schema, array $data)
    {
        parent::__construct($schema, $data);

        // this time we do not want to have mutable records
        $this->getValueStrategy = new ConcreteGetValueStrategy($this->data);
        $this->setValueStrategy = new ImmutableSetValueStrategy($this->data);
    }

    public function getNew(): ImmutableRecord
    {
        return new ImmutableRecord($this->getSchema(), $this->getData(true));
    }

    /**
     * Denies setting an id of an immutable record.
     * Throws an exception.
     *
     * @param mixed $id
     * @throws RecordException
     */
    public function setId($id): void
    {
        throw new RecordException('Cannot change an id of an immutable record.');
    }

    public function validate(): void
    {
        // everything is fine
    }
}