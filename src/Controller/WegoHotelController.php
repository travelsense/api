<?php
namespace Api\Controller;

use Api\Wego\WegoHotels;
use DateTime;
use PDO;

class WegoHotelController
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

    /**
     * @param array $response
     */
    private function updateLocalHotelCache(array $response)
    {
        $location = $response['location'];
        $hotels = $response['hotels'];
        foreach ($hotels as $hotel) {
            $hotelId = $this->getHotelIdByWegoId($hotel['id']);
            if ($hotelId !== false) {
                $this->updateHotelData($location, $hotel['name'], $hotel['address'], $hotel['latitude'], $hotel['longitude'], $hotel['desc'], $hotel['stars'], $hotelId);
            } else {
                $hotelId = $this->insertHotelData($location, $hotel['name'], $hotel['address'], $hotel['latitude'], $hotel['longitude'], $hotel['desc'], $hotel['stars']);
                $this->addWegoIdForHotelId($hotelId, $hotel['id']);
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
    private function insertHotelData(string $location, string $name, string $address, float $lat, float $lon, string $desc, int $stars): int
    {
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
     * @param int    $hotelId
     */
    private function updateHotelData(string $location, string $name, string $address, float $lat, float $lon, string $desc, int $stars, int $hotelId)
    {
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
            ':id'          => $hotelId,
        ]);
    }

    /**
     * @param int $hotelId
     * @param int $wegoId
     */
    private function addWegoIdForHotelId(int $hotelId, int $wegoId)
    {
        $insert = $this->pdo->prepare(
            'INSERT INTO wego_hotels
            (hotel_id, wego_hotel_id)
             VALUES (:id, :wego_hotel_id)'
        );
        $insert->execute([
            ':id'            => $hotelId,
            ':wego_hotel_id' => $wegoId,
        ]);
    }

    /**
     * @param int $wegoId
     * @return int|false
     */
    private function getHotelIdByWegoId(int $wegoId)
    {
        $select = $this->pdo->prepare('SELECT hotel_id FROM wego_hotels WHERE wego_hotel_id = :wego_hotel_id');
        $select->execute([':wego_hotel_id' => $wegoId]);
        return $select->fetchColumn();
    }
}
