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
     * @param \DateTime $date
     * @param string $email
     */
    public function sendEmail(\DateTime $date, string $email)
    {
        $ydate = clone $date;
        $stats_yesterday = $this->stats_mapper->getStats($ydate->modify('-1 day'));
        $stats_today = $this->stats_mapper->getStats($date);
        $stats = [];
        foreach ($stats_today as $statistic) {
            if ($statistic['users'] != null) {
                $stats['users'] = $statistic['users'];
            } elseif ($statistic['travels'] != null) {
                $stats['travels'] = $statistic['travels'];
            }
        }
        foreach ($stats_yesterday as $statistic) {
            if ($statistic['users'] != null) {
                $stats['delta_users'] = $stats['users'] - $statistic['users'];
            } elseif ($statistic['travels'] != null) {
                $stats['delta_travels'] = $stats['travels'] - $statistic['travels'];
            }
        }
        $this->mailer_service->sendStats($stats, $email);
    }
}
