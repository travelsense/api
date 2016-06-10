<?php
namespace Api\Controller\Travel;

use Api\Test\ControllerTestCase;

class CategoriesControllerTest extends ControllerTestCase
{
    private $categoryMapper;

    /**
     * @var CategoriesController
     */
    private $controller;

    private $testCategory;

    public function setUp()
    {
        $this->categoryMapper = $this->getMockBuilder('Api\\Mapper\\DB\\CategoryMapper')
            ->setMethods(['fetchAll'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new CategoriesController(
            $this->categoryMapper
        );

        $this->testCategory = $this->buildCategory();
    }

    /**
     * getCategories
     */
    public function testGetCategories()
    {
        $this->categoryMapper->method('fetchAll')
            ->willReturn(
                [$this->testCategory]
            );

        $this->assertEquals(
            [[
                'id' => 1,
                'title' => 'test_category',
            ]],
            $this->controller->getCategories()
        );
    }

}