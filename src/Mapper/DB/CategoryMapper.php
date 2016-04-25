<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Category;
use PDO;

class CategoryMapper extends AbstractPDOMapper
{
    /**
     * @return Category[]
     */
    public function fetchAll(): array
    {
        $select = $this->pdo->prepare('SELECT * FROM categories ORDER BY id ASC');
        $select->execute();
        $categories = [];
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $this->create($row);
        }
        return $categories;
    }

    /**
     * @param array $row
     * @return Category
     */
    public function create(array $row): Category
    {
        $category = new Category();
        return $category
            ->setId($row['id'])
            ->setTitle($row['name']);
    }


    /**
     * @param int|string $travelId
     * @return Category[]
     */
    public function fetchByTravelId(int $travelId): array
    {
        $select = $this->pdo->prepare('SELECT c.* FROM travel_categories ct JOIN categories c ON ct.category_id = c.id WHERE ct.travel_id = :travel_id');
        $select->execute([
            'travel_id' => $travelId,
        ]);
        $categories = [];
        while ($row = $select->fetch(PDO::FETCH_NAMED)) {
            $categories[] = $this->create($row);
        }
        return $categories;
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

}
