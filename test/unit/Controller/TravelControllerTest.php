<?php
namespace Api\Controller;

use Api\Controller\Travel\TravelController;
use Api\Test\ControllerTestCase;

class TravelControllerTest extends ControllerTestCase
{
    private $travel_mapper;
    private $category_mapper;
    private $action_mapper;

    /**
     * @var TravelController
     */
    private $controller;

    private $test_travel;

    /**
     * @var array
     */
    private $airportAction = array(
        "offsetStart" => 0,
        "hotels" => [],
        "id" => 2,
        "airports" => [],
        "offsetEnd" => 0,
        "type" => "flight",
        "sightseeings" => [],
        "car" => false
    );

    public function setUp()
    {
        $this->travel_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\TravelMapper')
            ->setMethods(['insert', 'fetchById', 'fetchByAuthorId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->category_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\CategoryMapper')
            ->setMethods(['insert', 'fetchAll', 'fetchAllByName'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->action_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\ActionMapper')
            ->setMethods(['insert', 'bindCommonValues', 'fetchActionsForTravel'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller = new TravelController(
            $this->travel_mapper,
            $this->category_mapper,
            $this->action_mapper
        );
        $this->test_travel = $this->buildTravel();
    }

    /**
     * getTravel
     */
    public function testGetTravel()
    {
        $this->travel_mapper->expects($this->once())
            ->method('fetchById')
            ->willReturn($this->test_travel);
        $this->assertEquals(
            [
                'id' => 1,
                'title' => 'test_travel',
                'description' => 'To make sure ids work properly',
                'content'     => [$this->airportAction],
                'created' => '2000-01-01T00:00:00+00:00',
                'category' => null,
                'category_ids' => [],
                'published'   => true,
                'creation_mode' => 'Travel test mode',
                'author' => [
                    'id' => 1,
                    'firstName' => 'User1',
                    'lastName' => 'Tester',
                    'picture' => 'http://example.com/user1.jpg'
                ],
                'is_favorited' => false,
                'image' => 'https://host.com/image.jpg',
                'places_count' => null,
                'days_count' => null
            ],
            $this->controller->getTravel(1)
        );
    }
}
