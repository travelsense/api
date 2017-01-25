<?php
namespace Api\Persistence;

interface Storage
{
    public function insert(array $dto): int;
    public function update(int $id, array $dto);
}
