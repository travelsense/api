<?php
namespace Api\Controller;

use Api\Wego\WegoHotelClient;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

class WegoHotelController
{
    /**
     * @var WegoHotelClient
     */
    private $wego;

    /**
     * WegoHotelController constructor.
     *
     * @param WegoHotelClient $wego
     */
    public function __construct(WegoHotelClient $wego)
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
    public function startSearch($location, DateTime $in, DateTime $out, $rooms)
    {
        return new JsonResponse($this->wego->startSearch($location, $in, $out, $rooms));
    }

    /**
     * Hotel search results
     *
     * @param  $id
     * @param  int $page
     * @return array
     */
    public function getSearchResults($id, $page)
    {
        return $this->wego->getSearchResults($id, false, 'USD', 'popularity', 'desc', 'XX', $page, 10);
    }
}
