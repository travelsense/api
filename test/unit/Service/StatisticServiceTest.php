<?php
namespace Api\Service;

use Api\Mapper\DB\StatsMapper;

class StatisticServiceTest extends \PHPUnit_Framework_TestCase
{
    private $stats_mapper;
    private $mailer_service;
    private $stats_service;

    protected function setUp()
    {
        $this->stats_mapper = $this->createMock(StatsMapper::class);

        $this->mailer_service = $this->createMock(Mailer::class);

        $this->stats_service = new StatisticService($this->stats_mapper, $this->mailer_service);
    }

    public function testSendStats()
    {
        $date = new \DateTime();
        $this->stats_mapper->expects($this->at(0))->method('getStats')
            ->with($date)
            ->willReturn([
                'users' => 2,
                'travels' => 10
            ]);
        $this->stats_mapper->expects($this->at(1))->method('getStats')
            ->with($date->modify('-1 day'))
            ->willReturn([
                'users' => 3,
                'travels' => 8
            ]);
        $this->mailer_service->expects($this->once())->method('sendStats')
            ->with($this->callback(function ($arr) {
                $this->assertEquals([
                    'users' => 2,
                    'travels' => 10,
                    'delta_users' => -1,
                    'delta_travels' => '+2'
                ], $arr);
                return $arr;
            }));
        $this->stats_service->sendEmail($date);
    }
}
