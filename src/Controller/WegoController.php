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
