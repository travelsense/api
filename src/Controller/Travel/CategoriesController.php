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
    private $categoryMapper;

    /**
     * CategoriesController constructor.
     * @param CategoryMapper $categoryMapper
     */
    public function __construct(CategoryMapper $categoryMapper)
    {
        $this->categoryMapper = $categoryMapper;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        $response = [];
        foreach ($this->categoryMapper->fetchAll() as $category) {
            $response[] = [
                'id'    => $category->getId(),
                'title' => $category->getName(),
            ];
        }
        return $response;
    }
}
