<?php
namespace Api\Controller\Travel;

use Api\Mapper\DB\BookingMapper;
use Api\Model\User;
use Api\Service\Mailer;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class BookingController
{
    use LoggerAwareTrait;

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
    private $percent_reward = 0.01; // 1 %

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
     * @param float $percent_reward
     */
    public function setPercentReward(float $percent_reward)
    {
        $this->percent_reward = $percent_reward;
    }

    /**
     * @param User    $user
     * @param int     $id Travel ID
     * @param Request $request
     * @return array
     */
    public function registerBooking(User $user, int $id, Request $request)
    {
        $json = $request->getContent();
        $json_obj = json_decode($json);
        $reward = $json_obj->totalPrice ? (($json_obj->totalPrice * 100) * $this->percent_reward) : 0;
        if ($this->logger) {
            $this->logger->debug($json);
        }
        $this->booking_mapper->registerBooking($user->getId(), $id, round($reward));
        $this->mailer_service->sendBookingDetails(json_decode($json, true));
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
            'bookingsTotal' => $bookings_total['bookings_total'],
            'rewardTotal' => $bookings_total['reward_total'],
            'bookingsLastWeek' => $this->booking_mapper->getStats($user->getId()),
        ];
    }
}
