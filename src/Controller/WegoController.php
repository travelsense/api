<?php
namespace Api\Controller;

use Api\Wego\WegoClient;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

class WegoController
{
    /**
     * @var WegoClient
     */
    private $wego;

    /**
     * WegoController constructor.
     *
     * @param WegoClient $wego
     */
    public function __construct(WegoClient $wego)
    {
        $this->wego = $wego;
    }

    /**
     * Start hotels search
     *
     * @param  string   $location Location ID
     * @param  DateTime $in
     * @param  DateTime $out
     * @param  int      $rooms
     * @return JsonResponse
     */
    public function startHotelSearch($location, DateTime $in, DateTime $out, $rooms)
    {
        return new JsonResponse($this->wego->startHotelSearch($location, $in, $out, $rooms));
    }

    /**
     * Hotel search results
     *
     * @param  $id
     * @param  int $page
     * @return array
     */
    public function getHotelSearchResults($id, $page)
    {
        return $this->wego->getHotelSearchResults($id, false, 'USD', 'popularity', 'desc', 'XX', $page, 10);
    }

    /**
     * Start flight search
     *
     * @param string    $departureCode
     * @param bool      $departureCity
     * @param string    $arrivalCode
     * @param bool      $arrivalCity
     * @param int       $adultsCount
     * @param int       $childrenCount
     * @param int       $infantsCount
     * @param string    $cabin
     * @param DateTime  $outboundDate
     * @param DateTime  $inboundDate
     * @param string    $userCountryCode
     * @param string    $countrySiteCode
     *
     * @return JsonResponse
     */
    public function startFlightSearch(
        $departureCode,
        $departureCity,
        $arrivalCode,
        $arrivalCity,
        $adultsCount,
        $childrenCount,
        $infantsCount,
        $cabin,
        DateTime $outboundDate,
        DateTime $inboundDate = NULL,
        $userCountryCode = 'US',
        $countrySiteCode = 'US'
    ) {
        return new JsonResponse($this->wego->startFlightSearch(
            $departureCode,
            $departureCity,
            $arrivalCode,
            $arrivalCity,
            $adultsCount,
            $childrenCount,
            $infantsCount,
            $cabin,
            $outboundDate,
            $inboundDate,
            $userCountryCode,
            $countrySiteCode
        ));
    }

    /**
     * Make filters block for flight search request - price
     *
     * @param int $priceFrom
     * @param int $priceTo
     *
     * @return void
     */
    public function setFlightFilterPrice($priceFrom, $priceTo)
    {
        $this->wego->setFlightFilterPrice($priceFrom, $priceTo);
    }

    /**
     * Make filters block for flight search request - stops
     *
     * @param bool $none
     * @param bool $one
     * @param bool $twoPlus
     *
     * @return void
     */
    public function setFlightFilterStops($none, $one, $twoPlus)
    {
        $this->wego->setFlightFilterStops($none, $one, $twoPlus);
    }

    /**
     * Make filters block for flight search request - offers
     *
     * @param array $airlines
     * @param array $providers
     * @param array $designators
     * @param array $departureAirports
     * @param array $arrivalAirports
     * @param array $stopoverAirports
     *
     * @return void
     */
    public function setFlightFilterOffers(
        $airlines = array(),
        $providers = array(),
        $designators = array(),
        $departureAirports = array(),
        $arrivalAirports = array(),
        $stopoverAirports = array()
    ) {
        $this->wego->setFlightFilterOffers($airlines, $providers, $designators, $departureAirports, $arrivalAirports, $stopoverAirports);
    }

    /**
     * Make filters block for flight search request - durations
     *
     * @param int $min
     * @param int $max
     * @param int $stopoverMin
     * @param int $stopoverMax
     *
     * @return void
     */
    public function setFlightFilterDurations($min = 0, $max = 0, $stopoverMin = 0, $stopoverMax = 0) {
        $this->wego->setFlightFilterDurations($min, $max, $stopoverMin, $stopoverMax);
    }

    /**
     * Make filters block for flight search request - times
     *
     * @param int $outboundMin
     * @param int $outboundMax
     * @param int $inboundMin
     * @param int $inboundMax
     *
     * @return void
     */
    public function setFlightFilterTimes($outboundMin = 0, $outboundMax = 0, $inboundMin = 0, $inboundMax = 0)
    {
        $this->wego->setFlightFilterTimes($outboundMin, $outboundMax, $inboundMin, $inboundMax);
    }

    /**
     * Get results of a flight search
     *
     * @param string $searchId    Search id you get when the search is created
     * @param string $tripId      Trip id you get when the search is created
     * @param string $sort        price|duration|outbound_departure_time|inbound_departure_time
     * @param string $order       asc|desc
     * @param string $currency    Currency to display prices in - use ISO 4217 3-letter currency codes. Defaults to USD
     * @param int    $page        Page of results to return
     * @param int    $perPage     Number of results to return per page
     *
     * @return object
     */
    public function getFlightSearchResults(
        $searchId,
        $tripId,
        $sort = 'price',
        $order = 'asc',
        $currency = 'USD',
        $page = 1,
        $perPage = 20
    ) {
        return $this->wego->getFlightSearchResults($searchId, $tripId, $sort, $order, $currency, $page, $perPage);
    }

    /**
     * Get currencies available for flight search
     *
     * @return array
     */
    public function getFlightCurrencies()
    {
        return $this->wego->getFlightCurrencies();
    }
}
