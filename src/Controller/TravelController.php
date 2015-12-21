<?php
namespace Controller;

use Exception\ApiException;
use Mapper\DB\TravelMapper as DBTravelMapper;
use Mapper\JSON\TravelMapper as JSONTravelMapper;

class TravelController
{
    /**
     * @var DBTravelMapper
     */
    private $dbTravelMapper;

    /**
     * @var JSONTravelMapper
     */
    private $jsonTravelMapper;

    /**
     * TravelController constructor.
     * @param DBTravelMapper $dbTravelMapper
     * @param JSONTravelMapper $jsonTravelMapper
     */
    public function __construct(DBTravelMapper $dbTravelMapper, JSONTravelMapper $jsonTravelMapper)
    {
        $this->dbTravelMapper = $dbTravelMapper;
        $this->jsonTravelMapper = $jsonTravelMapper;
    }

    public function getTravel($id)
    {
        $travel = $this->dbTravelMapper->fetchById($id);
        if (null === $travel) {
            throw ApiException::create(ApiException::RESOURCE_NOT_FOUND);
        }
        return $this->jsonTravelMapper->toArray($travel);
    }
}