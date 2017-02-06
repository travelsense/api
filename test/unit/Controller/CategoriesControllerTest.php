<?php
namespace Api\Controller\Travel;

use Api\Mapper\DB\Travel\CategoryMapper;
use Api\Model\User;
use Api\Test\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;

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
        $this->category_mapper = $this->createMock(CategoryMapper::class);

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
        $user = $this->createMock(User::class);

        $json = json_encode([
            'name' => 'crazy fun',
        ]);

        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn($json);

        $this->category_mapper->expects($this->once())
            ->method('insert')
            ->with([
                'name' => 'crazy fun',
            ])
            ->willReturn(1);
        ;

        $this->assertEquals(['id' => 1], $this->controller->createCategory($request, $user));
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
