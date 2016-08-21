<?php
namespace Api\Controller\Travel;

use Api\Mapper\DB\CategoryMapper;
use Api\Model\Travel\Category;
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
        $this->category_mapper = $this->getMockBuilder(CategoryMapper::class)
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
        $user = $this->buildUser();

        $json = json_encode([
            'name' => 'crazy fun',
        ]);

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getContent'])
            ->getMock();

        $request->method('getContent')->willReturn($json);

        $category = $this->getMockBuilder(Category::class)
            ->setMethods(['getId', 'getName'])
            ->getMock();

        $this->category_mapper->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (Category $cat) {
                return $cat->getName() === 'crazy fun';
            }));

        $this->assertEquals(['id' => $category->getId()], $this->controller->createCategory($request, $user));
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
            [
                [
                    'id'    => 1,
                    'title' => 'test_category',
                ],
            ],
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
            [
                [
                    'id'    => 1,
                    'title' => 'test_category',
                ],
            ],
            $this->controller->getCategories('te')
        );
    }
}
