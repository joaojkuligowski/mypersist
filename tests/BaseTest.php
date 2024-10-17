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

    // UNLINK NO DIRETORIO
    if (file_exists('test')) {
      unlink('test');
    }
  }

  public function testInsertAndSelectWithPDO()
  {
    $base = new Base('PDO', 'sqlite:api.test.db');

    $data = ['name' => 'John Doe', 'code' => '123', 'email' => 'john@example.com'];
    $base->insert('users', $data);

    $result = $base->select('users', ['email' => 'john@example.com']);
    $this->assertCount(1, $result);
    $this->assertEquals('John Doe', $result[0]['name']);
  }

  public function testInsertAndJoinWithPDO()
  {
    $base = new Base('PDO', 'sqlite:api.test.db');

    $data = ['userCode' => '123', 'pricing' => '1000', 'dueDate' => '2022-01-01'];
    $base->insert('invoices', $data);
    $data2 = ['name' => 'John Doe', 'code' => '123', 'email' => 'john@example.com'];
    $base->upsert('users', $data2, 'code');

    $result = $base->join('invoices', 'users', 'userCode', 'code');
    $this->assertCount(1, $result);
    $this->assertEquals('John Doe', $result[0]['name']);
    $this->assertEquals('123', $result[0]['userCode']);
  }

  public function testUpsertWithPDO()
  {
    $base = new Base('PDO', 'sqlite:api.test.db');

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

  public function testInsertAndSelectWithJSON(): void
  {
    $base = new Base('JSON', 'data');
    $data = ['name' => 'Alice', 'email' => 'alice@example.com'];
    $base->insert('users', $data);

    $result = $base->select('users', ['email' => 'alice@example.com']);
    $this->assertCount(1, $result);
    $this->assertEquals('Alice', $result[0]['name']);
  }

  public function testUpsertWithJSON(): void
  {
    $base = new Base('JSON', 'data');

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

  public function testInsertAndJoinWithJson(): void
  {
    $base = new Base('JSON', 'data');

    $data2 = ['name' => 'John Doe', 'code' => '123', 'email' => 'john@example.com'];
    $base->upsert('users', $data2, 'code');

    $data = ['userCode' => '123', 'pricing' => '1000', 'dueDate' => '2022-01-01'];
    $base->insert('invoices', $data);

    $result = $base->join('invoices', 'users', 'userCode', 'code');
    $this->assertCount(1, $result);
    $this->assertEquals('John Doe', $result[0]['name']);
    $this->assertEquals('123', $result[0]['userCode']);
  }

  public function testDataIsEqualInAllDrivers(): void
  {
    $baseJson = new Base('JSON', 'data');
    $basePdo = new Base('PDO', 'sqlite:api.test.db');

    $data = ['name' => 'John Doe', 'code' => '123', 'email' => 'john@example.com'];
    $baseJson->insert('users', $data);
    $basePdo->insert('users', $data);

    $resultJson = $baseJson->select('users', ['code' => '123'])[0];
    $resultPdo = $basePdo->select('users', ['code' => '123'])[0];
    unset($resultPdo['id']);
    unset($resultPdo['created_at']);
    unset($resultPdo['updated_at']);

    var_dump([
      'json' => $resultJson,
      'pdo' => $resultPdo
    ]);

    $this->assertEquals($resultJson, $resultPdo);
  }

  public function testDeleteDataJson(): void
  {
    $base = new Base('JSON', 'data');
    $codeGen = rand(0, 10000);
    $data = ['name' => 'John Doe', 'code' => $codeGen, 'email' => 'john@example.com'];
    $base->insert('users', $data);
    $base->delete('users', ['code' => $codeGen]);

    $result = $base->select('users', $data);
    var_dump([
      'testDeleteDataJson' => $result
    ]);
    $this->assertCount(0, $result);
  }

  public function testDeleteDataPDO(): void
  {
    $base = new Base('PDO', 'sqlite:api.test.db');
    $data = ['name' => 'John Doe', 'code' => '123', 'email' => 'john@example.com'];
    $base->insert('users', $data);
    $base->delete('users', ['code' => '123']);

    $result = $base->select('users', ['code' => '123']);
    $this->assertCount(0, $result);
  }

  public function testSelectWithLimitJson(): void
  {
    $data = array_map(function ($i) {
      return [
        'name' => "Person $i",
        'code' => str_pad($i, 3, '0', STR_PAD_LEFT),
        'email' => "person$i@example.com",
      ];
    }, range(1, 10));
    $base = new Base('JSON', 'data');
    $base->upsert('users', $data, 'code');

    $result = $base->selectWithLimit('users', [], 5, 1);
    var_dump([
      'count' => count($result)
    ]);
    $this->assertEquals(5, count($result));
    $result2 = $base->selectWithLimit('users', [], 10, 5);
    $this->assertCount(5, $result2);
  }

  public function testSelectWithLimitPDO(): void
  {
    $data = [
      [
        'name' => 'Person 1',
        'code' => '000',
        'email' => 'person1@example.com',
      ],
      [
        'name' => 'Person 2',
        'code' => '001',
        'email' => 'person2@example.com',
      ],
      [
        'name' => 'Person 3',
        'code' => '002',
        'email' => 'person3@example.com',
      ],
      [
        'name' => 'Person 4',
        'code' => '003',
        'email' => 'person4@example.com',
      ],
      [
        'name' => 'Person 5',
        'code' => '004',
        'email' => 'person5@example.com',
      ]
    ];
    $base = new Base('PDO', 'sqlite:api.test.db');
    foreach ($data as $item) {
      $base->upsert('users', $item, 'code');
    }

    $result = $base->selectWithLimit('users', [], 3, 1);
    var_dump([
      'count' => count($result)
    ]);
    $this->assertCount(3, $result);

    $result2 = $base->selectWithLimit('users', [], 1, 3);

    $this->assertCount(1, $result2);
  }

  protected function tearDown(): void
  {
    // Limpar o banco de dados SQLite depois de cada teste
    if (file_exists('api.test.db')) {
      unlink('api.test.db');
    }

    if (is_dir('data')) {
      array_map('unlink', glob('data/*'));
      rmdir('data');
    }
  }
}
