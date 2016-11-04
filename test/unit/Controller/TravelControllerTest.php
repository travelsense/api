<?php
namespace Api\Controller;

use Api\Mapper\DB\BannerMapper;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\ActionMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Model\Travel\Travel;
use Api\Security\Access\AccessManager;
use Api\Controller\Travel\TravelController;
use Api\Test\ControllerTestCase;
use DateTime;

class TravelControllerTest extends ControllerTestCase
{
    private $travel_mapper;
    private $category_mapper;
    private $action_mapper;
    private $banner_mapper;
    private $access_manager;

    /**
     * @var TravelController
     */
    private $controller;

    private $test_travel;

    /**
     * @var array
     */
    private $airportAction = [
        "offsetStart" => 0,
        "hotels" => [],
        "id" => 2,
        "airports" => [],
        "offsetEnd" => 0,
        "type" => "flight",
        "sightseeings" => [],
        "car" => false,
        "index" => -1,
        "end_index" => -1,
        "action_transportation" => 1
    ];

    public function setUp()
    {
        $this->travel_mapper = $this->getMockBuilder(TravelMapper::class)
            ->setMethods(['insert', 'fetchById', 'fetchFavoriteIds', 'fetchByAuthorId', 'fetchPublishedByAuthorId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->category_mapper = $this->getMockBuilder(CategoryMapper::class)
            ->setMethods(['insert', 'fetchAll', 'fetchAllByName'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->action_mapper = $this->getMockBuilder(ActionMapper::class)
            ->setMethods(['insert', 'bindCommonValues', 'fetchActionsForTravel'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->banner_mapper = $this->getMockBuilder(BannerMapper::class)
            ->setMethods(['fetchBanners'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->access_manager = $this->getMockBuilder(AccessManager::class)
            ->setMethods(['isGranted', 'hasWritePermission'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller = new TravelController(
            $this->travel_mapper,
            $this->category_mapper,
            $this->action_mapper,
            $this->banner_mapper,
            $this->access_manager
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
                'content'     => [(object)$this->airportAction],
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
                'places_count' => 1,
                'days_count' => 0,
                'estimated_price' => null,
                'transportation' =>null
            ],
            $this->controller->getTravel(1)
        );
    }

    /**
     * getTravel with User
     */
    public function testGetTravelWithUser()
    {
        $user = $this->buildUser();

        $this->travel_mapper->expects($this->once())
            ->method('fetchById')
            ->willReturn($this->test_travel);
        $this->assertEquals(
            [
                'id' => 1,
                'title' => 'test_travel',
                'description' => 'To make sure ids work properly',
                'content'     => [(object)$this->airportAction],
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
                'places_count' => 1,
                'days_count' => 0,
                'estimated_price' => null,
                'transportation' => null
            ],
            $this->controller->getTravel(1, $user)
        );
    }

    /**
     * getPublishedTravelsByAuthorId
     */
    public function testGetPublishedTravelsByAuthorId()
    {
        $this->travel_mapper->expects($this->once())
            ->method('fetchPublishedByAuthorId')
            ->willReturn([$this->test_travel]);
        $this->assertEquals(
            [[
                'id' => 1,
                'title' => 'test_travel',
                'is_favorited' => false,
                'image' => 'https://host.com/image.jpg',
                'places_count' => 1,
                'days_count' => 0
            ]],
            $this->controller->getPublishedByAuthor(1)
        );
    }

    /**
     * getPublishedTravelsByAuthorId with User
     */
    public function testGetPublishedTravelsByAuthorIdWithUser()
    {
        $user = $this->buildUser();

        $this->travel_mapper->expects($this->once())
            ->method('fetchPublishedByAuthorId')
            ->willReturn([$this->test_travel]);
        $this->assertEquals(
            [[
                'id' => 1,
                'title' => 'test_travel',
                'is_favorited' => false,
                'image' => 'https://host.com/image.jpg',
                'places_count' => 1,
                'days_count' => 0
            ]],
            $this->controller->getPublishedByAuthor(1, $user)
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildTravel()
    {
        $travel = $this->getMockBuilder(Travel::class)
            ->setMethods([
                'getId', 'getTitle', 'getDescription', 'isPublished', 'getImage',
                'getContent', 'getCreationMode', 'getCreated', 'getAuthor'
            ])
            ->getMock();
        $travel->method('getId')->willReturn(1);
        $travel->method('getTitle')->willReturn('test_travel');
        $travel->method('getDescription')->willReturn('To make sure ids work properly');
        $travel->method('isPublished')->willReturn(true);
        $travel->method('getImage')->willReturn('https://host.com/image.jpg');
        $travel->method('getContent')->willReturn([(object)$this->airportAction]);
        $travel->method('getCreationMode')->willReturn('Travel test mode');
        $travel->method('getCreated')->willReturn(new DateTime('2000-01-01'));
        $travel->method('getAuthor')->willReturn($this->buildUser());
        return $travel;
    }
}
