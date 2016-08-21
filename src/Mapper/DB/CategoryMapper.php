<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Category;
use PDO;
use PDOException;

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
     * @param string $name
     * @return Category[]
     */
    public function fetchAllByName(string $name): array
    {
        $select = $this->pdo->prepare(
            'SELECT * FROM categories WHERE name LIKE :query ORDER BY name ASC'
        );
        $select->execute([
            ':query' => '%' . $name . '%',
        ]);

        return $this->createAll($select);
    }

    /**
     * @param int $travel_id
     * @return Category[]
     */
    public function fetchByTravelId(int $travel_id): array
    {
        $select = $this->pdo->prepare('
            SELECT c.* FROM travel_categories ct 
            JOIN categories c ON ct.category_id = c.id 
            WHERE ct.travel_id = :travel_id
        ');
        $select->execute([
            'travel_id' => $travel_id,
        ]);
        return $this->createAll($select);
    }
    
    /**
     * @param int $id
     * @return Category
     */
    public function fetchById(int $id): Category
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
     * @return array
     */
    public function fetchFeaturedCategoryNames(): array
    {
        $select = $this->pdo->prepare(
            'SELECT name FROM categories WHERE featured'
        );
        $select->execute();

        $featured_category_names = [];
        while (false !== $featured = $select->fetchColumn()) {
            $featured_category_names[] = $featured;
        }
        return $featured_category_names;
    }

    /**
     * @param int $travel_id
     * @param array $category_ids
     */
    public function setTravelCategories(int $travel_id, array $category_ids)
    {
        try {
            $this->pdo->beginTransaction();
            $this->pdo
                ->prepare('DELETE FROM travel_categories WHERE travel_id=:travel_id')
                ->execute([
                    ':travel_id' => $travel_id,
                ]);

            $insert = $this->pdo
                ->prepare('INSERT INTO travel_categories (travel_id, category_id) VALUES (:travel_id, :category_id)');
            foreach ($category_ids as $category_id) {
                $insert->execute([
                    ':travel_id'   => $travel_id,
                    ':category_id' => $category_id,
                ]);
            }
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
        }
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
