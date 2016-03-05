<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\Mapper\DB\TravelMapper;
use Psr\Log\LoggerAwareTrait;

/**
 * Travel API controller
 */
class TravelController
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

    public function getTravel($id)
    {
        return [
            'id' => 0,
            'title' => 'Example travel',
            'description' => 'This is a mock travel object to help us understand what we need',
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
                'email' => 'example@example.com',
                'firstName' => 'Alexander',
                'lastName' => 'Radischev',
            ],
            'elements' => [
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
                    'offset' => 180,
                    'offsetUnit' => 'minute',
                    'type' => 'hotel',
                    'subtype' => 'check-in',
                    'hotel' => [
                        [
                            'name' => 'Cosmos',
                            'photos' => [],
                            'address' => [
                                'country' => 'RU',
                                'city' => 'Moscow',
                                'street' => 'pr-t. Mira, 150',
                                'zip' => 129366,
                            ],
                        ]
                    ]
                ],
                [
                    'offset' => 1,
                    'offsetUnit' => 'day',
                    'type' => 'sight',
                    'sight' => [
                        [
                            'name' => 'Red Square',
                            'photos' => [],
                            'address' => [
                                'country' => 'RU',
                                'city' => 'Moscow',
                                'street' => 'Red Square',
                                'zip' => 109012,
                            ],
                        ]
                    ]
                ],
                [
                    'offset' => 2880,
                    'offsetUnit' => 'minute',
                    'type' => 'hotel',
                    'subtype' => 'check-out',
                    'hotel' => [
                        [
                            'name' => 'Cosmos',
                            'photos' => [],
                            'address' => [
                                'country' => 'RU',
                                'city' => 'Moscow',
                                'street' => 'pr-t. Mira, 150',
                                'zip' => 129366,
                            ],
                        ]
                    ]
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
            ]
        ];
    }
}
