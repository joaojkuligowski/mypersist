<?php

namespace Joaojkuligowski\Mypersist;

interface PersistenceInterface
{
  public function createTableIfNotExists(string $table, array $columns): void;
  public function insert(string $table, array $data): void;
  public function select(string $table, array $where = []): array;
  public function upsert(string $table, array $data, string $uniqueKey): void;

  public function selectWithLimit(string $table, array $where = [], int $limit = 0, int $offset = 0): array;

  public function join(
    string $table1,
    string $table2,
    string $joinColumnTable1,
    string $joinColumnTable2
  ): array;

  public function delete(string $table, array $where = []): void;
}
