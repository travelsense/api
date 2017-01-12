<?php
namespace Api\Service;

use Api\Mapper\DB\StatsMapper;

class StatisticService
{
    /**
     * @var Mailer
     */
    private $mailer_service;

    /**
     * @var StatsMapper
     */
    private $stats_mapper;

    /**
     * StatisticService constructor.
     * @param StatsMapper $stats_mapper
     * @param Mailer $mailer_service
     */
    public function __construct(StatsMapper $stats_mapper, Mailer $mailer_service)
    {
        $this->stats_mapper = $stats_mapper;
        $this->mailer_service = $mailer_service;
    }

    /**
     * Added statistic data to DB
     */
    public function buildStats()
    {
        $this->stats_mapper->buildStats();
    }

    /**
     * @param \DateTime $date
     */
    public function sendEmail(\DateTime $date)
    {
        $stats = $this->getStats($date);
        $this->mailer_service->sendStats($stats);
    }

    /**
     * Getting statistic data
     * @param \DateTime $date
     * @return array
     */
    private function getStats(\DateTime $date): array
    {
        $ydate = clone $date;
        $stats_yesterday = $this->stats_mapper->getStats($ydate->modify('-1 day'));
        $stats_today = $this->stats_mapper->getStats($date);
        $stats = [
            'users' => $stats_today['users'],
            'travels' => $stats_today['travels']
        ];
        if (!empty($stats_yesterday)) {
            $delta_users = $stats['users'] - $stats_yesterday['users'];
            $stats['delta_users'] = ($delta_users > 0) ? ('+'.$delta_users) : $delta_users;
            $delta_travels = $stats['travels'] - $stats_yesterday['travels'];
            $stats['delta_travels'] = ($delta_travels > 0) ? ('+'.$delta_travels) : $delta_travels;
        } else {
            $stats['delta_users'] = 0;
            $stats['delta_travels'] = 0;
        }
        return $stats;
    }
}
