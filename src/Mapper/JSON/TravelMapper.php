<?php
namespace Mapper\JSON;

use Model\Travel;

class TravelMapper
{
    public function toArray(Travel $travel)
    {
        return [
            'title' => $travel->getTitle(),
            'description' => $travel->getDescription(),
        ];
    }
}