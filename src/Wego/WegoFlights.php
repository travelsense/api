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
    private $flight_filters = [];

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
     * @param string   $departure_code          Departure airport or city code, IATA 3-letter code
     * @param bool     $departure_city          Set true if departure_code is a city code
     * @param string   $arrival_code            Arrival airport or city code, IATA 3-letter code
     * @param bool     $arrival_city            Set true if arrival_code is a city code
     * @param int      $adults_count            Number of adults (1 - 10)
     * @param int      $children_count          Number of children (0 - 10)
     * @param int      $infants_count           Number of infants (0 - 2)
     * @param string   $cabin                   Can be 'economy', 'business', 'first'
     * @param DateTime $outbound_date           Travel date from departure_code to arrival_code in departure time zone
     * @param DateTime $inbound_date            Return date from arrival_code to departure_code in arrival time zone
     *                                          (NULL for one-way trip)
     * @param string   $user_country_code
     * @param string   $country_site_code       Country code of the user and site (use both parameters together).
     *                                          Some of our providers only support users from certain countries
     *                                          due to legal issues, with default value 'XX' certain content
     *                                          might be unavailable
     * @param string   $lang                    query locale
     *
     * @return object
     */
    public function startSearch(
        string $departure_code,
        bool $departure_city,
        string $arrival_code,
        bool $arrival_city,
        int $adults_count,
        int $children_count,
        int $infants_count,
        string $cabin,
        DateTime $outbound_date,
        DateTime $inbound_date = null,
        string $user_country_code = 'US',
        string $country_site_code = 'US',
        string $lang = 'en'
    ) {
        $query = [
            "trips"             => [
                [
                    'departure_code' => $departure_code,
                    'arrival_code'   => $arrival_code,
                    'outbound_date'  => $outbound_date->format(self::DATE_FORMAT),
                    'departure_city' => $departure_city,
                    'arrival_city'   => $arrival_city,
                ],
            ],
            'adults_count'      => (int)$adults_count,
            'children_count'    => (int)$children_count,
            'infants_count'     => (int)$infants_count,
            'cabin'             => $cabin,
            'user_country_code' => $user_country_code,
            'country_site_code' => $country_site_code,
            'locale'            => $lang,
        ];
        if ($inbound_date !== null) {
            $query['trips'][0]['inbound_date'] = $inbound_date->format(self::DATE_FORMAT);
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
    protected function setFilterArray(string $name, array $value)
    {
        if ($value) {
            $this->flight_filters[$name] = $value;
        }
    }

    /**
     * Add pair of from-to items to flight filters
     *
     * @param string $from_name  filter name for min value
     * @param string $to_name    filter name for max value
     * @param int    $from_value min value - skipped if 0
     * @param int    $to_value   max value - skipped if 0
     *
     * @return void
     */
    protected function setFilterMinMax(string $from_name, string $to_name, int $from_value, int $to_value)
    {
        if ($from_value <= 0) {
            throw new OutOfRangeException($from_name);
        }
        if ($to_value <= 0) {
            throw new OutOfRangeException($to_name);
        }
        if ($from_value > 0 && ($to_value == 0 || $to_value >= $from_value)) {
            $this->flight_filters[$from_name] = $from_value;
        }
        if ($to_value > 0 && ($from_value == 0 || $from_value <= $to_value)) {
            $this->flight_filters[$to_name] = $to_value;
        }
    }

    /**
     * Make filters block for flight search request - price
     *
     * @param int $price_from min price or false if not set
     * @param int $price_to   max price or false if not set
     *
     * @return void
     */
    public function setFilterPrice(int $price_from, int $price_to)
    {
        $this->setFilterMinMax('price_min_usd', 'price_max_usd', (int)$price_from, (int)$price_to);
    }

    /**
     * Make filters block for flight search request - stops
     *
     * @param bool $none     if no stop available
     * @param bool $one      if one stop available
     * @param bool $two_plus if 2+ stop available
     *
     * @return void
     */
    public function setFilterStops(bool $none, bool $one, bool $two_plus)
    {
        $types = [];
        if ($none) {
            $types[] = 'none';
        }
        if ($one) {
            $types[] = 'one';
        }
        if ($two_plus) {
            $types[] = 'two_plus';
        }
        $this->setFilterArray('stop_types', $types);
    }

    /**
     * Make filters block for flight search request - offers
     *
     * @param array $airlines           Airline codes
     * @param array $providers          An array of provider codes (e.g. expedia.com)
     * @param array $designators        An array of designator codes (or full flight numbers)
     * @param array $departure_airports An array of departure airport codes
     * @param array $arrival_airports   An array of arrival airport codes
     * @param array $stopover_airports  An array of stopover airport codes
     *
     * @return void
     */
    public function setFilterOffers(
        array $airlines = [],
        array $providers = [],
        array $designators = [],
        array $departure_airports = [],
        array $arrival_airports = [],
        array $stopover_airports = []
    ) {
        $this->setFilterArray('airline_codes', $airlines);
        $this->setFilterArray('provider_codes', $providers);
        $this->setFilterArray('designator_codes', $designators);
        $this->setFilterArray('departure_airport_codes', $departure_airports);
        $this->setFilterArray('arrival_airport_codes', $arrival_airports);
        $this->setFilterArray('stopover_airport_codes', $stopover_airports);
    }

    /**
     * Make filters block for flight search request - durations
     *
     * @param int $min          Minimum trip duration in minute
     * @param int $max          Maximum trip duration in minute
     * @param int $stopover_min Minimum stopover duration in minute
     * @param int $stopover_max Maximum stopover duration in minute
     *
     * @return void
     */
    public function setFilterDurations(int $min = 0, int $max = 0, int $stopover_min = 0, int $stopover_max = 0)
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
            (int)$stopover_min,
            (int)$stopover_max
        );
    }

    /**
     * Make filters block for flight search request - times
     *
     * @param int $outbound_min Outbound minimum departure day time in minute
     * @param int $outbound_max Outbound maximum departure day time in minute
     * @param int $inbound_min  Inbound minimum departure day time in minute (Only applicable for round-trip trips)
     * @param int $inbound_max  Inbound maximum departure day time in minute (Only applicable for round-trip trips)
     *
     * @return void
     */
    public function setFilterTimes(
        int $outbound_min = 0,
        int $outbound_max = 0,
        int $inbound_min = 0,
        int $inbound_max = 0
    ) {
        $this->setFilterMinMax(
            'outbound_departure_day_time_min',
            'outbound_departure_day_time_max',
            (int)$outbound_min,
            (int)$outbound_max
        );
        $this->setFilterMinMax(
            'inbound_departure_day_time_min',
            'inbound_departure_day_time_max',
            (int)$inbound_min,
            (int)$inbound_max
        );
        $this->flight_filters['departure_day_time_filter_type'] = 'separate';
    }

    /**
     * Get results of a flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#fares-get-results-of-a-search
     *
     * @param string $search_id Search id you get when the search is created
     * @param string $trip_id   Trip id you get when the search is created
     * @param string $sort      price|duration|outbound_departure_time|inbound_departure_time
     * @param string $order     asc|desc
     * @param string $currency  Currency to display prices in - use ISO 4217 3-letter currency codes. Defaults to USD
     * @param int    $page      Page of results to return
     * @param int    $per_page  Number of results to return per page
     *
     * @return object
     */
    public function getSearchResults(
        string $search_id,
        string $trip_id,
        string $sort = 'price',
        string $order = 'asc',
        string $currency = 'USD',
        int $page = 1,
        int $per_page = 20
    ) {
        $query = array_merge(
            [
                'id'               => rand(), // A random string. See the documentation
                'fares_query_type' => 'route',
                'search_id'        => $search_id,
                'trip_id'          => $trip_id,
                'sort'             => $sort,
                'order'            => $order,
                'page'             => (int)$page,
                'per_page'         => (int)$per_page,
                'currency_code'    => $currency,
            ],
            $this->flight_filters
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
     * @param array $deeplink_params All fare.deeplink_params of the fare you want to deeplink
     *
     * @return object
     */
    public function getDeeplink(array $deeplink_params)
    {
        return $this->http->get(
            '/flights/api/k/providers/2/deeplinks',
            $deeplink_params
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
