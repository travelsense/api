<?php
namespace Api\Persistence;

interface Storable
{
    public function storeIn(Storage $storage);
}
