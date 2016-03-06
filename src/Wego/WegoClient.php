<?php
namespace Api\Wego;

use DateTime;
use PHPCurl\CurlHttp\HttpClient;

/**
 * WAN.travel API client
 *
 * @see http://support.wan.travel/hc/en-us
 */

class WegoClient
{
    const DATE_FORMAT = 'Ymd';

    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $tsCode;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $flightFilters;

    /**
     * Client constructor.
     *
     * @param string     $key
     * @param string     $tsCode
     * @param string     $apiUrl
     * @param HttpClient $http
     */
    public function __construct($key, $tsCode, $apiUrl = 'http://api.wego.com', HttpClient $http = null, $locale = 'en')
    {
        $this->key = $key;
        $this->tsCode = $tsCode;
        $this->apiUrl = $apiUrl;
        $this->http = $http ?: new HttpClient();
        $this->locale = $locale;
        $this->flightFilters = [];
    }

    /**
     * Start a new hotel search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_search_new
     *
     * @param string   $location The location ID of the location to search for.
     * @param DateTime $checkIn  Check-in date
     * @param DateTime $checkOut Check-out date
     * @param int      $rooms    Number of hotel rooms required. Defaults to 1
     * @param int      $guests   Number of guests staying. Defaults to 2
     * @param string   $ip       The IP address of the end user who is performing the search (not your backend server).
     *                              We require this to display room rates that are valid for the user's country.
     * @param string   $country  Country code of the user. We require this to display room rates
     *                              that are valid for the user's country.
     *
     * @return string Search ID
     */
    public function startHotelSearch(
        $location,
        DateTime $checkIn,
        DateTime $checkOut,
        $rooms = 1,
        $guests = 2,
        $ip = 'direct',
        $country = 'US'
    ) {
        $response = $this->httpGet(
            '/hotels/api/search/new',
            [
            'location_id' => $location,
            'check_in' => $checkIn->format(self::DATE_FORMAT),
            'check_out' => $checkOut->format(self::DATE_FORMAT),
            'user_ip' => $ip,
            'country_code_for_site' => $country,
            'rooms' => (int)$rooms,
            'quests' => (int)$guests,
            ]
        );

        return $response["search_id"];
    }

    /**
     * Search for a Wego Hotels location
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_locations_search
     *
     * @param $query
     *
     * @param  string $lang    Language of results
     * @param  int    $page    Page of results to return. Use this together with per_page
     * @param  int    $perPage Number of results to return per page. Use this together with page. Defaults to 10
     * @return mixed
     */
    public function getHotelLocations($query, $lang = 'en', $page = 1, $perPage = 10)
    {
        $query = preg_replace('/[^a-z0-9]/i', ' ', $query);
        $query = implode('_', preg_split('/ /', $query, -1, PREG_SPLIT_NO_EMPTY));
        $query = strtolower($query);

        return $this->httpGet(
            '/hotels/api/locations/search',
            [
            'q' => $query,
            'lang' => $lang,
            'page' => (int)$page,
            'per_page' => (int)$perPage,
            ]
        );
    }

    /**
     * Get results of a hotel search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_search_search_id
     *
     * @param string $id          ID of search for retrieving "live" prices together with the hotel
     * @param bool   $refresh     Whether to refresh the results with any new results since the last request.
     * @param string $currency    Currency to display prices in - use ISO 4217 3-letter currency codes. Defaults to USD
     * @param string $sort        popularity|name|price|satisfaction|stars
     * @param string $order       asc|desc
     * @param string $popularWith 2-character country code
     * @param int    $page        Page of results to return
     * @param int    $perPage     Number of results to return per page
     *
     * @return mixed
     */
    public function getHotelSearchResults(
        $id,
        $refresh = false,
        $currency = 'USD',
        $sort = 'popularity',
        $order = 'asc',
        $popularWith = 'XX',
        $page = 1,
        $perPage = 20
    ) {
        return $this->httpGet(
            '/hotels/api/search/' . urlencode($id),
            [
            'refresh' => $refresh,
            'currency_code' => $currency,
            'sort' => $sort ? 'true' : 'false',
            'order' => $order,
            'popular_with' => $popularWith,
            'page' => $page,
            'per_page' => $perPage,
            ]
        );
    }

    /**
     * Get details of a hotel (live search)
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_show_hotel_id
     *
     * @param string $searchID ID of search for retrieving "live" prices together with the hotel.
     * @param string $hotelID  ID of the hotel.
     * @param string $currency Currency to display prices in - use ISO 4217 3-letter currency codes. Defaults to USD.
     * @param string $lang     Language of results. Defaults to en.
     *
     * @return mixed
     */
    public function getHotelDetails($searchID, $hotelID, $currency = 'USD', $lang = 'en')
    {
        return $this->httpGet(
            '/hotels/api/search/show',
            [
            'search_id' => $searchID,
            'hotel_id' => $hotelID,
            'currency' => $currency,
            'lang' => $lang,
            ]
        );
    }

    /**
     * Do HTTP GET
     *
     * @param  string $uri
     * @param  array  $query
     * @return mixed Parsed JSON response
     */
    public function httpGet($uri, array $query)
    {
        $query['key'] = $this->key;
        $query['ts_code'] = $this->tsCode;
        $fullUrl = $this->apiUrl . $uri . '?' . http_build_query($query);
        $response = $this->http->get($fullUrl);
        $json = json_decode($response->getBody(), true);
        if ($response->getCode() === 200) {
            return $json;
        }
        throw new WegoApiException(isset($json['error']) ? $json['error'] : 'Unknown error', $response->getCode());
    }

    /**
     * Do HTTP POST
     *
     * @param  string $uri
     * @param  array  $query
     * @return mixed Parsed JSON response
     */
    public function httpPost($uri, array $query)
    {
        $query['key'] = $this->key;
        $query['ts_code'] = $this->tsCode;
        $query['locale'] = $this->locale;
        $fullUrl = $this->apiUrl . $uri;
        $preparedQuery = http_build_query($query);
        $response = $this->http->post($fullUrl, $preparedQuery);
        $json = json_decode($response->getBody(), true);
        if ($response->getCode() === 200) {
            return $json;
        }
        throw new WegoApiException(isset($json['error']) ? $json['error'] : 'Unknown error', $response->getCode());
    }

    /**
     * Start a new flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#search-start-an-new-flight-search
     *
     * @param string    $departureCode    Departure airport or city code, IATA 3-letter code
     * @param bool      $departureCity    Set true if departure_code is a city code
     * @param string    $arrivalCode      Arrival airport or city code, IATA 3-letter code
     * @param bool      $arrivalCity      Set true if arrival_code is a city code
     * @param int       $adultsCount      Number of adults (1 - 10)
     * @param int       $childrenCount    Number of children (0 - 10)
     * @param int       $infantsCount     Number of infants (0 - 2)
     * @param string    $cabin             Can be 'economy', 'business', 'first'
     * @param DateTime  $outboundDate     Travel date from departure_code to arrival_code in departure time zone
     * @param DateTime  $inboundDate      Return date from arrival_code to departure_code in arrival time zone (NULL for one-way trip)
     * @param string    $userCountryCode
     * @param string    $countrySiteCode  Country code of the user and site (use both parameters together).
     *                                        Some of our providers only support users from certain countries due to legal issues
     *                                        with default value 'XX' certain content might be unavailable
     *
     * @return array
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
        $query = [
            "trips" => [
                [
                'departure_code' => $departureCode,
                'arrival_code' => $arrivalCode,
                'outbound_date' => $outboundDate->format(self::DATE_FORMAT),
                'departure_city' => $departureCity,
                'arrival_city' => $arrivalCity
                ]
            ],
            'adults_count' => (int)$adultsCount,
            'children_count' => (int)$childrenCount,
            'infants_count' => (int)$infantsCount,
            'cabin' => $cabin,
            'user_country_code' => $userCountryCode,
            'country_site_code' => $countrySiteCode
        ];
        if($inboundDate !== NULL) {
            $query['trips']['inbound_date'] = $inboundDate->format(self::DATE_FORMAT);
        }
        $response = $this->httpGet(
            '/flights/api/k/2/searches',
            $query
        );
        return ['search_id' => $response["id"], 'trip_id' => $response['trips'][0]['id']];
    }

    /**
     * Add flight filters item as array if it's not empty
     *
     * @param string $name   filter name
     * @param array  $value  filter data (probably empty)
     *
     * @return void
     */
    protected function setFlightFilterArray($name, $value)
    {
        if(is_array($value)) {
            if(!empty($value)) {
                $this->flightFilters[$name] = $value;
            }
        } else {
            throw new WegoApiException('Flight filters error', $name);
        }
    }

    /**
     * Add pair of from-to items to flight filters
     *
     * @param string $fromName   filter name for min value
     * @param string $toName     filter name for max value
     * @param int    $fromValue  min value - skipped if 0
     * @param int    $toValue    max value - skipped if 0
     *
     * @return void
     */
    protected function setFlightFilterMinMax($fromName, $toName, $fromValue, $toValue)
    {
        if($fromValue > 0 && ($toValue == 0 || $toValue >= $fromValue)) {
            $this->flightFilters[$fromName] = $fromValue;
        }
        if($toValue > 0 && ($fromValue == 0 || $fromValue <= $toValue)) {
            $this->flightFilters[$toName] = $toValue;
        }
    }

    /**
     * Make filters block for flight search request - price
     *
     * @param int $priceFrom   min price or false if not set
     * @param int $priceTo     max price or false if not set
     *
     * @return void
     */
    public function setFlightFilterPrice($priceFrom, $priceTo)
    {
        $this->setFlightFilterMinMax('price_min_usd', 'price_max_usd', (int)$priceFrom, (int)$priceTo);
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
    public function setFlightFilterStops($none, $one, $twoPlus)
    {
        $types = [];
        if($none) $types[] = 'none';
        if($one) $types[] = 'one';
        if($twoPlus) $types[] = 'two_plus';
        $this->setFlightFilterArray('stop_types', $types);
    }

    /**
     * Make filters block for flight search request - offers
     *
     * @param array $airlines           Airline codes
     * @param array $providers          An array of provider codes (e.g. expedia.com)
     * @param array $designators        An array of designator codes (or full flight numbers)
     * @param array $departureAirports  An array of departure airport codes
     * @param array $arrivalAirports    An array of arrival airport codes
     * @param array $stopoverAirports   An array of stopover airport codes
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
        $this->setFlightFilterArray('airline_codes', $airlines);
        $this->setFlightFilterArray('provider_codes', $providers);
        $this->setFlightFilterArray('designator_codes', $designators);
        $this->setFlightFilterArray('departure_airport_codes', $departureAirports);
        $this->setFlightFilterArray('arrival_airport_codes', $arrivalAirports);
        $this->setFlightFilterArray('stopover_airport_codes', $stopoverAirports);
    }

    /**
     * Make filters block for flight search request - durations
     *
     * @param int $min                Minimum trip duration in minute
     * @param int $max                Maximum trip duration in minute
     * @param int $stopoverMin       Minimum stopover duration in minute
     * @param int $stopoverMax       Maximum stopover duration in minute
     *
     * @return void
     */
    public function setFlightFilterDurations($min = 0, $max = 0, $stopoverMin = 0, $stopoverMax = 0) {
        $this->setFlightFilterMinMax('duration_min', 'duration_max', (int)$min, (int)$max);
        $this->setFlightFilterMinMax('stopover_duration_min', 'stopover_duration_max', (int)$stopoverMin, (int)$stopoverMax);
    }

    /**
     * Make filters block for flight search request - times
     *
     * @param int $outboundMin  Outbound minimum departure day time in minute
     * @param int $outboundMax  Outbound maximum departure day time in minute
     * @param int $inboundMin   Inbound minimum departure day time in minute (Only applicable for round-trip trips)
     * @param int $inboundMax   Inbound maximum departure day time in minute (Only applicable for round-trip trips)
     *
     * @return void
     */
    public function setFlightFilterTimes($outboundMin = 0, $outboundMax = 0, $inboundMin = 0, $inboundMax = 0)
    {
        $this->setFlightFilterMinMax('outbound_departure_day_time_min', 'outbound_departure_day_time_max', (int)$outboundMin, (int)$outboundMax);
        $this->setFlightFilterMinMax('inbound_departure_day_time_min', 'inbound_departure_day_time_max', (int)$inboundMin, (int)$inboundMax);
        $this->flightFilters['departure_day_time_filter_type'] = 'separate';
    }

    /**
     * Get results of a flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#fares-get-results-of-a-search
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
        $query = array_merge(
            [
                'id' => rand(), // A random string you need to assign for this query, used for debugging purposes
                'fares_query_type' => 'route',
                'search_id' => $searchId,
                'trip_id' => $tripId,
                'sort' => $sort,
                'order' => $order,
                'page' => (int)$page,
                'per_page' => (int)$perPage,
                'currency_code' => $currency
            ],
            $this->flightFilters
        );
        return $this->httpPost(
            '/flights/api/k/2/fares',
            $query
        );
    }

    /**
     * Get deeplink to flight offer
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#customized-handoff-page
     *
     * @param string $searchId    Search id you get when the search is created
     * @param string $tripId      Trip id you get when the search is created
     * @param string $fareId      Fare id of the fare you want to deeplink
     * @param string $route       A combination of fare.departure_airport_code and fare.arrival_airport_code, e.g. SIN-HAN
     *
     * @return object
     */
    public function getFlightDeeplink($searchId, $tripId, $fareId, $route)
    {
        return $this->httpGet(
            '/flights/api/k/providers/2/deeplinks',
            [
                'search_id' => $searchId,
                'trip_id' => $tripId,
                'fare_id' => $fareId,
                'route' => $route
            ]
        );
    }

    /**
     * Get currencies available for flight search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200191669-Wego-Flights-API#currencies
     *
     * @return array
     *
     * Answer example:
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
    public function getFlightCurrencies()
    {
        $response = $this->httpGet(
            '/flights/api/k/2/currencies',
            []
        );
        return $response['currencies'] ?: [];
    }
}
