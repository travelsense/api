<?php
namespace Api\Service;

use Api\Mapper\DB\TravelMapper;
use DateTime;
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

    private $file_name = '/tmp/updated/last_updated.txt';

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

    public function setCitiesStatesCountriesToTravel()
    {
    }

    private function getTravelsUpdatedAfter(DateTime $date_time)
    {
        $travels = $this->travel_mapper->fetchUpdatedAfter($date_time);
        return $travels;
    }

    private function filePutDate($date_time)
    {
        $file = file($this->file_name);
        file_put_contents($file, $date_time);
    }
}
