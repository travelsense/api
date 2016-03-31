<?php
namespace Api\Controller;

use Api\Wego\WegoHotelClient;
use PDO;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

class WegoHotelController
{
    /**
     * @var WegoHotelClient
     */
    private $wego;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * WegoHotelController constructor.
     *
     * @param WegoHotelClient $wego
     * @param PDO $pdo
     */
    public function __construct(WegoHotelClient $wego, PDO $pdo)
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
     * @return JsonResponse
     */
    public function startSearch($location, DateTime $in, DateTime $out, $rooms)
    {
        return new JsonResponse($this->wego->startSearch($location, $in, $out, $rooms));
    }

    /**
     * Hotel search results
     *
     * @param  int $id
     * @param  int $page
     * @return array
     */
    public function getSearchResults($id, $page)
    {
        $response = $this->wego->getSearchResults($id, false, 'USD', 'popularity', 'desc', 'XX', $page, 10);
        $this->getHotelsFromWego($response);
        return $response;
    }

    /**
     * @param array $response
     */
    protected function getHotelsFromWego($response)
    {
        $location = $response['location'];
        $hotels = $response['hotels'];
        foreach ($hotels as $hotel) {
            $hotelId  = $this->getHotelIdByWegoId($hotel['id']);
            if ($hotelId != false){
                $this->updateHotelData($location, $hotel['name'], $hotel['address'], $hotel['latitude'], $hotel['longitude'], $hotel['desc'], $hotel['stars'], $hotelId);
            } else{
                $hotelId = $this->addHotelData($location, $hotel['name'], $hotel['address'], $hotel['latitude'], $hotel['longitude'], $hotel['desc'], $hotel['stars']);
                $this->addWegoIdForHotelId($hotelId, $hotel['id']);
            }
        }
    }

    /**
     * @param string $location
     * @param string $name
     * @param string $address
     * @param float $lat
     * @param float $lon
     * @param string $desc
     * @param int $stars
     * @return int
     */
    public function addHotelData($location, $name, $address, $lat, $lon, $desc, $stars)
    {
        $insert = $this->pdo->prepare(
            'INSERT INTO hotels
            (name, location, address, lat, lon, description, stars)
             VALUES
            (:name, :location, :address, :lat, :lon, :description, :stars) RETURNING id'
        );
        $insert->execute([
            ':name' => $name,
            ':location' => $location,
            ':address' => $address,
            ':lat' => $lat,
            ':lon' => $lon,
            ':description' => $desc,
            ':stars' => $stars
        ]);
        $id = $insert->fetchColumn();
        return $id;
    }

    /**
     * @param string $location
     * @param string $name
     * @param string $address
     * @param float $lat
     * @param float $lon
     * @param string $desc
     * @param int $stars
     * @return int
     */
    public function updateHotelData($location, $name, $address, $lat, $lon, $desc, $stars, $hotelId)
    {
        $insert = $this->pdo->prepare(
            'UPDATE hotels SET
             name = :name, location = :location, address = :address,
             lat = :lat, lon = :lon, description = :description, stars = :stars
             WHERE id = :id RETURNING id'
        );
        $insert->execute([
            ':name' => $name,
            ':location' => $location,
            ':address' => $address,
            ':lat' => $lat,
            ':lon' => $lon,
            ':description' => $desc,
            ':stars' => $stars,
            ':id' => $hotelId
        ]);
        return $insert->fetchColumn();
    }

    /**
     * @param int $hotelId
     * @param int $wegoId
     */
    public function addWegoIdForHotelId($hotelId, $wegoId)
    {
        $insert = $this->pdo->prepare(
            'INSERT INTO self_wego_hotel
            (hotels_id, wego_hotel_id)
             VALUES (:hotels_id, :wego_hotel_id)'
        );
        $insert->execute([
            ':hotels_id' => $hotelId,
            ':wego_hotel_id' => $wegoId
        ]);
    }

    /**
     * @param int $wegoId
     * @return int
     */
    public function getHotelIdByWegoId($wegoId)
    {
        $select = $this->pdo->prepare('SELECT hotels_id FROM self_wego_hotel WHERE wego_hotel_id = :wego_hotel_id');
        $select->execute([':wego_hotel_id' => $wegoId]);
        return $select->fetchColumn();
    }
}
