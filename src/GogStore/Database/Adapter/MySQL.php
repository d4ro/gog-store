<?php

namespace GogStore\Database\Adapter;

use GogStore\Config;
use GogStore\Database\Record\AbstractRecord;
use GogStore\Database\Record\NullRecord;
use GogStore\Database\Record\Record;
use GogStore\Database\Record\RecordException;
use GogStore\Database\Record\SimpleRecord;
use GogStore\Log;
use GogStore\Pattern\Collection\Collection;
use GogStore\Pattern\Collection\Lst;
use PDO;
use PDOException;

class MySQL extends AbstractAdapter
{
    private Pdo $db;

    public function __construct(Config $config)
    {
        $port = $config->getValue('DB_PORT') ?? 3306;
        $dsn = "mysql:host={$config->getValue('DB_HOST')};port=$port;dbname={$config->getValue('DB_NAME')}";
        $username = $config->getValue('DB_USER');
        $password = $config->getValue('DB_PASS');
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8;',
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

        $this->db = new PDO($dsn, $username, $password, $options);
    }

    /**
     * Gets a record of a given id from a database.
     * A type of a record will be:
     * - NullRecord if a record does not exist in a database
     * - Given $type if specified; type must inherit from a Record class
     * - SimpleRecord otherwise
     * Throws an exception if a type is invalid or a database operation fails.
     *
     * @param string $schema
     * @param string $id
     * @param string|null $type
     * @return Record
     * @throws RecordException
     * @throws PDOException
     * @see AbstractRecord::getById()
     */
    function getById(string $schema, string $id, string $type = null): Record
    {
        $schema = $this->quote($schema);
        $query = "SELECT * FROM $schema WHERE `id` = :id";
        Log::getInstance()->info("Mysql::getById()", "About to make a query: ($query) with id=$id");
        $statement = $this->db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$data)
            return new NullRecord($schema);

        $record = new SimpleRecord($schema, $data);
        if (null !== $type) {
            $record = $record->getAsType($type);
        }

        return $record;
    }

    /**
     * Retrieves a collection of records from a database that match specified params.
     * Throws an exception if a type is invalid or a database operation fails.
     *
     * @param string $schema
     * @param string|null $type
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @return Collection
     * @throws RecordException
     * @throws PDOException
     * @see AbstractRecord::find()
     */
    function find(string $schema, string $type = null, array $filter = [], int $limit = 0, int $offset = 0): Collection
    {
        return $this->findJoin($schema, [], [], $type, $filter, $limit, $offset);
    }

    function findJoin(string $schema, array $join, array $projection = [], string $type = null, array $filter = [],
                      int $limit = 0, int $offset = 0): Collection
    {
        $joins = $this->prepareJoins($schema, $join);
        $columns = $this->prepareProjection($projection);
        $where = $this->prepareWhereClause($filter);
        $offsetLimit = $this->prepareOffsetLimit($offset, $limit);
        $query = "SELECT $columns FROM $schema $joins $where $offsetLimit";
        Log::getInstance()->info("Mysql::findJoin()", "About to make a query: ($query) with join="
            . json_encode($join) . " projection=" . json_encode($projection)
            . " filter=" . json_encode($filter) . " limit=$limit offset=$offset");
        $statement = $this->db->prepare($query);
        foreach ($filter as $field => $value) {
            $statement->bindValue(":$field", $value);
        }
        $statement->execute();

        $collection = new Lst();
        while (($data = $statement->fetch(PDO::FETCH_ASSOC))) {
            $record = new SimpleRecord($schema, $data);
            if (null !== $type) {
                $record = $record->getAsType($type);
            }
            $collection->add($record);
        }

        return $collection;
    }

    /**
     * Removes records from a database that match specified params.
     * Throws an exception if a type is invalid or a database operation fails.
     *
     * @param string $schema
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @throws PDOException
     * @see AbstractRecord::delete()
     */
    public function delete(string $schema, array $filter, int $limit = 0, int $offset = 0): void
    {
        $schema = $this->quote($schema);
        $where = $this->prepareWhereClause($filter);
        $offsetLimit = $this->prepareOffsetLimit($offset, $limit);
        $query = "DELETE FROM $schema $where $offsetLimit";
        Log::getInstance()->info("Mysql::delete()", "About to make a query: ($query) with filter="
            . json_encode($filter) . " limit=$limit offset=$offset");
        $statement = $this->db->prepare($query);
        foreach ($filter as $field => $value) {
            $statement->bindValue(":$field", $value);
        }
        $statement->execute();
    }

    /**
     * Return a last inserted id in a current connection.
     *
     * @return string
     */
    public function getLastInsertedId(): string
    {
        return $this->db->lastInsertId();
    }

    /**
     * Inserts data into a scheme.
     * Thrown an exception when a database operation fails.
     *
     * @param string $schema
     * @param array $data
     * @return $this
     * @throws PDOException
     * @see AbstractRecord::insert()
     */
    function insert(string $schema, array $data): self
    {
        $schema = $this->quote($schema);
        list($fields, $values) = $this->quoteDataForInsert($data);
        $query = "INSERT INTO $schema ($fields) VALUES ($values);";
        Log::getInstance()->info("Mysql::insert()", "About to make a query: ($query) with data=" . json_encode($data));
        $this->db->exec($query);

        return $this;
    }

    /**
     * Updates data into a scheme.
     * Thrown an exception when a database operation fails.
     *
     * @param string $schema
     * @param string $id
     * @param array $data
     * @return $this
     * @throws PDOException
     * @see AbstractRecord::update()
     */
    function updateById(string $schema, string $id, array $data): self
    {
        $assigment = $this->quoteDataForUpdate($data);
        $query = "UPDATE $schema SET $assigment WHERE `id` = :id";
        Log::getInstance()->info("Mysql::updateById()", "About to make a query: ($query) with id=$id data=" . json_encode($data));
        $statement = $this->db->prepare($query);
        array_walk($data, function ($value, $field, $statement) {
            $statement->bindValue(":$field", $value);
        }, $statement);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $this;
    }

    /**
     * Removes a record from a scheme.
     * Thrown an exception when a database operation fails.
     *
     * @param string $schema
     * @param string $id
     * @return $this
     * @throws PDOException
     * @see AbstractRecord::delete()
     */
    function deleteById(string $schema, string $id): self
    {
        $query = "DELETE FROM $schema WHERE `id` = :id";
        Log::getInstance()->info("Mysql::deleteById()", "About to make a query: ($query) with id=$id");
        $statement = $this->db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $this;
    }

    /**
     * Quotes names of tables or columns.
     * For the sake of simplicity this method IS NOT safe against SQL injection!
     *
     * @param string $value
     * @return string
     */
    public function quote(string $value): string
    {
        return implode('.', array_map(function ($part) {
            return "`$part`";
        }, explode('.', $value)));
    }

    /**
     * Returns separately quoted column names and values as an array of these two strings.
     *
     * @param array $data
     * @return string[]
     */
    public function quoteDataForInsert(array $data): array
    {
        if (empty($data)) {
            return ['', ''];
        }

        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $values = '"' . implode('", "', array_values($data)) . '"';
        return [$fields, $values];
    }

    /**
     * Returns quoted assignment list of column names and values in one string
     *
     * @param array $data
     * @return string
     */
    public function quoteDataForUpdate(array $data): string
    {
        $map = array_map(function ($field) {
            return $this->quote($field) . ' = :' . $field;
        }, array_keys($data));
        return implode(', ', $map);
    }

    private function prepareWhereClause(array $filter): string
    {
        if (empty($filter))
            return '';

        $map = array_map(function ($field) {
            return $this->quote($field) . ' = :' . $field;
        }, array_keys($filter));

        return "WHERE " . implode(' AND ', $map);
    }

    private function prepareOffsetLimit(int $offset, int $limit): string
    {
        $result = [];

        if ($limit)
            $result[] = "LIMIT $limit";

        if ($offset)
            $result[] = "OFFSET $offset";

        return implode(' ', $result);
    }

    private function prepareProjection(array $projection): string
    {
        if (empty($projection))
            return '*';

        return implode(', ', array_map(function ($part) {
            return $this->quote($part);
        }, $projection));
    }

    private function prepareJoins(string $schema, array $joins)
    {
        $map = array_map(function (array $join) use ($schema) {
            $type = $join['type'] ?? 'LEFT';
            $local = $this->quote($join['local']) ?? $this->throwEx('Cannot join without "local" part.');
            $remote = $this->quote($join['remote']) ?? $this->throwEx('Cannot join without "remote" part.');
            $remoteSchema = $this->quote($join['schema']) ?? $this->throwEx('Cannot join without "schema" part.');
            $operator = $join['operator'] ?? '=';
            return "$type JOIN $remoteSchema ON $schema.$local $operator $remoteSchema.$remote";
        }, $joins);

        return implode(' ', $map);
    }

    private function throwEx(string $message)
    {
        throw new RecordException($message);
    }
}