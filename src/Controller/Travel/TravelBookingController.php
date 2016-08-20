<?php
namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\Exception\ApiException;
use Api\Mapper\DB\ActionMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Model\Travel\Travel;
use Api\Wego\WegoFlights;
use Api\Wego\WegoHotels;

class TravelBookingController extends ApiController
{
    private $hotels = [];
    private $airports = [];

    /**
     * @var TravelMapper
     */
    private $travel_mapper;

    /**
     * @var ActionMapper
     */
    private $action_mapper;

    /**
     * @var WegoHotels
     */
    private $wego_hotel;

    /**
     * @var WegoFlights
     */
    private $wego_flight;

    /**
     * TravelController constructor.
     * @param TravelMapper   $travel_mapper
     * @param ActionMapper   $action_mapper
     */
    public function __construct(
        TravelMapper $travel_mapper,
        ActionMapper $action_mapper
    ) {
        $this->travel_mapper = $travel_mapper;
        $this->action_mapper = $action_mapper;
    }

    public function setTravelActions(int $id)
    {
        $travel = $this->travel_mapper->fetchById($id);
        if (!$travel) {
            throw new ApiException('Travel not found', ApiException::RESOURCE_NOT_FOUND);
        }
        $actions = $travel->getActions();
        foreach ($actions as $action) {
            if ($action->getType() === 'flight') {
                foreach ($action->getAirports() as $airport) {
                    $this->airports = [$airport->code];
                }
            }
            if ($action->getType() === 'lodging') {
                foreach ($action->getHotels() as $hotel) {
                    $this->hotels = [$hotel->wan_id];
                }
            }
        }
    }
}
