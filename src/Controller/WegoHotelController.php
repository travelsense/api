<?php
namespace Api\Controller;

use Api\Wego\WegoHotels;
use DateTime;
use PDO;

class WegoHotelController extends ApiController
{
    /**
     * @var WegoHotels
     */
    private $wego;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * WegoHotelController constructor.
     *
     * @param WegoHotels $wego
     * @param PDO        $pdo
     */
    public function __construct(WegoHotels $wego, PDO $pdo)
    {
        $this->wego = $wego;
        $this->pdo = $pdo;
    }

    /**
     * Start hotels search
     *
     * @param  string   $location Location ID
     * @param  DateTime $in
     * @param  DateTime $out
     * @param  int      $rooms
     * @return array
     */
    public function startSearch($location, DateTime $in, DateTime $out, int $rooms): array
    {
        return [
            'search_id' => $this->wego->startSearch($location, $in, $out, $rooms),
        ];
    }

    /**
     * Hotel search results
     *
     * @param  string $id
     * @param  int    $page
     * @return array
     */
    public function getSearchResults(string $id, int $page): array
    {
        $response = $this->wego->getSearchResults($id, false, 'USD', 'popularity', 'desc', 'XX', $page, 10);
        $this->updateLocalHotelCache($response);
        return $response;
    }

    /*
     * Get details of a hotel (live search)
     *
     * @param string $id
     * @param int $hotel_id
     * @return array
     */
    public function getDetails(string $id, int $hotel_id, $currency = 'USD', $lang = 'en') : array
    {
        $response = $this->wego->getDetails($id, $hotel_id, $currency, $lang);
        return $response;
    }

    /**
     * @param array $response
     */
    private function updateLocalHotelCache(array $response)
    {
        $location = $response['location'];
        $hotels = $response['hotels'];
        foreach ($hotels as $hotel) {
            $hotel_id = $this->getHotelIdByWegoId($hotel['id']);
            if ($hotel_id !== false) {
                $this->updateHotelData(
                    $location,
                    $hotel['name'],
                    $hotel['address'],
                    $hotel['latitude'],
                    $hotel['longitude'],
                    $hotel['desc'],
                    $hotel['stars'],
                    $hotel_id
                );
            } else {
                $hotel_id = $this->insertHotelData(
                    $location,
                    $hotel['name'],
                    $hotel['address'],
                    $hotel['latitude'],
                    $hotel['longitude'],
                    $hotel['desc'],
                    $hotel['stars']
                );
                $this->addWegoIdForHotelId($hotel_id, $hotel['id']);
            }
        }
    }

    /**
     * @param string $location
     * @param string $name
     * @param string $address
     * @param float  $lat
     * @param float  $lon
     * @param string $desc
     * @param int    $stars
     * @return int
     */
    private function insertHotelData(
        string $location,
        string $name,
        string $address,
        float $lat,
        float $lon,
        string $desc,
        int $stars
    ): int {
        $insert = $this->pdo->prepare(
            'INSERT INTO hotels
            (name, location, address, lat, lon, description, stars)
             VALUES
            (:name, :location, :address, :lat, :lon, :description, :stars) RETURNING id'
        );
        $insert->execute([
            ':name'        => $name,
            ':location'    => $location,
            ':address'     => $address,
            ':lat'         => $lat,
            ':lon'         => $lon,
            ':description' => $desc,
            ':stars'       => $stars,
        ]);
        $id = $insert->fetchColumn();
        return $id;
    }

    /**
     * @param string $location
     * @param string $name
     * @param string $address
     * @param float  $lat
     * @param float  $lon
     * @param string $desc
     * @param int    $stars
     * @param int    $hotel_id
     */
    private function updateHotelData(
        string $location,
        string $name,
        string $address,
        float $lat,
        float $lon,
        string $desc,
        int $stars,
        int $hotel_id
    ) {
        $insert = $this->pdo->prepare(
            'UPDATE hotels SET
             name = :name, location = :location, address = :address,
             lat = :lat, lon = :lon, description = :description, stars = :stars
             WHERE id = :id'
        );
        $insert->execute([
            ':name'        => $name,
            ':location'    => $location,
            ':address'     => $address,
            ':lat'         => $lat,
            ':lon'         => $lon,
            ':description' => $desc,
            ':stars'       => $stars,
            ':id'          => $hotel_id,
        ]);
    }

    /**
     * @param int $hotel_id
     * @param int $wego_id
     */
    private function addWegoIdForHotelId(int $hotel_id, int $wego_id)
    {
        $insert = $this->pdo->prepare(
            'INSERT INTO wego_hotels
            (hotel_id, wego_hotel_id)
             VALUES (:id, :wego_hotel_id)'
        );
        $insert->execute([
            ':id'            => $hotel_id,
            ':wego_hotel_id' => $wego_id,
        ]);
    }

    /**
     * @param int $wego_id
     * @return int|false
     */
    private function getHotelIdByWegoId(int $wego_id)
    {
        $select = $this->pdo->prepare('SELECT hotel_id FROM wego_hotels WHERE wego_hotel_id = :wego_hotel_id');
        $select->execute([':wego_hotel_id' => $wego_id]);
        return $select->fetchColumn();
    }
}
