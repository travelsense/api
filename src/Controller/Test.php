<?php

namespace Controller;

class Test
{
    public function getTest($foo, $limit = 10, $offset = 0)
    {
        return [
            'foo' => $foo,
            'limit' => $limit,
            'offset' => $offset,
         ];
    }

}