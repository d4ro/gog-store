<?php

namespace GogStore\Database\Record;

use GogStore\Database\Adapter\Adapter;
use GogStore\Database\Adapter\DatabaseAdapterException;
use GogStore\Database\Record\ValueStrategy\ConcreteGetValueStrategy;
use GogStore\Database\Record\ValueStrategy\GetValueStrategy;
use GogStore\Database\Record\ValueStrategy\MutableSetValueStrategy;
use GogStore\Database\Record\ValueStrategy\SetValueStrategy;
use GogStore\Pattern\Collection\Collection;
use GogStore\Pattern\DataWrapper;
use GogStore\Pattern\Observable;
use GogStore\Pattern\ObservableTrait;
use GogStore\Pattern\Observer;
use JsonSerializable;

/**
 * Class AbstractRecord is a base class for every database model.
 * It requires to provide a schema name (usually table or collection)
 * and a database adapter to be able to persist a data.
 * It is recommended to provide those information upon instantiate.
 *
 * @package GogStore\Database
 */
abstract class AbstractRecord implements Record, Observable, Observer, JsonSerializable
{
    use ObservableTrait;

    private ?Adapter $db = null;
    private string $schema;
    protected DataWrapper $data;
    protected string $id = '';
    protected GetValueStrategy $getValueStrategy;
    protected SetValueStrategy $setValueStrategy;

    public function __construct(string $schema, array $data = [])
    {
        $this->initObservable();

        $this->schema = $schema;
        $this->data = new DataWrapper($data);
        $this->data->attach($this);

        $this->setIdFromData();

        // the default strategy is to have mutable records
        $this->getValueStrategy = new ConcreteGetValueStrategy($this->data);
        $this->setValueStrategy = new MutableSetValueStrategy($this->data);
    }

    public function __destruct()
    {
        $this->data->detach($this);
    }

    /**
     * Throws an exception if a database adapter has not been set.
     *
     * @throws DatabaseAdapterException
     */
    protected function ensureHasDb(): void
    {
        if (null === $this->db)
            throw new DatabaseAdapterException('Database adapter has not been set. Cannot execute any query.');
    }

    /**
     * Get previously set database adapter.
     * Throws an exception if a database adapter has not been set.
     *
     * @return Adapter
     * @throws DatabaseAdapterException
     */
    public function getDbAdapter(): Adapter
    {
        $this->ensureHasDb();
        return $this->db;
    }

    public function setDbAdapter(Adapter $db): void
    {
        $this->db = $db;
    }

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function getData(bool $withId = true): array
    {
        $data = $this->data->toArray();
        if ($withId) {
            $data['id'] = $this->getId();
        }
        return $data;
    }

    public function getId()
    {
        return (string)$this->id;
    }

    public function setId($id): void
    {
        $this->id = (string)$id;
    }

    protected function setIdFromData(): void
    {
        if (isset($this->data['id'])) {
            $this->setId($this->data['id']);
            unset($this->data['id']);
        }
    }

    public function isNew(): bool
    {
        return !$this->id;
    }

    /**
     * Creates a clone of an object.
     * It requires a concrete class of a record to have an empty constructor.
     * To get this method to work in a class without an empty constructor,
     * it is mandatory to override this method and provide a proper construction.
     *
     * @return static
     */
    public function getNew(): self
    {
        // it will work only if there is an empty constructor
        $new = new static();
        $new->data->replace($this->getData(false));
        $new->schema = $this->schema;
        return $new;
    }

    /**
     * Creates a new immutable record from this object's data.
     *
     * @return Record
     */
    public function getImmutable(): Record
    {
        return new ImmutableRecord($this->schema, $this->getData(true));
    }

    /**
     * Creates a clone of an object in a concrete type.
     * Handy if for some reason you have to have your record in a specific type.
     * It requires a concrete class of a record to have an empty constructor.
     * Throws an exception if a type is invalid.
     *
     * @param string $type
     * @return Record
     * @throws RecordException
     */
    public function getAsType(string $type): Record
    {
        // it will work only if there is an empty constructor
        $new = new $type();
        if (!($new instanceof Record))
            throw new RecordException("Given type in not a descendant of a Record class.");

        $new->data->replace($this->getData(true));
        $new->setIdFromData();
        $new->schema = $this->schema;
        return $new;
    }

    /**
     * Retrieves a record from a database of a given id.
     * Returns NullRecord if no record was found.
     * Throws an exception if a database adapter has not been set.
     *
     * @param mixed $id
     * @return Record
     * @throws DatabaseAdapterException
     */
    public function getById($id): Record
    {
        $this->ensureHasDb();
        return $this->db->getById($this->getSchema(), $id, static::class);
    }

    /**
     * Retrieves a collection of records from a database.
     * Filtering is done by doing a simple comparison between corresponding
     * columns (keys of array $filter) and literal values (values of array $filter)
     * and joining those comparisons by a logic AND operator.
     * Throws an exception if a database adapter has not been set.
     *
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @return Collection
     * @throws DatabaseAdapterException
     */
    public function find(array $filter = [], int $limit = 0, int $offset = 0): Collection
    {
        $this->ensureHasDb();
        return $this->db->find($this->getSchema(), static::class, $filter, $limit, $offset);
    }

    /**
     * Retrieves a collection of records from a database.
     * Joins with other tables or collections via 2-dimensional array $join
     * which is list of associated arrays structured as follows:
     * - schema => name of schema to join to
     * - type => join type
     * - local => name of local field of join
     * - remote => name of remote field of join
     * - operator => logical operator used to compare local and remote fields; default: equality operator
     * Projection lets to reduce returned columns to that list. Empty array means no limit.
     * Filtering is done by doing a simple comparison between corresponding
     * columns (keys of array $filter) and literal values (values of array $filter)
     * and joining those comparisons by a logic AND operator.
     * Throws an exception if a database adapter has not been set.
     *
     * @param array $join
     * @param string $projection
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @return Collection
     * @throws DatabaseAdapterException
     */
    public function findJoin(array $join, array $projection = [], array $filter = [], int $limit = 0, int $offset = 0): Collection
    {
        $this->ensureHasDb();
        return $this->db->findJoin($this->getSchema(), $join, $projection, static::class, $filter, $limit, $offset);
    }

    /**
     * Removes records from a database that match a specific filters.
     * Filtering is done by doing a simple comparison between corresponding
     * columns (keys of array $filter) and literal values (values of array $filter)
     * and joining those comparisons by a logic AND operator.
     * Throws an exception if a database adapter has not been set.
     *
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @throws DatabaseAdapterException
     */
    public function delete(array $filter, int $limit = 0, int $offset = 0): void
    {
        $this->ensureHasDb();
        $this->db->delete($this->getSchema(), $filter, $limit, $offset);
    }

    public function getFields(): array
    {
        return $this->data->getKeys();
    }

    public function hasValue($field): bool
    {
        return $this->getValueStrategy->hasValue($field);
    }

    public function getValue($field, $default = null)
    {
        return $this->getValueStrategy->getValue($field, $default);
    }

    public function setValue($field, $value): void
    {
        $this->setValueStrategy->setValue($field, $value);
    }

    public function removeValue($field): void
    {
        $this->setValueStrategy->removeValue($field);
    }

    public function patch(array $data): void
    {
        $this->data->replace(array_merge($this->getData(false), $data));
    }

    public function replace(array $data): void
    {
        $this->data->replace($data);
    }

    public abstract function validate(): void;

    /**
     * Saves a record to a database.
     * It inserts a new record if this object has no id
     * or updates existing one otherwise.
     * Throws an exception if a database adapter has not been set.
     *
     * @throws DatabaseAdapterException
     * @throws RecordException
     */
    public function save(): void
    {
        $this->isNew() ? $this->insert() : $this->update();
    }

    /**
     * Inserts a new record to a database
     * Throws an exception if a database adapter has not been set
     * or when a record is not new.
     *
     * @throws DatabaseAdapterException
     * @throws RecordException
     */
    public function insert(): void
    {
        if (!$this->isNew())
            throw new RecordException('Cannot insert a record when it has an id assigned. '
                . 'Create a clone by calling getNew() and then insert.');

        $this->validate();
        $this->onBeforeInsert();
        $this->setId($this->getDbAdapter()->insert($this->getSchema(), $this->getData(false))->getLastInsertedId());
        $this->onAfterInsert();
    }

    protected function onBeforeInsert(): void
    {
    }

    protected function onAfterInsert(): void
    {
    }

    /**
     * Updates content of a record into database.
     * Throws an exception if a database adapter has not been set
     * or when a record is new.
     *
     * @throws DatabaseAdapterException
     * @throws RecordException
     */
    public function update(): void
    {
        if ($this->isNew())
            throw new RecordException('Cannot update a record when it has no id assigned. '
                . 'Insert the record beforehand and then you can update.');

        $this->validate();
        $this->onBeforeUpdate();
        $this->getDbAdapter()->updateById($this->getSchema(), $this->getId(), $this->getData(false));
        $this->onAfterUpdate();
    }

    protected function onBeforeUpdate(): void
    {
    }

    protected function onAfterUpdate(): void
    {
    }

    /**
     * Removes a record from a database.
     * Throws an exception if a database adapter has not been set.
     *
     * @throws DatabaseAdapterException
     */
    public function remove(): void
    {
        if ($this->isNew()) return;

        $this->onBeforeRemove();
        $this->getDbAdapter()->deleteById($this->getSchema(), $this->getId());
        $this->onAfterRemove();
    }

    protected function onBeforeRemove()
    {
    }

    protected function onAfterRemove()
    {
    }

    public function notify(Observable $observable): void
    {
        $this->notifyChanged();
    }

    public function jsonSerialize()
    {
        return $this->getData(true);
    }
}