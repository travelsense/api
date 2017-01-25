<?php
namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\JSON\DataObject;
use Api\Mapper\DB\Travel\CategoryMapper;
use Api\Model\Travel\Category;
use Api\Model\User;
use Symfony\Component\HttpFoundation\Request;

class CategoriesController extends ApiController
{
    /**
     * @var CategoryMapper
     */
    private $category_mapper;

    /**
     * CategoriesController constructor.
     * @param CategoryMapper $category_mapper
     */
    public function __construct(CategoryMapper $category_mapper)
    {
        $this->category_mapper = $category_mapper;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function createCategory(Request $request, User $user) : array
    {
        $json = DataObject::createFromString($request->getContent());

        $category = new Category($json->getString('name'));
        $category->saveTo($this->category_mapper);
        return ['id' => $category->getId()];
    }

    /**
     * @param string $name
     * @return array
     */
    public function getCategories(string $name = null): array
    {
        if ($name === null) {
            $categories = $this->category_mapper->fetchAll();
        } else {
            $categories = $this->category_mapper->fetchAllByName($name);
        }
        $response = [];
        foreach ($categories as $category) {
            $response[] = [
                'id'    => $category->getId(),
                'title' => $category->getName(),
            ];
        }
        return $response;
    }
}
