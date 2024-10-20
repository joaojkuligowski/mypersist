# Base

[Full Documentation](DOCS.html)



## Methods

### __construct

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

Example usage:
```php
$instance->__construct($type, $location);
```

### insert

/**
   * Insert data into the specified table.
   *
   * This method delegates the insert operation to the underlying persistence
   * driver, which handles the specifics of how the data is stored.
   *
   * @param string $table The name of the table to insert data into.
   * @param array $data An associative array representing the data to be inserted
   *                    where the keys are column names and the values are the corresponding values.
   * @return void
   */

Example usage:
```php
$instance->insert($table, $data);
```

### select

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

Example usage:
```php
$instance->select($table, $where);
```

### upsert

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

Example usage:
```php
$instance->upsert($table, $data, $uniqueKey);
```

### selectWithLimit

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

Example usage:
```php
$instance->selectWithLimit($table, $where, $limit, $offset);
```

### join

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

Example usage:
```php
$instance->join($table1, $table2, $joinColumnTable1, $joinColumnTable2);
```

### delete

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

Example usage:
```php
$instance->delete($table, $where);
```

