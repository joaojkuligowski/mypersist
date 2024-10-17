<?php

namespace Joaojkuligowski\Mypersist;

class JSONDriver implements PersistenceInterface
{
  private string $baseDir;

  public function __construct(string $baseDir = 'data')
  {
    $this->baseDir = rtrim($baseDir, '/') . '/';
    if (!file_exists($this->baseDir)) {
      mkdir($this->baseDir, 0755, true);
    }
  }

  private function getFilePath(string $table): string
  {
    return $this->baseDir . $table . '.json';
  }

  private function readData(string $table): mixed
  {
    $filePath = $this->getFilePath($table);
    if (!file_exists($filePath)) {
      return [];
    }

    $data = file_get_contents($filePath);
    return json_decode($data, true);
  }

  private function writeData(string $table, $data): void
  {
    $filePath = $this->getFilePath($table);
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
  }

  public function selectWithLimit(string $table, array $where = [], int $limit = 0, int $offset = 0): array
  {
    $data = $this->select($table, $where)[0];
    return array_slice($data, $offset, $limit);
  }

  public function join(string $table1, string $table2, string $joinColumnTable1, string $joinColumnTable2): array
  {
    $table1Records = $this->readData($table1);
    $table2Records = $this->readData($table2);

    $result = [];

    foreach ($table1Records as $record1) {
      foreach ($table2Records as $record2) {
        if (
          isset($record1[$joinColumnTable1]) && isset($record2[$joinColumnTable2]) &&
          $record1[$joinColumnTable1] === $record2[$joinColumnTable2]
        ) {
          $result[] = array_merge($record1, $record2);
        }
      }
    }

    return $result;
  }

  public function createTableIfNotExists(string $table, array $columns): void
  {
    $data = $this->readData($table);
    if (empty($data)) {
      $this->writeData($table, []);
    }
  }

  public function insert(string $table, array $data): void
  {
    $currentData = $this->readData($table);
    $currentData[] = $data;
    $this->writeData($table, $currentData);
  }

  public function select(string $table, array $where = []): array
  {
    $currentData = $this->readData($table);
    if (empty($currentData)) {
      return [];
    }

    $results = $currentData;
    if (!empty($where)) {
      foreach ($where as $key => $value) {
        $results = array_filter($results, fn($item) => isset($item[$key]) && $item[$key] === $value);
      }
    }

    return array_values($results);
  }

  public function delete(string $table, array $conditions = []): void
  {
    // Carrega os dados atuais da tabela
    $currentData = $this->select($table);

    // Filtra os dados para excluir registros que correspondem às condições
    $filteredData = array_filter($currentData, function ($record) use ($conditions) {
      foreach ($conditions as $key => $value) {
        if (!isset($record[$key]) || $record[$key] !== $value) {
          return true; // Mantém o registro
        }
      }
      return false; // Exclui o registro
    });

    // Reindexa o array e grava os dados atualizados
    $filteredData = array_values($filteredData);
    $this->writeData($table, $filteredData);
  }

  public function upsert(string $table, array $data, string $uniqueKey): void
  {
    $currentData = $this->readData($table);
    $existingIndex = null;

    foreach ($currentData as $index => $item) {
      if (isset($item[$uniqueKey]) && $item[$uniqueKey] === $data[$uniqueKey]) {
        $existingIndex = $index;
        break;
      }
    }

    if ($existingIndex !== null) {
      $currentData[$existingIndex] = array_merge($currentData[$existingIndex], $data);
    } else {
      $currentData[] = $data;
    }

    $this->writeData($table, $currentData);
  }
}
