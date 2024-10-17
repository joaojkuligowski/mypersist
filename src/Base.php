<?php

namespace Joaojkuligowski\Mypersist;

class Base
{
  private $type;
  private $location;
  private $persistence;

  /**
   * Construct a new base object for a persistence driver.
   *
   * The type parameter should be either 'JSON' or 'PDO'. If not provided, it
   * defaults to 'JSON'. The location parameter should be the path to the
   * persistence location. If not provided, it defaults to 'database'.
   *
   * @param string $type The type of persistence driver to use.
   * @param string $location The path to the persistence location.
   */
  public function __construct($type = null, $location = null)
  {
    $this->type = $type ? $type : 'JSON';
    $this->location = $location ? $location : 'database';
    if ($this->type === 'JSON') {
      $this->persistence = new JSONDriver($this->location);
    } else {
      $this->persistence = new PDODriver($this->location);
    }
  }

  /**
   * Insert data into the specified table.
   *
   * This method delegates the insert operation to the underlying persistence
   * driver, which handles the specifics of how the data is stored.
   *
   * @param string $table The name of the table to insert data into.
   * @param array $data An associative array representing the data to be inserted
   *                    where the keys are column names and the values are the corresponding values.
   */
  public function insert(string $table, array $data): void
  {
    $this->persistence->insert($table, $data);
  }

  /**
   * Select data from the specified table.
   *
   * This method delegates the select operation to the underlying persistence
   * driver, which handles the specifics of how the data is retrieved.
   *
   * @param string $table The name of the table to select data from.
   * @param array $where An associative array representing the conditions for
   *                     which records should be returned. The keys are column
   *                     names and the values are the corresponding values.
   * @return array An array of associative arrays, where each associative array
   *              represents a record in the table and the keys are column names
   *              and the values are the corresponding values.
   */
  public function select(string $table, array $where = []): array
  {
    return $this->persistence->select($table, $where);
  }

  /**
   * Upsert data into the specified table.
   *
   * This method delegates the upsert operation to the underlying persistence
   * driver, which handles the specifics of how the data is stored.
   *
   * @param string $table The name of the table to upsert data into.
   * @param array $data An associative array representing the data to be upserted
   *                    where the keys are column names and the values are the
   *                    corresponding values.
   * @param string $uniqueKey The column name to use as the unique key for the
   *                          upsert operation.
   */
  public function upsert(string $table, array $data, string $uniqueKey): void
  {
    $this->persistence->upsert($table, $data, $uniqueKey);
  }

  /**
   * Select data from the specified table with a limit and offset.
   *
   * This method delegates the select with limit and offset operation to the
   * underlying persistence driver, which handles the specifics of how the data
   * is retrieved.
   *
   * @param string $table The name of the table to select data from.
   * @param array $where An associative array representing the conditions for
   *                     which records should be returned. The keys are column
   *                     names and the values are the corresponding values.
   * @param int $limit The maximum number of records to return.
   * @param int $offset The number of records to skip before returning records.
   * @return array An array of associative arrays, where each associative array
   *              represents a record in the table and the keys are column names
   *              and the values are the corresponding values.
   */
  public function selectWithLimit(string $table, array $where = [], int $limit = 0, int $offset = 0): array
  {
    return $this->persistence->selectWithLimit($table, $where, $limit, $offset);
  }

  /**
   * Join two tables based on a common column.
   *
   * This method delegates the join operation to the underlying persistence
   * driver, which handles the specifics of how the data is joined.
   *
   * @param string $table1 The name of the first table to join.
   * @param string $table2 The name of the second table to join.
   * @param string $joinColumnTable1 The name of the column in the first table
   *                                 to use as the join key.
   * @param string $joinColumnTable2 The name of the column in the second table
   *                                 to use as the join key.
   * @return array An array of associative arrays, where each associative array
   *              represents a record in the joined table and the keys are
   *              column names and the values are the corresponding values.
   */
  public function join(
    string $table1,
    string $table2,
    string $joinColumnTable1,
    string $joinColumnTable2
  ): array {
    return $this->persistence->join($table1, $table2, $joinColumnTable1, $joinColumnTable2);
  }

  /**
   * Delete records from the specified table.
   *
   * This method delegates the delete operation to the underlying persistence
   * driver, which handles the specifics of how the data is deleted.
   *
   * @param string $table The name of the table to delete records from.
   * @param array $where An associative array representing the conditions for
   *                     which records should be deleted. The keys are column
   *                     names and the values are the corresponding values.
   */
  public function delete(string $table, array $where = []): void
  {
    $this->persistence->delete($table, $where);
  }
}
