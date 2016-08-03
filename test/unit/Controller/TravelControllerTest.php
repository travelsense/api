<?php
namespace Api\Controller;

use Api\Controller\Travel\TravelController;
use Api\Model\Travel\Travel;
use Api\Test\ControllerTestCase;
use DateTime;

class TravelControllerTest extends ControllerTestCase
{
    private $travel_mapper;
    private $category_mapper;

    /**
     * @var TravelController
     */
    private $controller;

    private $test_travel;

    public function setUp()
    {
        $this->travel_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\TravelMapper')
            ->setMethods(['insert', 'fetchById', 'fetchByAuthorId', 'update', 'bindCommonValues'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->category_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\CategoryMapper')
            ->setMethods(['insert', 'fetchAll', 'fetchAllByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new TravelController(
            $this->travel_mapper,
            $this->category_mapper
        );

        $this->test_travel = $this->buildTravel();
    }

    /**
     * create Travel
     */
    public function testCreateTravel()
    {
        $user = $this->buildUser();

        $json = json_encode([
            'id' => 1,
            'title' => 'test_travel',
            'description' => 'To make sure ids work properly',
            'published'   => false,
            'image' => 'https://host.com/image.jpg',
            'content'     => ['foo' => 'bar'],
            'creation_mode' => 'Travel test mode'
        ]);

        $request = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Request')
            ->setMethods(['getContent'])
            ->getMock();

        $request->method('getContent')->willReturn($json);

        $travel = $this->getMockBuilder('Api\\Model\\Travel\\Travel')
            ->setMethods(['getId', 'getTitle', 'getDescription', 'isPublished', 'getImage', 'getContent', 'getCreationMode'])
            ->getMock();

        $this->travel_mapper->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (Travel $travel) {
                return $travel->getTitle() === 'test_travel'
                    && $travel->getDescription() === 'To make sure ids work properly'
                    && $travel->isPublished() === false
                    && $travel->getImage() === 'https://host.com/image.jpg'
                    && $travel->getCreationMode() === 'Travel test mode';
            }));

        $this->assertEquals(['id' => $travel->getId()], $this->controller->createTravel($request, $user));
    }

    /**
     * getTravel
     */
    public function testGetTravel()
    {
        $this->travel_mapper->expects($this->any())
            ->method('fetchById')
            ->will($this->returnValue($this->test_travel));
        $this->assertEquals(
            [
                'id' => 1,
                'title' => 'test_travel',
                'description' => 'To make sure ids work properly',
                'published'   => true,
                'image' => 'https://host.com/image.jpg',
                'content'     => ['foo' => 'bar'],
                'creation_mode' => 'Travel test mode',
                'created' => '2000-01-01T00:00:00+00:00',
                'category' => null,
                'category_ids' => [],
                'is_favorited' => false,
                'author' => [
                    'id' => 1,
                    'firstName' => 'User1',
                    'lastName' => 'Tester',
                    'picture' => 'http://example.com/user1.jpg'
                ]
            ],
            $this->controller->getTravel(1)
        );
    }

    /*
     * getUserTravel
     */
    public function testGetUserTravel()
    {
        $user = $this->buildUser();

        $this->travel_mapper->method('fetchByAuthorId')
            ->willReturn(
                [$this->test_travel]
            );
        $this->assertEquals(
            [[
                'id' => 1,
                'title' => 'test_travel',
                'description' => 'To make sure ids work properly',
                'published'   => true,
                'image' => 'https://host.com/image.jpg',
                'content'     => ['foo' => 'bar'],
                'creation_mode' => 'Travel test mode',
                'created' => '2000-01-01T00:00:00+00:00',
                'category' => null,
                'category_ids' => [],
                'is_favorited' => false,
                'author' => [
                    'id' => 1,
                    'firstName' => 'User1',
                    'lastName' => 'Tester',
                    'picture' => 'http://example.com/user1.jpg'
                ]
            ]],
            $this->controller->getUserTravels($user)
        );
    }
}
