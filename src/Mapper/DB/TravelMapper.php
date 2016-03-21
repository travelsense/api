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
            . '(title, description, author_id)'
            . ' VALUES '
            . '(:title, :description, :author_id) RETURNING id');
        $insert->execute([
            ':title'       => $travel->getTitle(),
            ':description' => $travel->getDescription(),
            ':author_id'   => $travel->getAuthor()->getId(),
        ]);
        $id = $insert->fetchColumn();
        $travel->setId($id);
    }

    protected function create(array $row)
    {
        $travel = new Travel();
        return $travel
            ->setId($row['id'])
            ->setDescription($row['description'])
            ->setTitle($row['title'])
            ->setCreated(new DateTime($row['created']))
            ->setUpdated(new DateTime($row['updated']));
    }

    /**
     * @param int $travelId
     * @param int $userId
     */
    public function addFavorite($travelId, $userId)
    {
        $add = $this->prepare(
            'INSERT INTO favorite_travels '
            . '(user_id, travel_id) '
            . 'VALUES '
            . '(:user_id, :travel_id)');
        $add->execute([
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
        $remove = $this->prepare('DELETE FROM favorite_travels WHERE user_id = :user_id AND travel_id = :travel_id');
        $remove->execute([
            ':user_id' => $userId,
            ':travel_id' => $travelId,
        ]);
    }

    /**
     * @param $userId
     * @return Travel|null
     */
    public function getFavorite($userId)
    {
        $get = $this->prepare(
                'SELECT t.*, u.* FROM  favorite_travels ft
                JOIN travels t ON ft.travel_id = t.id
                JOIN users u ON ft.user_id = u.id
                WHERE ft.user_id = :user_id');
        $get->execute(['user_id' => $userId]);
//        $rows = $get->fetchAll(PDO::FETCH_ASSOC);
//        return $get->fetchAll(PDO::FETCH_OBJ);
        $rows = array();
        while ($row = $get->fetchObject()){
            $rows[] = $row;
        }
        $travels = array();
        foreach ($rows as $row) {
            $travel = $this->createFromAlias($row, 't');
            $author = $this->userMapper->createFromAlias($row, 'u');
            $travels[] = $travel->setAuthor($author);
        }
        return $travels;
    }
}
