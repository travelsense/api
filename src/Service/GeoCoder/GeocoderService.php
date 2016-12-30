<?php
namespace Api\Service\GeoCoder;

use Api\Mapper\DB\TravelMapper;
use DateTime;

class GeocoderService
{
    /**
     * @var GoogleMapsClient
     */
    private $geocoder;

    /**
     * @var DateWriteReader
     */
    private $date_write_reader;

    /**
     * @var TravelMapper
     */
    private $travel_mapper;

    /**
     * Geocoder constructor.
     * @param GoogleMapsClient $geocoder
     * @param DateWriteReader $date_write_reader
     * @param TravelMapper $travel_mapper
     */
    public function __construct(
        GoogleMapsClient $geocoder,
        DateWriteReader $date_write_reader,
        TravelMapper $travel_mapper
    ) {
        $this->geocoder = $geocoder;
        $this->date_write_reader = $date_write_reader;
        $this->travel_mapper = $travel_mapper;
    }

    public function setCitiesStatesCountriesToTravel()
    {
        $last_updated = $this->date_write_reader->readLastUpdatedTime();
        $this->date_write_reader->writeLastUpdatedTime(date('Y-m-d H:i:s'));
        if ($last_updated != null) {
            foreach ($this->travel_mapper->fetchUpdatedAfter($last_updated) as $travel) {
                $geo_names = [];
                //            foreach ($travel->getAll Coordinates as $point) {
                //                $geo_names[] = $this->geocoder->getName($point);
                //            }
                //            $travel->setGeotags($geo_names);
                $this->travel_mapper->update($travel);
            }
        }
    }
}
