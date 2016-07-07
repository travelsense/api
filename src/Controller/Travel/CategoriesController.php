<?php
namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\Mapper\DB\CategoryMapper;
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
     * @return array
     */
    public function getCategories(): array
    {
        $response = [];
        foreach ($this->category_mapper->fetchAll() as $category) {
            $response[] = [
                'id'    => $category->getId(),
                'title' => $category->getName(),
            ];
        }
        return $response;
    }
}
