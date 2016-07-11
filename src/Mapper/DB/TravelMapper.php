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
     * @param UserMapper $user_mapper
     */
    public function setUserMapper(UserMapper $user_mapper)
    {
        $this->user_mapper = $user_mapper;
    }

    /**
     * @param CategoryMapper $category_mapper
     */
    public function setCategoryMapper($category_mapper)
    {
        $this->category_mapper = $category_mapper;
    }

    /**
     * @param int $id
     * @return Travel|null
     */
    public function fetchById(int $id)
    {
        $select = $this->pdo->prepare('SELECT t.*, u.* FROM travels t JOIN users u ON t.author_id = u.id WHERE t.id = :id');
        $select->execute(['id' => $id]);
        $row = $select->fetch(PDO::FETCH_NAMED);
        if (empty($row)) {
            return null;
        }
        return $this->build($row);
    }

    /**
     * @param int $author_id
     * @param int $limit
     * @param int $offset
     * @return Travel[]
     */
    public function fetchByAuthorId(int $author_id, int $limit, int $offset): array
    {
        $select = $this->pdo->prepare('
            SELECT t.*, u.* FROM travels t 
            JOIN users u ON t.author_id = u.id 
            WHERE t.author_id = :userId 
            ORDER BY t.id DESC
            LIMIT :limit OFFSET :offset
        ');

        $select->execute([
            'userId'  => $author_id,
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);
        return $this->buildAll($select);
    }

    /**
     * Insert into DB, update id
     *
     * @param Travel $travel
     */
    public function insert(Travel $travel)
    {
        $insert = $this->pdo->prepare('
            INSERT INTO travels (title, description, content, is_published, image, author_id, creation_mode)
            VALUES (:title, :description, :content::JSON, :published, :image, :author_id, :creation_mode) RETURNING id, created
        ');
        $this->bindCommonValues($insert, $travel);
        $insert->execute();
        $row = $insert->fetch(PDO::FETCH_ASSOC);
        $travel->setId($row['id']);
        $travel->setCreated(new DateTime($row['created']));

        if ($travel->getCategoryIds()) {
            $this->category_mapper->addTravelToCategories($travel->getId(), $travel->getCategoryIds());
        }
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
     * @param int $travel_id
     * @param int $user_id
     */
    public function addFavorite(int $travel_id, int $user_id)
    {
        $this->pdo->prepare('
            INSERT INTO favorite_travels (user_id, travel_id)
            VALUES (:user_id, :travel_id) ON CONFLICT DO NOTHING
        ')->execute([
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
        $select = $this->pdo->prepare('
            SELECT t.*, u.* FROM  favorite_travels ft
            JOIN travels t ON ft.travel_id = t.id
            JOIN users u ON t.author_id = u.id
            WHERE ft.user_id = :user_id
            ');
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
        $select = $this->pdo->prepare('
            SELECT t.*, u.* FROM travel_categories ct
            JOIN travels t ON ct.travel_id = t.id
            JOIN categories c ON ct.category_id = c.id
            JOIN users u ON u.id = t.author_id
            WHERE c.name = :name
            LIMIT :limit OFFSET :offset
        ');
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
        $select = $this->pdo->prepare('
            SELECT t.*, u.* FROM travel_categories ct
            JOIN travels t ON ct.travel_id = t.id
            JOIN categories c ON ct.category_id = c.id
            JOIN users u ON u.id = t.author_id
            WHERE c.name = :name AND is_published
            LIMIT :limit OFFSET :offset
        ');
        $select->execute([
            'name'    => $name,
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);
        return $this->buildAll($select);
    }

    /**
     * Update title and description in DB
     *
     * @param Travel $travel
     */
    public function update(Travel $travel)
    {
        $update = $this->pdo->prepare('
            UPDATE travels SET
            title = :title,
            description = :description,
            content = :content::JSON,
            is_published = :published,
            image = :image,
            author_id = :author_id,
            creation_mode = :creation_mode
            WHERE id = :id
        ');

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
            $travel->setCategoryIds($categories);
        }
        return $travel;
    }

    /**
     * @param PDOStatement $statement
     * @param Travel $travel
     */
    private function bindCommonValues(PDOStatement $statement, Travel $travel)
    {
        $statement->bindValue('title', $travel->getTitle(), PDO::PARAM_STR);
        $statement->bindValue('description', $travel->getDescription(), PDO::PARAM_STR);
        $statement->bindValue('content', json_encode($travel->getContent()), PDO::PARAM_STR);
        $statement->bindValue('published', $travel->isPublished(), PDO::PARAM_BOOL);
        $statement->bindValue('image', $travel->getImage(), PDO::PARAM_STR);
        $statement->bindValue('author_id', $travel->getAuthorId(), PDO::PARAM_INT);
        $statement->bindValue('creation_mode', $travel->getCreationMode(), PDO::PARAM_STR);
    }

    /**
     * @param array $row
     * @return Travel
     */
    protected function build(array $row): Travel
    {
        list($travel, $author) = $this->createFromJoined($row, $this, $this->user_mapper);
        $travel->setAuthor($author);
        return $travel;
    }
}
