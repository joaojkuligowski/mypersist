<?php

use Joaojkuligowski\Mypersist\Base;
use Joaojkuligowski\Mypersist\PDODriver;
use Joaojkuligowski\Mypersist\JSONDriver;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
  protected function setUp(): void
  {
    // Limpar o banco de dados SQLite antes de cada teste
    if (file_exists('api.test.db')) {
      unlink('api.test.db');
    }

    if (file_exists('data.test.json')) {
      unlink('data.test.json');
    }
  }

  public function testInsertAndSelectWithPDO()
  {
    $base = new Base(new PDODriver('sqlite:api.test.db'));

    $data = ['name' => 'John Doe', 'email' => 'john@example.com'];
    $base->insert('users', $data);

    $result = $base->select('users', ['email' => 'john@example.com']);
    $this->assertCount(1, $result);
    $this->assertEquals('John Doe', $result[0]['name']);
  }

  public function testUpsertWithPDO()
  {
    $base = new Base(new PDODriver('sqlite:api.test.db'));

    $data = ['email' => 'jane@example.com', 'name' => 'Jane Doe'];
    $base->upsert('users', $data, 'email');

    $result = $base->select('users', ['email' => 'jane@example.com']);
    $this->assertCount(1, $result);
    $this->assertEquals('Jane Doe', $result[0]['name']);

    // Update the record
    $data['name'] = 'Jane Smith';
    $base->upsert('users', $data, 'email');

    $updatedResult = $base->select('users', ['email' => 'jane@example.com']);
    $this->assertEquals('Jane Smith', $updatedResult[0]['name']);
  }

  public function testInsertAndSelectWithJSON()
  {
    $base = new Base(new JSONDriver('data.test.json'));
    $data = ['name' => 'Alice', 'email' => 'alice@example.com'];
    $base->insert('users', $data);

    $result = $base->select('users', ['email' => 'alice@example.com']);
    $this->assertCount(1, $result);
    $this->assertEquals('Alice', $result[0]['name']);
  }

  public function testUpsertWithJSON()
  {
    $base = new Base(new JSONDriver('data.test.json'));

    $data = ['email' => 'bob@example.com', 'name' => 'Bob Brown'];
    $base->upsert('users', $data, 'email');
    $result = $base->select('users', ['email' => 'bob@example.com']);
    $this->assertCount(1, $result);
    $this->assertEquals('Bob Brown', $result[0]['name']);

    // Update the record
    $data['name'] = 'Bob Black';
    $base->upsert('users', $data, 'email');

    $updatedResult = $base->select('users', ['email' => 'bob@example.com']);
    $this->assertEquals('Bob Black', $updatedResult[0]['name']);
  }

  protected function tearDown(): void
  {
    // Limpar o banco de dados SQLite depois de cada teste
    if (file_exists('api.test.db')) {
      unlink('api.test.db');
    }

    if (file_exists('data.test.json')) {
      unlink('data.test.json');
    }
  }
}
