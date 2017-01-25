<?php
namespace Api\Mapper\DB\Travel;

use Api\DB\AbstractMapper;
use Api\Model\Travel\Category;
use Api\Persistence\Storage;

class CategoryMapper extends AbstractMapper implements Storage
{
    /**
     * @return Category[]
     */
    public function fetchAll(): array
    {
        $select = $this->connection->prepare('SELECT * FROM categories ORDER BY sort_order, id ASC');
        $select->execute();
        return $this->createAll($select);
    }

    /**
     * @param string $name
     * @return Category[]
     */
    public function fetchAllByName(string $name): array
    {
        $select = $this->connection->prepare(
            'SELECT * FROM categories WHERE name LIKE :query ORDER BY name ASC'
        );
        $select->execute([
            ':query' => "%{$name}%",
        ]);

        return $this->createAll($select);
    }

    /**
     * @param int $travel_id
     * @return Category[]
     */
    public function fetchByTravelId(int $travel_id): array
    {
        $select = $this->connection->prepare('
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
     * @param int $travel_id
     * @return int[]
     */
    public function fetchIdsByTravelId(int $travel_id): array
    {
        $select = $this->connection->prepare('
            SELECT ct.category_id FROM travel_categories ct 
            WHERE ct.travel_id = :travel_id
        ');
        $select->execute([
            'travel_id' => $travel_id,
        ]);
        return $select->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    /**
     * @param int $id
     * @return Category
     */
    public function fetchById(int $id): Category
    {
        $select = $this->connection->prepare('SELECT * FROM categories WHERE id = :id');
        $select->execute([
            'id' => $id,
        ]);
        $row = $select->fetch(\PDO::FETCH_ASSOC);
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
        $select = $this->connection->prepare(
            'SELECT name FROM categories WHERE featured ORDER BY sort_order, id ASC'
        );
        $select->execute();
        $names = [];
        while (false !== $featured = $select->fetchColumn()) {
            $names[] = $featured;
        }
        return $names;
    }

    /**
     * @param int $travel_id
     * @param array $category_ids
     */
    public function setTravelCategories(int $travel_id, array $category_ids)
    {
        try {
            $this->connection->beginTransaction();
            $this->connection
                ->prepare('DELETE FROM travel_categories WHERE travel_id=:travel_id')
                ->execute([
                    ':travel_id' => $travel_id,
                ]);

            $insert = $this->connection
                ->prepare('INSERT INTO travel_categories (travel_id, category_id) VALUES (:travel_id, :category_id)');
            foreach ($category_ids as $category_id) {
                $insert->execute([
                    ':travel_id'   => $travel_id,
                    ':category_id' => $category_id,
                ]);
            }
            $this->connection->commit();
        } catch (\PDOException $e) {
            $this->connection->rollBack();
        }
    }

    /**
     * @param array $row
     * @return Category
     */
    protected function create(array $row): Category
    {
        return Category::fromSaved($row);
    }

    public function insert(array $dto): int
    {
        $insert = $this->connection->prepare(
            'INSERT INTO categories (name) VALUES (:name) RETURNING id'
        );
        $insert->execute([
            ':name' => $dto['name'],
        ]);
        return $insert->fetchColumn();
    }

    public function update(int $id, array $dto)
    {
        // TODO: Implement update() method.
    }
}
