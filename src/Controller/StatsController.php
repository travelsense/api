<?php
namespace Api\Controller;


use Api\Mapper\DB\BookingMapper;
use Api\Model\User;

class StatsController
{
    /**
     * @var BookingMapper
     */
    private $bookingMapper;

    /**
     * StatsController constructor.
     * @param BookingMapper $bookingMapper
     */
    public function __construct(BookingMapper $bookingMapper)
    {
        $this->bookingMapper = $bookingMapper;
    }

    public function getStats(User $user): array
    {
        return [
            'stats' => [
                'bookingsTotal' => $this->bookingMapper->getBooksTotal($user->getId()),
                'bookingsLastWeek' => $this->bookingMapper->getStats($user->getId()),
            ]
        ];
    }

}