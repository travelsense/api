<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\JSON\DataObject;
use Api\Mapper\DB\TravelMapper;
use Api\Model\Travel\Travel;
use Api\Model\User;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Travel API controller
 */
class TravelController extends ApiController
{
    use LoggerAwareTrait;

    /**
     * @var TravelMapper
     */
    private $travelMapper;

    /**
     * TravelController constructor.
     *
     * @param TravelMapper $travelMapper
     */
    public function __construct(TravelMapper $travelMapper)
    {
        $this->travelMapper = $travelMapper;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function createTravel(Request $request, User $user)
    {
        $json = DataObject::createFromString($request->getContent());

        $travel = new Travel();
        $travel->setAuthor($user);
        $travel->setTitle($json->getString('title'));
        $travel->setDescription($json->getString('description'));
        $this->travelMapper->insert($travel);

        return ['id' => $travel->getId()];
    }

    /**
     * @param $id
     * @return array
     * @throws ApiException
     */
    public function getTravel($id)
    {
        if ($id === 0) {
            return $this->getTravelMock();
        }
        $travel = $this->travelMapper->fetchById($id);
        if (!$travel) {
            throw ApiException::create(ApiException::RESOURCE_NOT_FOUND);
        }
        $author = $travel->getAuthor();
        return [
            'id' => $travel->getId(),
            'title' => $travel->getTitle(),
            'description' => $travel->getDescription(),
            'created' => $travel->getCreated()->format(self::DATETIME_FORMAT),
            'author' => [
                'id' => $author->getId(),
                'firstName' => $author->getFirstName(),
                'lastName' => $author->getLastName(),
                'picture' => $author->getPicture(),
            ]
        ];
    }

    public function addFavorite($id, User $user)
    {
        $userId = $user->getId();
        $travelId = $id;
        $this->travelMapper->addFavorite($travelId, $userId);
        return new JsonResponse();
    }

    public function removeFavorite($id, User $user)
    {
        $userId = $user->getId();
        $travelId = $id;
        $this->travelMapper->removeFavorite($travelId, $userId);
        return new JsonResponse();
    }

    public function getFavorite(User $user)
    {

    }

    public function getTravelMock()
    {
        $led = [
            'id' => 0,
            'iata' => 'LED',
            'name' => 'Pulkovo',
            'geo' => [59.800278, 30.2625]
        ];
        $svo = [
            'id' => 0,
            'iata' => 'SVO',
            'name' => 'Sheremetievo',
            'geo' => [55.972778, 37.414722],
        ];
        $dme = [
            'id' => 0,
            'iata' => 'DME',
            'name' => 'Domodedovo',
            'geo' => [55.408611, 37.906111],
        ];
        $cosmos = [
            'id' => 0,
            'name' => 'Cosmos',
            'images' => [
                'http://www.gostinica-kocmoc.ru/images/zdanie_gostinicy_kosmos_v_moskve-full8.jpg',
                'http://static.tonkosti.ru/images/b/b3/%D0%9A%D0%BE%D1%81%D0%BC%D0%BE%D1%81_%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0.jpg'
            ],
            'geo' => [55.8222, 37.6472],
            'address' => [
                'country' => 'RU',
                'city' => 'Moscow',
                'street' => 'pr-t. Mira, 150',
                'zip' => 129366,
            ],
        ];
        $redSquare = [
            'name' => 'The Red Square',
            'images' => [
                'http://strana.ru/media/images/uploaded/gallery_promo21092359.jpg',
                'http://olgazhdan.com/wp-content/uploads/MY_KREMLIN/IMG_3111.jpg'
            ],
            'geo' => [55.754194, 37.620139],
            'address' => [
                'country' => 'RU',
                'city' => 'Moscow',
                'street' => 'Red Square',
                'zip' => 109012,
            ],
        ];


        return [
            'id' => 0,
            'favorite' => true,
            'title' => 'Example travel',
            'description' => 'This is a mock travel object to help us understand what we need',
            'images' => [
                'http://www.provancewine.ru/assets/shop/images/vodka01.jpg',
                'http://s00.yaplakal.com/pics/pics_original/9/0/3/609309.jpg',
            ],
            'allowedDates' => [
                [
                    'first' => '2016-06-01',
                    'last' => '2016-08-31',
                ],
                [
                    'first' => '2016-12-01',
                    'last' => '2017-02-28',
                ],
            ],
            'author' => [
                'id' => 0,
                'image' => 'http://slon.gr/names/bday_photos/389.jpg',
                'firstName' => 'Alexander',
                'lastName' => 'Radischev',
            ],
            'elements' => [
                [
                    'offset' => 0,
                    'offsetUnit' => 'minute',
                    'type' => 'airport',
                    'airport' => $led,
                ],
                [
                    'offset' => 0,
                    'offsetUnit' => 'minute',

                    'type' => 'flight',
                    'segments' => [
                        [
                            'origin' => 'LED',
                            'destination' => 'SVO',
                            'duration' => 80
                        ]
                    ]
                ],
                [
                    'offset' => 80,
                    'offsetUnit' => 'minute',
                    'type' => 'airport',
                    'airport' => $svo,
                ],
                [
                    'offset' => 180,
                    'offsetUnit' => 'minute',
                    'type' => 'hotel',
                    'subtype' => 'check-in',
                    'hotel' => $cosmos,
                ],
                [
                    'offset' => 1,
                    'offsetUnit' => 'day',
                    'type' => 'sight',
                    'sight' => $redSquare,
                ],
                [
                    'offset' => 2880,
                    'offsetUnit' => 'minute',
                    'type' => 'hotel',
                    'subtype' => 'check-out',
                    'hotel' => $cosmos
                ],
                [
                    'offset' => 3000,
                    'offsetUnit' => 'minute',
                    'type' => 'airport',
                    'airport' => $dme,
                ],
                [
                    'offset' => 3000,
                    'offsetUnit' => 'minute',
                    'type' => 'flight',
                    'segments' => [
                        [
                            'origin' => 'DME',
                            'destination' => 'LED',
                            'duration' => 80
                        ]
                    ]
                ],
                [
                    'offset' => 3080,
                    'offsetUnit' => 'minute',
                    'type' => 'airport',
                    'airport' => $led,
                ],
            ]
        ];
    }
}
