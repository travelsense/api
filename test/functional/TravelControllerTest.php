<?php
use Mapper\DB\TravelMapper;
use Model\Travel;
use Test\FunctionalTestCase;

class TravelControllerTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $travel = new Travel();
        $travel
            ->setDescription('Very cool travel')
            ->setTitle('Wow title');

        /** @var TravelMapper $mapper */
        $mapper = $this->app['mapper.db.travel'];
        $mapper->insert($travel);
    }

    public function testGetById()
    {

        $client = $this->createApiClient();
        $client->request(
            'GET',
            'https://example.com/travel/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $expected = [
            'title' => "Wow title",
            'description' => "Very cool travel",
        ];
        $this->assertEquals($expected, $client->getJson());
    }
}