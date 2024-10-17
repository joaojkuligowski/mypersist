<?php

namespace Joaojkuligowski\Mypersist;

interface PersistenceInterface
{
  public function createTableIfNotExists(string $table, array $columns): void;
  public function insert(string $table, array $data): void;
  public function select(string $table, array $where = []): array;
  public function upsert(string $table, array $data, string $uniqueKey): void;
}
