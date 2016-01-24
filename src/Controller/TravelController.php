<?php
namespace Controller;

use Exception\ApiException;
use Mapper\DB\TravelMapper;

class TravelController
{
    /**
     * @var TravelMapper
     */
    private $travelMapper;

    /**
     * TravelController constructor.
     * @param TravelMapper $travelMapper
     */
    public function __construct(TravelMapper $travelMapper)
    {
        $this->travelMapper = $travelMapper;
    }

    public function getTravel($id)
    {
        $travel = $this->travelMapper->fetchById($id);
        if (null === $travel) {
            throw ApiException::create(ApiException::RESOURCE_NOT_FOUND);
        }
        return [
            'title' => $travel->getTitle(),
            'description' => $travel->getDescription(),
        ];
    }
}