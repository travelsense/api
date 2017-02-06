<?php
namespace Api\Persistence;

interface Storable
{
    public function saveTo(Storage $storage);
}
