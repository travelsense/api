<?php
namespace Api\Controller;

use Api\Mapper\DB\CategoryMapper;
use Psr\Log\LoggerAwareTrait;

class CategoriesController extends ApiController
{
    use LoggerAwareTrait;

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
        foreach ($this->categoryMapper->getAllCategories() as $category) {
            $response[] = [
                'id'    => $category->getId(),
                'title' => $category->getTitle(),
            ];
        }
        return $response;
    }
}
