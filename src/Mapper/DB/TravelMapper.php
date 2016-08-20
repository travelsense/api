<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Travel;
use DateTime;
use PDO;
use PDOStatement;

/**
 * Class TravelMapper
 * @package Api\Mapper\DB
 */
class TravelMapper extends AbstractPDOMapper
{
    /**
     * @var UserMapper
     */
    private $user_mapper;

    /**
     * @var CategoryMapper
     */
    private $category_mapper;

    /**
     * @var ActionMapper
     */
    private $action_mapper;

    /**
     * @param UserMapper $user_mapper
     */
    public function setUserMapper(UserMapper $user_mapper)
    {
        $this->user_mapper = $user_mapper;
    }

    /**
     * @param CategoryMapper $category_mapper
     */
    public function setCategoryMapper(CategoryMapper $category_mapper)
    {
        $this->category_mapper = $category_mapper;
    }

    /**
     * @param ActionMapper $action_mapper
     */
    public function setActionMapper(ActionMapper $action_mapper)
    {
        $this->action_mapper = $action_mapper;
    }

    /**
     * @param int $id
     * @return Travel|null
     */
    public function fetchById(int $id)
    {
        $select = $this->pdo->prepare(
            'SELECT t.*, u.* FROM travels t JOIN users u ON t.author_id = u.id WHERE t.id = :id AND NOT deleted'
        );
        $select->execute(['id' => $id]);
        $row = $select->fetch(PDO::FETCH_NAMED);
        if (empty($row)) {
            return null;
        }
        $travel = $this->build($row);
        $travel->setActions($this->action_mapper->fetchActionsForTravel($id));
        return $travel;
    }

    /**
     * @param int  $author_id
     * @param int  $limit
     * @param int  $offset
     * @param bool $is_published
     * @return Travel[]
     */
    public function fetchByAuthorId(int $author_id, int $limit, int $offset, bool $is_published = null): array
    {
        $select = $this->pdo->prepare(
            'SELECT t.*, u.* FROM travels t
             JOIN users u ON t.author_id = u.id
             WHERE t.author_id = :userId AND NOT deleted '
            . ($is_published !== null ? 'AND is_published = :is_published ' : '')
            . 'ORDER BY t.id DESC LIMIT :limit OFFSET :offset'
        );

        $params = [
            'userId'  => $author_id,
            ':limit'  => $limit,
            ':offset' => $offset,
        ];
        if ($is_published !== null) {
            $params['is_published'] = $is_published;
        }
        $select->execute($params);
        return $this->buildAll($select);
    }

    /**
     * Fetch published travels of the given author
     * @param int $author_id
     * @param int $limit
     * @param int $offset
     * @return Travel[]
     */
    public function fetchPublishedByAuthorId(int $author_id, int $limit, int $offset): array
    {
        $is_published = true;
        return $this->fetchByAuthorId($author_id, $limit, $offset, $is_published);
    }

    /**
     * Insert into DB, update id
     *
     * @param Travel $travel
     */
    public function insert(Travel $travel)
    {
        $insert = $this->pdo->prepare(
            'INSERT INTO travels (title, description, content, is_published, image, author_id, creation_mode)
            VALUES (:title, :description, :content::JSON, :published, :image, :author_id, :creation_mode) 
            RETURNING id, created'
        );
        $this->bindCommonValues($insert, $travel);
        $insert->execute();
        $row = $insert->fetch(PDO::FETCH_ASSOC);
        $travel->setId($row['id']);
        $travel->setCreated(new DateTime($row['created']));

        $this->category_mapper->setTravelCategories($travel->getId(), $travel->getCategoryIds());
    }

    /**
     * @param int $id
     */
    public function delete(int $id)
    {
        $this->pdo
            ->prepare("DELETE FROM travels WHERE id = :id")
            ->execute([':id' => $id]);
    }
    /**
     * @param int $user_id
     * @return int[]
     */
    public function fetchFavoriteIds(int $user_id): array
    {
        $select = $this->pdo->prepare('
            SELECT travel_id FROM  favorite_travels
            WHERE user_id = :user_id
            ');
        $select->execute([
            ':user_id' => $user_id,
        ]);

        $ids = [];
        while (false !== $id = $select->fetchColumn()) {
            $ids[$id] = $id;
        }
        return $ids;
    }

    /**
     * @param int $travel_id
     * @param int $user_id
     */
    public function addFavorite(int $travel_id, int $user_id)
    {
        $this->pdo->prepare(
            'INSERT INTO favorite_travels (user_id, travel_id)
            VALUES (:user_id, :travel_id) ON CONFLICT DO NOTHING'
        )->execute([
            ':user_id' => $user_id,
            ':travel_id' => $travel_id,
        ]);
    }

    /**
     * @param int $travel_id
     * @param int $user_id
     */
    public function removeFavorite(int $travel_id, int $user_id)
    {
        $this->pdo
            ->prepare('DELETE FROM favorite_travels WHERE user_id = :user_id AND travel_id = :travel_id')
            ->execute([
                ':user_id' => $user_id,
                ':travel_id' => $travel_id,
            ]);
    }

    /**
     * @param int $user_id
     * @return Travel[]
     */
    public function fetchFavorites(int $user_id): array
    {
        $select = $this->pdo->prepare(
            'SELECT t.*, u.* FROM  favorite_travels ft
            JOIN travels t ON ft.travel_id = t.id AND NOT t.deleted
            JOIN users u ON t.author_id = u.id
            WHERE ft.user_id = :user_id'
        );
        $select->execute(['user_id' => $user_id]);
        return $this->buildAll($select);
    }

    /**
     * @param string $name
     * @param int    $limit
     * @param int    $offset
     * @return Travel[]
     */
    public function fetchByCategory(string $name, int $limit, int $offset): array
    {
        $select = $this->pdo->prepare(
            'SELECT t.*, u.* FROM travel_categories ct
            JOIN travels t ON ct.travel_id = t.id AND NOT t.deleted
            JOIN categories c ON ct.category_id = c.id
            JOIN users u ON u.id = t.author_id
            WHERE c.name = :name
            LIMIT :limit OFFSET :offset'
        );
        $select->execute([
            'name'    => $name,
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);
        return $this->buildAll($select);
    }

    /**
     * @param string $name
     * @param int    $limit
     * @param int    $offset
     * @return Travel[]
     */
    public function fetchPublishedByCategory(string $name, int $limit, int $offset): array
    {
        $select = $this->pdo->prepare(
            'SELECT t.*, u.* FROM travel_categories ct
            JOIN travels t ON ct.travel_id = t.id AND NOT t.deleted
            JOIN categories c ON ct.category_id = c.id
            JOIN users u ON u.id = t.author_id
            WHERE c.name = :name AND is_published
            LIMIT :limit OFFSET :offset'
        );
        $select->execute([
            'name'    => $name,
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);
        return $this->buildAll($select);
    }

    /**
     * @return array[]
     */
    public function fetchBanners() : array
    {
        $select = $this->pdo->prepare('SELECT title, subtitle, image, category FROM banners');
        $select->execute();

        return $select->fetchAll(PDO::FETCH_NAMED);
    }

    /**
     * Update title and description in DB
     *
     * @param Travel $travel
     */
    public function update(Travel $travel)
    {
        $update = $this->pdo->prepare(
            'UPDATE travels SET
            title = :title,
            description = :description,
            content = :content::JSON,
            is_published = :published,
            image = :image,
            author_id = :author_id,
            creation_mode = :creation_mode
            WHERE id = :id'
        );
        $this->bindCommonValues($update, $travel);
        $update->bindValue('id', $travel->getId(), PDO::PARAM_INT);
        $update->execute();
    }

    /**
     * @param array $row
     * @return Travel
     */
    protected function create(array $row)
    {
        $travel = new Travel();
        $travel
            ->setId($row['id'])
            ->setDescription($row['description'])
            ->setTitle($row['title'])
            ->setContent(json_decode($row['content']))
            ->setPublished($row['is_published'])
            ->setImage($row['image'])
            ->setCreated(new DateTime($row['created']))
            ->setUpdated(new DateTime($row['updated']))
            ->setCreationMode($row['creation_mode']);
        $categories = $this->category_mapper->fetchByTravelId($travel->getId());
        if (count($categories)) {
            foreach ($categories as $category) {
                $category_ids[] = $category->getId();
            }
            $travel->setCategoryIds($category_ids);
        }
        return $travel;
    }

    /**
     * @param PDOStatement $statement
     * @param Travel $travel
     */
    private function bindCommonValues(PDOStatement $statement, Travel $travel)
    {
        $values = [
            'title' => $travel->getTitle(),
            'description' => $travel->getDescription(),
            'content' => json_encode($travel->getContent()),
            'published' => $travel->isPublished(),
            'image' => $travel->getImage(),
            'author_id' => $travel->getAuthorId(),
            'creation_mode' => $travel->getCreationMode()
        ];
        $this->bindValues($statement, $values);
    }

    /**
     * @param array $row
     * @return Travel
     */
    protected function build(array $row): Travel
    {
        list($travel, $author) = $this->createFromJoined($row, $this, $this->user_mapper);
        $travel->setAuthor($author);
        $travel->setActions($this->action_mapper->fetchActionsForTravel($travel->getId()));
        return $travel;
    }

    public function markDeleted(int $travelId, bool $deleted = true)
    {
        $update = $this->pdo->prepare(
            'UPDATE travels SET
              deleted = :deleted
            WHERE id = :id'
        );
        $values = [
            'id' => $travelId,
            'deleted' => $deleted
        ];
        $this->bindValues($update, $values);
        $update->execute();
    }
}
