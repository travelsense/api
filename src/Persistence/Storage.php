<?php
namespace Api\Persistence;

interface Storage
{
    public function save(array $dto): int;
}
