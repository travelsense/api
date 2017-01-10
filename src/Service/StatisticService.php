<?php
namespace Api\Service;

use Api\Mapper\DB\StatsMapper;

class StatisticService
{
    /**
     * @var StatsMapper
     */
    private $stats_mapper;

    public function __construct(StatsMapper $stats_mapper)
    {
        $this->stats_mapper = $stats_mapper;
    }

    public function sendEmail(\DateTime $date)
    {
        $stats = $this->stats_mapper->getStats($date);
    }
}
