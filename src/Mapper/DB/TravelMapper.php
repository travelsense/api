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
    private $userMapper;

    /**
     * @var CategoryMapper
     */
    private $categoryMapper;

    /**
     * @param UserMapper $userMapper
     */
    public function setUserMapper(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * @param CategoryMapper $categoryMapper
     */
    public function setCategoryMapper($categoryMapper)
    {
        $this->categoryMapper = $categoryMapper;
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
     * @param int $authorId
     * @param int $limit
     * @param int $offset
     * @return Travel[]
     */
    public function fetchByAuthorId(int $authorId, int $limit, int $offset): array
    {
        $select = $this->pdo->prepare('
            SELECT t.*, u.* FROM travels t 
            JOIN users u ON t.author_id = u.id 
            WHERE t.author_id = :userId 
            ORDER BY t.id DESC
            LIMIT :limit OFFSET :offset
        ');

        $select->execute([
            'userId'  => $authorId,
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
            INSERT INTO travels (title, description, content, is_published, image, author_id)
            VALUES (:title, :description, :content::JSON, :published, :image, :author_id) RETURNING id
        ');
        $this->bindCommonValues($insert, $travel);
        $insert->execute();
        $travel->setId($insert->fetchColumn());
        
        if ($travel->getCategoryId()) {
            $this->categoryMapper->addTravelToCategory($travel->getId(), $travel->getCategoryId());
        }
    }

    /**
     * @param int $id
     */
    public function delete(int $id)
    {
        $deleteMain = $this->pdo->prepare("DELETE FROM travels WHERE id = :id");
        $deleteMain->execute([':id' => $id]);
    }

    /**
     * @param int $travelId
     * @param int $userId
     */
    public function addFavorite(int $travelId, int $userId)
    {
        $insert = $this->pdo->prepare('
            INSERT INTO favorite_travels (user_id, travel_id)
            VALUES (:user_id, :travel_id) ON CONFLICT DO NOTHING
        ');
        $insert->execute([
            ':user_id'   => $userId,
            ':travel_id' => $travelId,
        ]);
    }

    /**
     * @param int $travelId
     * @param int $userId
     */
    public function removeFavorite(int $travelId, int $userId)
    {
        $delete = $this->pdo->prepare('DELETE FROM favorite_travels WHERE user_id = :user_id AND travel_id = :travel_id');
        $delete->execute([
            ':user_id'   => $userId,
            ':travel_id' => $travelId,
        ]);
    }

    /**
     * @param int $userId
     * @return Travel[]
     */
    public function fetchFavorites(int $userId): array
    {
        $select = $this->pdo->prepare('
            SELECT t.*, u.* FROM  favorite_travels ft
            JOIN travels t ON ft.travel_id = t.id
            JOIN users u ON t.author_id = u.id
            WHERE ft.user_id = :user_id
            ');
        $select->execute(['user_id' => $userId]);
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
            author_id = :author_id
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
            ->setUpdated(new DateTime($row['updated']));
        $categories = $this->categoryMapper->fetchByTravelId($travel->getId());
        if (count($categories)) {
            $travel->setCategoryId($categories[0]);
        }
        return $travel;
    }

    /**
     * @param PDOStatement $statement
     * @param Travel        $travel
     */
    private function bindCommonValues(PDOStatement $statement, Travel $travel)
    {
        $statement->bindValue('title', $travel->getTitle(), PDO::PARAM_STR);
        $statement->bindValue('description', $travel->getDescription(), PDO::PARAM_STR);
        $statement->bindValue('content', json_encode($travel->getContent()), PDO::PARAM_STR);
        $statement->bindValue('published', $travel->isPublished(), PDO::PARAM_BOOL);
        $statement->bindValue('image', $travel->getImage(), PDO::PARAM_STR);
        $statement->bindValue('author_id', $travel->getAuthorId(), PDO::PARAM_INT);
    }

    /**
     * @param array $row
     * @return Travel
     */
    protected function build(array $row): Travel
    {
        list($travel, $author) = $this->createFromJoined($row, $this, $this->userMapper);
        $travel->setAuthor($author);
        return $travel;
    }
}
