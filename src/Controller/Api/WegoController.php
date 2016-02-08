<?php
namespace Controller\Api;

use DateTime;
use Wego\WegoClient;

class WegoController
{
    /**
     * @var WegoClient
     */
    private $wego;

    /**
     * WegoController constructor.
     * @param WegoClient $wego
     */
    public function __construct(WegoClient $wego)
    {
        $this->wego = $wego;
    }

    /**
     * Start hotels search
     * @param string $location Location ID
     * @param DateTime $in
     * @param DateTime $out
     * @param int $rooms
     * @return array
     */
    public function startSearch($location, DateTime $in, DateTime $out, $rooms)
    {
        return [
            'searchId' => $this->wego->startSearch($location, $in, $out, $rooms),
        ];
    }

    /**
     * Hotel search results
     * @param $id
     * @param int $page
     * @return array
     */
    public function getSearchResults($id, $page)
    {
        return $this->wego->getSearchResults($id, false, 'USD', 'popularity', 'desc', 'XX', $page, 10);
    }
}
