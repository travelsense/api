<?php
namespace Api\Controller;


use Api\Controller\Travel\TravelController;
use Api\Model\Travel\Travel;
use Api\Test\ControllerTestCase;

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
            ->setMethods(['insert', 'fetchById', 'update', 'bindCommonValues'])
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
            'title' => 'test_travel',
            'description' => 'To make sure ids work properly',
            'published'   => true,
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
                return $travel->getTitle() === 'test_travel';
            }));

        $this->assertEquals(['id' => $travel->getId()], $this->controller->createTravel($request, $user));
    }

    /**
     * getCategories
     */
    public function testGetTravel()
    {
        $this->travel_mapper->method('fetchById')
            ->willReturn(
                [$this->test_travel]
            );

        $this->assertEquals(
            [
                'id' => 1,
                'title' => 'test_travel',
                'description' => 'To make sure ids work properly',
                'published'   => true,
                'image' => 'https://host.com/image.jpg',
                'content'     => ['foo' => 'bar'],
                'creation_mode' => 'Travel test mode'
            ],
            $this->controller->getTravel(1)
        );
    }

}