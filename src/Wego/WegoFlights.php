<?php
namespace Api\Wego;

use DateTime;
use OutOfRangeException;

/**
 * WAN.travel API client
 *
 * @see http://support.wan.travel/hc/en-us
 */
class WegoFlights
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var WegoHttpClient
     */
    private $http;

    /**
     * @var array
     */
    private $flightFilters = [];

    /**
     * Client constructor.
     *
     * @param WegoHttpClient $http
     */
    public function __construct(WegoHttpClient $http)
    {
        $this->http = $http;
    }


    /**
     * Start a new flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#search-start-an-new-flight-search
     *
     * @param string   $departureCode         Departure airport or city code, IATA 3-letter code
     * @param bool     $departureCity         Set true if departure_code is a city code
     * @param string   $arrivalCode           Arrival airport or city code, IATA 3-letter code
     * @param bool     $arrivalCity           Set true if arrival_code is a city code
     * @param int      $adultsCount           Number of adults (1 - 10)
     * @param int      $childrenCount         Number of children (0 - 10)
     * @param int      $infantsCount          Number of infants (0 - 2)
     * @param string   $cabin                 Can be 'economy', 'business', 'first'
     * @param DateTime $outboundDate          Travel date from departure_code to arrival_code in departure time zone
     * @param DateTime $inboundDate           Return date from arrival_code to departure_code in arrival time zone
     *                                        (NULL for one-way trip)
     * @param string   $userCountryCode
     * @param string   $countrySiteCode       Country code of the user and site (use both parameters together).
     *                                        Some of our providers only support users from certain countries
     *                                        due to legal issues, with default value 'XX' certain content
     *                                        might be unavailable
     * @param string   $lang                  query locale
     *
     * @return object
     */
    public function startSearch(
        $departureCode,
        $departureCity,
        $arrivalCode,
        $arrivalCity,
        $adultsCount,
        $childrenCount,
        $infantsCount,
        $cabin,
        DateTime $outboundDate,
        DateTime $inboundDate = null,
        $userCountryCode = 'US',
        $countrySiteCode = 'US',
        $lang = 'en'
    )
    {
        $query = [
            "trips"             => [
                [
                    'departure_code' => $departureCode,
                    'arrival_code'   => $arrivalCode,
                    'outbound_date'  => $outboundDate->format(self::DATE_FORMAT),
                    'departure_city' => $departureCity,
                    'arrival_city'   => $arrivalCity,
                ],
            ],
            'adults_count'      => (int)$adultsCount,
            'children_count'    => (int)$childrenCount,
            'infants_count'     => (int)$infantsCount,
            'cabin'             => $cabin,
            'user_country_code' => $userCountryCode,
            'country_site_code' => $countrySiteCode,
            'locale'            => $lang,
        ];
        if ($inboundDate !== null) {
            $query['trips'][0]['inbound_date'] = $inboundDate->format(self::DATE_FORMAT);
        }
        return $this->http->post(
            '/flights/api/k/2/searches',
            $query
        );
    }

    /**
     * Add flight filters item as array if it's not empty
     *
     * @param string $name  filter name
     * @param array  $value filter data (probably empty)
     *
     * @return void
     */
    protected function setFilterArray($name, array $value)
    {
        if ($value) {
            $this->flightFilters[$name] = $value;
        }
    }

    /**
     * Add pair of from-to items to flight filters
     *
     * @param string $fromName  filter name for min value
     * @param string $toName    filter name for max value
     * @param int    $fromValue min value - skipped if 0
     * @param int    $toValue   max value - skipped if 0
     *
     * @return void
     */
    protected function setFilterMinMax($fromName, $toName, $fromValue, $toValue)
    {
        if ($fromValue <= 0) {
            throw new OutOfRangeException($fromName);
        }
        if ($toValue <= 0) {
            throw new OutOfRangeException($toName);
        }
        if ($fromValue > 0 && ($toValue == 0 || $toValue >= $fromValue)) {
            $this->flightFilters[$fromName] = $fromValue;
        }
        if ($toValue > 0 && ($fromValue == 0 || $fromValue <= $toValue)) {
            $this->flightFilters[$toName] = $toValue;
        }
    }

    /**
     * Make filters block for flight search request - price
     *
     * @param int $priceFrom min price or false if not set
     * @param int $priceTo   max price or false if not set
     *
     * @return void
     */
    public function setFilterPrice($priceFrom, $priceTo)
    {
        $this->setFilterMinMax('price_min_usd', 'price_max_usd', (int)$priceFrom, (int)$priceTo);
    }

    /**
     * Make filters block for flight search request - stops
     *
     * @param bool $none    if no stop available
     * @param bool $one     if one stop available
     * @param bool $twoPlus if 2+ stop available
     *
     * @return void
     */
    public function setFilterStops($none, $one, $twoPlus)
    {
        $types = [];
        if ($none) {
            $types[] = 'none';
        }
        if ($one) {
            $types[] = 'one';
        }
        if ($twoPlus) {
            $types[] = 'two_plus';
        }
        $this->setFilterArray('stop_types', $types);
    }

    /**
     * Make filters block for flight search request - offers
     *
     * @param array $airlines          Airline codes
     * @param array $providers         An array of provider codes (e.g. expedia.com)
     * @param array $designators       An array of designator codes (or full flight numbers)
     * @param array $departureAirports An array of departure airport codes
     * @param array $arrivalAirports   An array of arrival airport codes
     * @param array $stopoverAirports  An array of stopover airport codes
     *
     * @return void
     */
    public function setFilterOffers(
        array $airlines = [],
        array $providers = [],
        array $designators = [],
        array $departureAirports = [],
        array $arrivalAirports = [],
        array $stopoverAirports = []
    )
    {
        $this->setFilterArray('airline_codes', $airlines);
        $this->setFilterArray('provider_codes', $providers);
        $this->setFilterArray('designator_codes', $designators);
        $this->setFilterArray('departure_airport_codes', $departureAirports);
        $this->setFilterArray('arrival_airport_codes', $arrivalAirports);
        $this->setFilterArray('stopover_airport_codes', $stopoverAirports);
    }

    /**
     * Make filters block for flight search request - durations
     *
     * @param int $min         Minimum trip duration in minute
     * @param int $max         Maximum trip duration in minute
     * @param int $stopoverMin Minimum stopover duration in minute
     * @param int $stopoverMax Maximum stopover duration in minute
     *
     * @return void
     */
    public function setFilterDurations($min = 0, $max = 0, $stopoverMin = 0, $stopoverMax = 0)
    {
        $this->setFilterMinMax(
            'duration_min',
            'duration_max',
            (int)$min,
            (int)$max
        );
        $this->setFilterMinMax(
            'stopover_duration_min',
            'stopover_duration_max',
            (int)$stopoverMin,
            (int)$stopoverMax
        );
    }

    /**
     * Make filters block for flight search request - times
     *
     * @param int $outboundMin Outbound minimum departure day time in minute
     * @param int $outboundMax Outbound maximum departure day time in minute
     * @param int $inboundMin  Inbound minimum departure day time in minute (Only applicable for round-trip trips)
     * @param int $inboundMax  Inbound maximum departure day time in minute (Only applicable for round-trip trips)
     *
     * @return void
     */
    public function setFilterTimes($outboundMin = 0, $outboundMax = 0, $inboundMin = 0, $inboundMax = 0)
    {
        $this->setFilterMinMax(
            'outbound_departure_day_time_min',
            'outbound_departure_day_time_max',
            (int)$outboundMin,
            (int)$outboundMax
        );
        $this->setFilterMinMax(
            'inbound_departure_day_time_min',
            'inbound_departure_day_time_max',
            (int)$inboundMin,
            (int)$inboundMax
        );
        $this->flightFilters['departure_day_time_filter_type'] = 'separate';
    }

    /**
     * Get results of a flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#fares-get-results-of-a-search
     *
     * @param string $searchId Search id you get when the search is created
     * @param string $tripId   Trip id you get when the search is created
     * @param string $sort     price|duration|outbound_departure_time|inbound_departure_time
     * @param string $order    asc|desc
     * @param string $currency Currency to display prices in - use ISO 4217 3-letter currency codes. Defaults to USD
     * @param int    $page     Page of results to return
     * @param int    $perPage  Number of results to return per page
     *
     * @return object
     */
    public function getSearchResults(
        $searchId,
        $tripId,
        $sort = 'price',
        $order = 'asc',
        $currency = 'USD',
        $page = 1,
        $perPage = 20
    )
    {
        $query = array_merge(
            [
                'id'               => rand(), // A random string you need to assign for this query, used for debugging purposes
                'fares_query_type' => 'route',
                'search_id'        => $searchId,
                'trip_id'          => $tripId,
                'sort'             => $sort,
                'order'            => $order,
                'page'             => (int)$page,
                'per_page'         => (int)$perPage,
                'currency_code'    => $currency,
            ],
            $this->flightFilters
        );
        return $this->http->post(
            '/flights/api/k/2/fares',
            $query
        );
    }

    /**
     * Get deeplink to flight offer
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#customized-handoff-page
     *
     * @param string $deeplinkParams All fare.deeplink_params of the fare you want to deeplink
     *
     * @return object
     */
    public function getDeeplink($deeplinkParams)
    {
        return $this->http->get(
            '/flights/api/k/providers/2/deeplinks',
            $deeplinkParams
        );
    }

    /**
     * Get currencies available for flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#currencies
     *
     * @return array
     *
     * Response contains a lot of additional info, currencies list is at ['currencies']['currencies']:
     * [
     * "code" => "USD",
     * "symbol" => "US$",
     * "exchange_rate" => 1.0
     * ],
     * [
     * "code" => "SGD",
     * "symbol" => "S$",
     * "exchange_rate" => 1.2703
     * ]
     */
    public function getCurrencies()
    {
        return $this->http->get(
            '/flights/api/k/2/currencies',
            []
        );
    }
}
