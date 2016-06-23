<?php
namespace Api\Controller\Travel;

use Api\Mapper\DB\BookingMapper;
use Api\Model\User;

class BookingController
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

    /**
     * @param User $user
     * @param int $id Travel ID
     * @return array
     */
    public function registerBooking(User $user, int $id)
    {
        $this->bookingMapper->registerBooking($user->getId(), $id);
        return [];
    }

    /**
     * Get booking stats
     * @param User $user
     * @return array
     */
    public function getStats(User $user): array
    {
        return [
            'bookingsTotal' => $this->bookingMapper->getBooksTotal($user->getId()),
            'bookingsLastWeek' => $this->bookingMapper->getStats($user->getId()),
        ];
    }
}