<?php
namespace Api\Controller\Travel;

use Api\Model\Travel\Category;
use Api\Test\ControllerTestCase;

class CategoriesControllerTest extends ControllerTestCase
{
    private $category_mapper;

    /**
     * @var CategoriesController
     */
    private $controller;

    private $test_category;

    public function setUp()
    {
        $this->category_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\CategoryMapper')
            ->setMethods(['insert', 'fetchAll', 'fetchAllByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new CategoriesController(
            $this->category_mapper
        );

        $this->test_category = $this->buildCategory();
    }

    /**
     * create Category
     */
    public function testCreateCategory()
    {
        $json = json_encode([
            'name' => 'crazy fun',
        ]);

        $request = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Request')
            ->setMethods(['getContent'])
            ->getMock();

        $request->method('getContent')->willReturn($json);

        $this->category_mapper->method('insert')
            ->with($this->callback(function (Category $cat) {
                return $cat->getName() === 'crazy fun';
            }));
    }

    /**
     * getCategories
     */
    public function testGetCategories()
    {
        $this->category_mapper->method('fetchAll')
            ->willReturn(
                [$this->test_category]
            );

        $this->assertEquals(
            [[
                'id' => 1,
                'title' => 'test_category',
            ]],
            $this->controller->getCategories()
        );
    }

    /**
     * getCategoriesByName
     */
    public function testGetCategoriesByName()
    {
        $this->category_mapper->method('fetchAllByName')
            ->with($this->equalTo('te'))
            ->willReturn([$this->test_category]);

        $this->assertEquals(
            [[
                'id' => 1,
                'title' => 'test_category',
            ]],
            $this->controller->getCategories('te')
        );
    }
}
