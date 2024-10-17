<?php

namespace Joaojkuligowski\Mypersist;

use PDO;

class PDODriver implements PersistenceInterface
{
  private $db;

  public function __construct($filePath = 'sqlite:api.db')
  {
    $this->db = new PDO($filePath);
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function createTableIfNotExists(string $table, array $columns): void
  {
    $columns_sql = "id INTEGER PRIMARY KEY AUTOINCREMENT, ";
    $columns_sql .= implode(', ', array_map(fn($col, $type) => "$col $type", array_keys($columns), $columns));
    $columns_sql .= ", created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP";

    $sql = "CREATE TABLE IF NOT EXISTS $table ($columns_sql)";
    $this->db->exec($sql);
  }

  public function insert(string $table, array $data): void
  {
    $this->createTableIfNotExists($table, array_map(fn($value) => 'TEXT', $data));
    $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
    $stmt->execute(array_values($data));
  }

  public function select(string $table, array $where = []): array
  {
    $where_clause = '';
    if (!empty($where)) {
      $conditions = array_map(fn($col) => "$col = ?", array_keys($where));
      $where_clause = 'WHERE ' . implode(' AND ', $conditions);
    }

    $stmt = $this->db->prepare("SELECT * FROM $table $where_clause");
    $stmt->execute(array_values($where));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function upsert(string $table, array $data, string $uniqueKey): void
  {
    $this->createTableIfNotExists($table, array_map(fn($value) => 'TEXT', $data));
    $this->db->beginTransaction();
    try {
      $existing = $this->select($table, [$uniqueKey => $data[$uniqueKey]]);
      if ($existing) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $updateColumns = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $updateColumns WHERE $uniqueKey = ?");
        $stmt->execute(array_merge(array_values($data), [$data[$uniqueKey]]));
      } else {
        $this->insert($table, $data);
      }
      $this->db->commit();
    } catch (\Exception $e) {
      $this->db->rollBack();
      throw $e;
    }
  }
}
