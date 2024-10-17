<?php

namespace Joaojkuligowski\Mypersist;

class Base
{
  private $persistence;

  public function __construct(PersistenceInterface $persistence)
  {
    $this->persistence = $persistence;
  }

  public function insert(string $table, array $data): void
  {
    $this->persistence->insert($table, $data);
  }

  public function select(string $table, array $where = []): array
  {
    return $this->persistence->select($table, $where);
  }

  public function upsert(string $table, array $data, string $uniqueKey): void
  {
    $this->persistence->upsert($table, $data, $uniqueKey);
  }
}
