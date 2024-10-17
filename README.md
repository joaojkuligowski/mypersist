# MyPersist

MyPersist is a lightweight micro ORM (Object-Relational Mapping) for PHP that supports both PDO (for database interaction) and JSON files (for lightweight data storage). This library allows you to easily create, read, update, and delete data while maintaining a clean and simple codebase.

## Features

- Support for SQLite databases using PDO.
- Option to use JSON files for data storage.
- Automatic table creation and column management.
- Upsert functionality to handle both inserts and updates.
- Simple API for interacting with your data.

## Installation

You can install MyPersist via Composer. First, add the repository to your `composer.json`:

```json
{
  "require": {
    "joaojkuligowski/mypersist": "^1.0"
  }
}
```

Then run:

```bash
composer update
```

## Usage

### Setup

First, create a new instance of the `Base` class. You can choose the storage driver (PDO or JSON).

#### Using PDO (SQLite)

```php
use Joaojkuligowski\Mypersist\Base;

$base = new Base('sqlite', 'api.db'); // Specify 'sqlite' and the database name
```

#### Using JSON

```php
use Joaojkuligowski\Mypersist\Base;

$base = new Base('json', 'data.json'); // Specify 'json' and the file name
```

### Basic Operations

#### Inserting Data

To insert data into a table, use the `insert` method:

```php
$data = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
];

$base->insert('users', $data);
```

#### Selecting Data

To retrieve data from a table, use the `select` method:

```php
$users = $base->select('users', ['email' => 'john.doe@example.com']);
print_r($users);
```

#### Upserting Data

To insert or update a record, use the `upsert` method. This is particularly useful for maintaining unique records:

```php
$data = [
    'email' => 'john.doe@example.com',
    'name' => 'Johnathan Doe', // Update name if email already exists
];

$base->upsert('users', $data, 'email'); // 'email' is the unique key
```

### Table and Column Management

The library automatically creates tables and columns as needed. If you insert data into a table that doesn’t exist, it will be created with the appropriate columns.

### Error Handling

All methods include basic error handling. If an error occurs during database operations, it will output an error message. You can further enhance error handling based on your requirements.

## Running Tests

You can run the tests using PHPUnit. First, ensure you have PHPUnit installed, then execute:

```bash
./vendor/bin/phpunit tests
```

## Contributing

If you'd like to contribute to MyPersist, feel free to fork the repository and submit a pull request. Ensure you follow the coding standards and include tests for any new features or bug fixes.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
