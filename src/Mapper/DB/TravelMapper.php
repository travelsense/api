<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Travel;
use DateTime;
use PDO;

/**
 * Class TravelMapper
 * @package Api\Mapper\DB
 * @method Travel createFromAlias(array $row, $alias)
 */
class TravelMapper extends AbstractPDOMapper
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @param UserMapper $userMapper
     */
    public function setUserMapper(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * @param $id
     * @return Travel|null
     */
    public function fetchById($id)
    {
        $select = $this->prepare('SELECT * FROM travels t JOIN users u ON t.author_id = u.id WHERE t.id = :id');
        $select->execute(['id' => $id]);
        $row = $select->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $travel = $this->createFromAlias($row, 't');
        $author = $this->userMapper->createFromAlias($row, 'u');
        $travel->setAuthor($author);
        return $travel;
    }

    /**
     * Insert into DB, update id
     *
     * @param Travel $travel
     */
    public function insert(Travel $travel)
    {
        $insert = $this->prepare(
            'INSERT INTO travels '
            . '(title, description, content, author_id)'
            . ' VALUES '
            . '(:title, :description, :content::JSON, :author_id) RETURNING id'
        );
        $insert->execute([
            ':title'       => $travel->getTitle(),
            ':description' => $travel->getDescription(),
            ':content'     => json_encode($travel->getContent()),
            ':author_id'   => $travel->getAuthor()->getId(),
        ]);
        $id = $insert->fetchColumn();
        $travel->setId($id);
    }

    /**
     * @param array $row
     * @return Travel
     */
    protected function create(array $row)
    {
        $travel = new Travel();
        return $travel
            ->setId($row['id'])
            ->setDescription($row['description'])
            ->setTitle($row['title'])
            ->setContent(json_decode($row['content']))
            ->setCreated(new DateTime($row['created']))
            ->setUpdated(new DateTime($row['updated']));
    }

    /**
     * Update title and description in DB
     *
     * @param Travel $travel
     */
    public function update(Travel $travel)
    {
        $update = $this->prepare(
            'UPDATE travels SET '
            . 'title = :title, '
            . 'description = :description, '
            . 'content = :content::JSON '
            . 'WHERE id = :id'
        );
        $update->execute([
            ':title'       => $travel->getTitle(),
            ':description' => $travel->getDescription(),
            ':content'     => json_encode($travel->getContent()),
            ':id'          => $travel->getId(),
        ]);
    }

    public function delete($id)
    {
        $deleteMain = $this->prepare("DELETE FROM travels WHERE id = :id");
        $deleteMain->execute([':id' => $id]);
    }

    /**
     * @param int $travelId
     * @param int $userId
     */
    public function addFavorite($travelId, $userId)
    {
        $insert = $this->prepare(
            'INSERT INTO favorite_travels '
            . '(user_id, travel_id) '
            . 'VALUES '
            . '(:user_id, :travel_id)');
        $insert->execute([
            ':user_id' => $userId,
            ':travel_id' => $travelId,
        ]);
    }

    /**
     * @param int $travelId
     * @param int $userId
     */
    public function removeFavorite($travelId, $userId)
    {
        $delete = $this->prepare('DELETE FROM favorite_travels WHERE user_id = :user_id AND travel_id = :travel_id');
        $delete->execute([
            ':user_id' => $userId,
            ':travel_id' => $travelId,
        ]);
    }

    /**
     * @param int $userId
     * @return Travel[]
     */
    public function getFavorites($userId)
    {
        $select = $this->prepare(
                'SELECT t.*, u.* FROM  favorite_travels ft
                JOIN travels t ON ft.travel_id = t.id
                JOIN users u ON ft.user_id = u.id
                WHERE ft.user_id = :user_id');
        $select->execute(['user_id' => $userId]);
        $travels = [];
        while ($row = $select->fetch(PDO::FETCH_ASSOC)){
            $travel = $this->createFromAlias($row, 't');
            $author = $this->userMapper->createFromAlias($row, 'u');
            $travel->setAuthor($author);
            $travels[] = $travel;
        }
        return $travels;
    }

    /**
     * @param string $name
     * @param int $limit
     * @param int $offset
     * @return Travel[]
     */
    public function getTravelsByCategory($name, $limit, $offset)
    {
        $select = $this->prepare(
                'SELECT t.*, u.* FROM travel_category ct
                JOIN travels t ON ct.travel_id = t.id
                JOIN categories c ON ct.category_id = c.id
                JOIN users u ON u.id = t.author_id
                WHERE c.name = :name
                LIMIT :limit OFFSET :offset');
        $select->execute([
            'name' => $name,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        $travels = [];
        while($row = $select->fetch(PDO::FETCH_ASSOC)){
            $travel = $this->createFromAlias($row, 't');
            $author = $this->userMapper->createFromAlias($row, 'u');
            $travel->setAuthor($author);
            $travels[] = $travel;
        }
        return $travels;
    }
}
