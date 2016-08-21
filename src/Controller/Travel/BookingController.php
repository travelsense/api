<?php
namespace Api\Controller\Travel;

use Api\Mapper\DB\BookingMapper;
use Api\Model\User;

class BookingController
{
    /**
     * @var BookingMapper
     */
    private $booking_mapper;

    /**
     * @var float
     */
    private $point_price = 0.01;

    /**
     * StatsController constructor.
     * @param BookingMapper $booking_mapper
     */
    public function __construct(BookingMapper $booking_mapper)
    {
        $this->booking_mapper = $booking_mapper;
    }

    /**
     * @param float $point_price
     */
    public function setPointPrice(float $point_price)
    {
        $this->point_price = $point_price;
    }

    /**
     * @param User $user
     * @param int  $id Travel ID
     * @return array
     */
    public function registerBooking(User $user, int $id)
    {
        $this->booking_mapper->registerBooking($user->getId(), $id);
        return [];
    }

    /**
     * Get booking stats
     * @param User $user
     * @return array
     */
    public function getStats(User $user): array
    {
        $bookings_total = $this->booking_mapper->getBookingsTotal($user->getId());
        return [
            'bookingsTotal'    => $bookings_total,
            'rewardTotal'      => $bookings_total * $this->point_price,
            'bookingsLastWeek' => $this->booking_mapper->getStats($user->getId()),
        ];
    }
}
