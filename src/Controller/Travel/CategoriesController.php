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
     * @param string $query
     * @return array
     */
    public function getCategories(string $query = ''): array
    {
        $response = [];
        foreach ($this->category_mapper->fetchAllByName($query) as $category) {
            $response[] = [
                'id'    => $category->getId(),
                'title' => $category->getName(),
            ];
        }
        return $response;
    }
}
