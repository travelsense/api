<?php
namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\Mapper\DB\CategoryMapper;
use Api\Model\Travel\Category;
use Psr\Log\LoggerAwareTrait;

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
     * @param string $name
     * @return array
     */
    public function getCategories(string $name = null): array
    {
        $response = [];

        if ($name == null) {
            foreach ($this->category_mapper->fetchAll() as $category) {
                $response[] = [
                    'id'    => $category->getId(),
                    'title' => $category->getName(),
                ];
            }
        } else {
            foreach ($this->category_mapper->fetchAllByName($name) as $category) {
                $response[] = [
                    'id'    => $category->getId(),
                    'title' => $category->getName(),
                ];
            }
        }
        return $response;
    }
}
