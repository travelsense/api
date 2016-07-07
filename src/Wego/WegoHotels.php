<?php
namespace Api\Wego;

use DateTime;

/**
 * WAN.travel API client
 *
 * @see http://support.wan.travel/hc/en-us
 */
class WegoHotels
{
    const DATE_FORMAT = 'Ymd';

    /**
     * @var WegoHttpClient
     */
    private $http;

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
     * Start a new hotel search
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_search_new
     *
     * @param string   $location    The location ID of the location to search for.
     * @param DateTime $check_in     Check-in date
     * @param DateTime $check_out    Check-out date
     * @param int      $rooms       Number of hotel rooms required. Defaults to 1
     * @param int      $guests      Number of guests staying. Defaults to 2
     * @param string   $ip          The IP address of the end user who is performing the search (not your backend
     *                              server). We require this to display room rates that are valid for the user's
     *                              country.
     * @param string   $country     Country code of the user. We require this to display room rates
     *                              that are valid for the user's country.
     *
     * @return string Search ID
     */
    public function startSearch(
        $location,
        DateTime $check_in,
        DateTime $check_out,
        $rooms = 1,
        $guests = 2,
        $ip = 'direct',
        $country = 'US'
    )
    {
        $response = $this->http->get(
            '/hotels/api/search/new',
            [
                'location_id'           => $location,
                'check_in'              => $check_in->format(self::DATE_FORMAT),
                'check_out'             => $check_out->format(self::DATE_FORMAT),
                'user_ip'               => $ip,
                'country_code_for_site' => $country,
                'rooms'                 => (int)$rooms,
                'quests'                => (int)$guests,
            ]
        );

        return $response["search_id"];
    }

    /**
     * Search for a Wego Hotels location
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_locations_search
     *
     * @param         $query
     *
     * @param  string $lang    Language of results
     * @param  int    $page    Page of results to return. Use this together with per_page
     * @param  int    $per_page Number of results to return per page. Use this together with page. Defaults to 10
     * @return mixed
     */
    public function getLocations($query, $lang = 'en', $page = 1, $per_page = 10)
    {
        $query = preg_replace('/[^a-z0-9]/i', ' ', $query);
        $query = implode('_', preg_split('/ /', $query, -1, PREG_SPLIT_NO_EMPTY));
        $query = strtolower($query);

        return $this->http->get(
            '/hotels/api/locations/search',
            [
                'q'        => $query,
                'lang'     => $lang,
                'page'     => (int) $page,
                'per_page' => (int) $per_page,
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
     * @param string $popular_with 2-character country code
     * @param int    $page        Page of results to return
     * @param int    $per_page     Number of results to return per page
     *
     * @return mixed
     */
    public function getSearchResults(
        $id,
        $refresh = false,
        $currency = 'USD',
        $sort = 'popularity',
        $order = 'asc',
        $popular_with = 'XX',
        $page = 1,
        $per_page = 20
    )
    {
        return $this->http->get(
            '/hotels/api/search/' . urlencode($id),
            [
                'refresh'       => $refresh,
                'currency_code' => $currency,
                'sort'          => $sort ? 'true' : 'false',
                'order'         => $order,
                'popular_with'  => $popular_with,
                'page'          => $page,
                'per_page'      => $per_page,
            ]
        );
    }

    /**
     * Get details of a hotel (live search)
     *
     * @see http://support.wan.travel/hc/en-us/articles/200713154-Wego-Hotels-API#api_show_hotel_id
     *
     * @param string $search_id ID of search for retrieving "live" prices together with the hotel.
     * @param string $hotel_id  ID of the hotel.
     * @param string $currency Currency to display prices in - use ISO 4217 3-letter currency codes. Defaults to USD.
     * @param string $lang     Language of results. Defaults to en.
     *
     * @return mixed
     */
    public function getDetails($search_id, $hotel_id, $currency = 'USD', $lang = 'en')
    {
        return $this->http->get(
            '/hotels/api/search/show',
            [
                'search_id' => $search_id,
                'hotel_id'  => $hotel_id,
                'currency'  => $currency,
                'lang'      => $lang,
            ]
        );
    }
}
