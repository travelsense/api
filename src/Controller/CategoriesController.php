<?php
namespace Api\Controller;

use Api\Mapper\DB\CategoryMapper;
use Psr\Log\LoggerAwareTrait;
use Api\Model\Category;

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
     * @param Category $category
     * @return array
     */
    private function buildCategoryView(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'title' => $category->getTitle()
        ];
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        $categories = $this->categoryMapper->getAllCategories();
        $response = [];
        foreach ($categories as $category) {
            $response[] = $this->buildCategoryView($category);
        }
        return $response;
    }
}
