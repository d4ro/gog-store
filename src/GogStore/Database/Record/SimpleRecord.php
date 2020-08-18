<?php

namespace GogStore\Database\Record;

/**
 * This is a simple implementation of an AbstractRecord class.
 * It gives an ability to validate data against a list of allowed fields.
 *
 * @package GogStore\Database\Record
 */
class SimpleRecord extends AbstractRecord
{
    private array $allowedFields = [];

    public function __construct(string $schema, array $data = [])
    {
        parent::__construct($schema, $data);
    }

    public function getAllowedFields(): array
    {
        return $this->allowedFields;
    }

    public function setAllowedFields(array $allowedFields): void
    {
        $this->allowedFields = $allowedFields;
    }

    /**
     * Perform simple validation against $allowedFields.
     * Fields not allowed are simply removed.
     */
    public function validate(): void
    {
        $this->data->replace(array_intersect_key($this->getData(false), array_flip($this->getAllowedFields())));
    }

    public function getNew(): SimpleRecord
    {
        return new self($this->getSchema(), $this->getData(false));
    }
}