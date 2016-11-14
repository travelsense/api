<?php
namespace Api\Controller\Travel;

use Api\Mapper\DB\BookingMapper;
use Api\Model\User;
use Api\Service\Mailer;
use Symfony\Component\HttpFoundation\Request;

class BookingController
{
    /**
     * @var BookingMapper
     */
    private $booking_mapper;

    /**
     * @var Mailer
     */
    private $mailer_service;

    /**
     * @var float
     */
    private $point_price = 0.01;

    /**
     * StatsController constructor.
     * @param BookingMapper $booking_mapper
     * @param Mailer        $mailer_service
     */
    public function __construct(BookingMapper $booking_mapper, Mailer $mailer_service)
    {
        $this->booking_mapper = $booking_mapper;
        $this->mailer_service = $mailer_service;
    }

    /**
     * @param float $point_price
     */
    public function setPointPrice(float $point_price)
    {
        $this->point_price = $point_price;
    }

    /**
     * @param User    $user
     * @param int     $id Travel ID
     * @param Request $request
     * @return array
     */
    public function registerBooking(User $user, int $id, Request $request)
    {
        $this->booking_mapper->registerBooking($user->getId(), $id);
        $this->mailer_service->sendBookingDetails(json_decode($request->getContent(), true));
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
            'bookingsTotal' => $bookings_total,
            'rewardTotal' => $bookings_total * $this->point_price,
            'bookingsLastWeek' => $this->booking_mapper->getStats($user->getId()),
        ];
    }
}
