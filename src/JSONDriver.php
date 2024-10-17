<?php

namespace Joaojkuligowski\Mypersist;

class JSONDriver implements PersistenceInterface
{
  private $filePath;

  public function __construct($filePath = 'data.json')
  {
    $this->filePath = $filePath;
    if (!file_exists($this->filePath)) {
      file_put_contents($this->filePath, json_encode([]));
    }
  }

  private function readData(): mixed
  {
    $data = file_get_contents($this->filePath);
    return json_decode($data, true);
  }

  private function writeData($data): void
  {
    file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
  }

  public function createTableIfNotExists(string $table, array $columns): void
  {
    $data = $this->readData();
    if (!isset($data[$table])) {
      $data[$table] = [];
      $this->writeData($data);
    }
  }

  public function insert($table, $data): void
  {
    $currentData = $this->readData();
    if (!isset($currentData[$table])) {
      $currentData[$table] = [];
    }

    $currentData[$table][] = $data;
    $this->writeData($currentData);
  }

  public function select($table, $where = []): array
  {
    $currentData = $this->readData();
    if (!isset($currentData[$table])) {
      return [];
    }

    $results = $currentData[$table];
    if (!empty($where)) {
      foreach ($where as $key => $value) {
        $results = array_filter($results, fn($item) => isset($item[$key]) && $item[$key] === $value);
      }
    }

    return array_values($results); // Reindex the array
  }
  public function upsert($table, $data, $uniqueKey): void
  {
    $currentData = $this->readData();

    if (!isset($currentData[$table])) {
      $currentData[$table] = [];
    }

    $existingIndex = null;
    foreach ($currentData[$table] as $index => $item) {
      if (isset($item[$uniqueKey]) && $item[$uniqueKey] === $data[$uniqueKey]) {
        $existingIndex = $index;
        break;
      }
    }

    if ($existingIndex !== null) {
      // Update existing record
      $currentData[$table][$existingIndex] = array_merge($currentData[$table][$existingIndex], $data);
    } else {
      // Insert new record
      $currentData[$table][] = $data;
    }

    $this->writeData($currentData);
  }
}
