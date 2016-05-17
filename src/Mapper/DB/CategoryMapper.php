<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Category;
use PDO;

class CategoryMapper extends AbstractPDOMapper
{
    /**
     * @param Category $category
     */
    public function insert(Category $category)
    {
        $insert = $this->pdo->prepare('INSERT INTO categories (name) VALUES (:name) RETURNING id');
        $insert->execute([
            ':name' => $category->getName(),
        ]);
        $category->setId($insert->fetchColumn());
    }
    
    /**
     * @return Category[]
     */
    public function fetchAll(): array
    {
        $select = $this->pdo->prepare('SELECT * FROM categories ORDER BY id ASC');
        $select->execute();
        return $this->createAll($select);
    }

    /**
     * @param int $travelId
     * @return Category[]
     */
    public function fetchByTravelId(int $travelId): array
    {
        $select = $this->pdo->prepare('SELECT c.* FROM travel_categories ct JOIN categories c ON ct.category_id = c.id WHERE ct.travel_id = :travel_id');
        $select->execute([
            'travel_id' => $travelId,
        ]);
        return $this->createAll($select);
    }
    
    /**
     * @param int $id
     * @return Category
     */
    public function fetchBylId(int $id): Category
    {
        $select = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $select->execute([
            'id' => $id,
        ]);
        $row = $select->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return null;
        }
        return $this->create($row);
    }

    /**
     * @param int $travelId
     * @param int $categoryId
     */
    public function addTravelToCategory(int $travelId, int $categoryId)
    {
        $this->pdo->prepare('
            DELETE FROM travel_categories
            WHERE travel_id=:travel_id
        ')->execute([
            ':travel_id' => $travelId,
        ]);

        $this->pdo->prepare('
            INSERT INTO travel_categories (travel_id, category_id)
            VALUES (:travel_id, :category_id)
        ')->execute([
            ':travel_id'   => $travelId,
            ':category_id' => $categoryId,
        ]);
    }

    /**
     * @param array $row
     * @return Category
     */
    protected function create(array $row): Category
    {
        $category = new Category();
        return $category
            ->setId($row['id'])
            ->setName($row['name']);
    }
}
