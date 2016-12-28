<?php
namespace Api\Service;

use Api\Mapper\DB\TravelMapper;
use GoogleMapsGeocoder;

class Geocoder
{
    /**
     * @var GoogleMapsGeocoder
     */
    private $geocoder;

    /**
     * @var TravelMapper
     */
    private $travel_mapper;

    /**
     * Geocoder constructor.
     * @param GoogleMapsGeocoder $geocoder
     * @param TravelMapper $travel_mapper
     */
    public function __construct(GoogleMapsGeocoder $geocoder, TravelMapper $travel_mapper)
    {
        $this->geocoder = $geocoder;
        $this->travel_mapper = $travel_mapper;
    }
}
