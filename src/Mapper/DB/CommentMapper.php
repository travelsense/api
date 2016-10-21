<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Comment;
use Api\Model\User;
use DateTime;
use PDO;

/**
 * Class CommentMapper
 * @package Api\Mapper\DB
 */
class CommentMapper extends AbstractPDOMapper
{
    /**
     * @var UserMapper
     */
    private $user_mapper;

    /**
     * @param UserMapper $user_mapper
     */
    public function setUserMapper(UserMapper $user_mapper)
    {
        $this->user_mapper = $user_mapper;
    }

    /**
     * Insert into DB travel comment
     *
     * @param Comment $comment
     */
    public function insert(Comment $comment)
    {
        $insert = $this->pdo->prepare('
            INSERT INTO travel_comments 
            (author_id, travel_id, text)
            VALUES 
            (:author_id, :travel_id, :text) RETURNING id
        ');
        $insert->execute([
            ':author_id' => $comment->getAuthorId(),
            ':travel_id' => $comment->getTravelId(),
            ':text'      => $comment->getText(),
        ]);
        $id = $insert->fetchColumn();
        $comment->setId($id);
    }

    /**
     * Get by id
     * @param int $id
     * @return Comment|null
     */
    public function fetchById(int $id)
    {
        $select = $this->pdo->prepare('
            SELECT c.*, u.* FROM travel_comments c 
            JOIN users u ON u.id = c.author_id
            WHERE c.id = :id 
        ');
        $select->execute([
            ':id' => $id,
        ]);

        $row = $select->fetch(PDO::FETCH_NAMED);
        if (empty($row)) {
            return null;
        }
        return $this->build($row);
    }

    /**
     * Get all comments by travel id
     * @param int $travel_id
     * @param int $limit
     * @param int $offset
     * @return Comment[]
     */
    public function fetchByTravelId(int $travel_id, int $limit, int $offset): array
    {
        $select = $this->pdo->prepare('
            SELECT c.*, u.* FROM travel_comments c 
            JOIN users u ON u.id = c.author_id
            WHERE c.travel_id = :id 
            ORDER BY c.id DESC LIMIT :limit OFFSET :offset
        ');
        $select->execute([
            ':id'     => $travel_id,
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);
        return $this->buildAll($select);
    }

    /**
     * @param int $id
     */
    public function delete(int $id)
    {
        $this->pdo
            ->prepare('DELETE FROM travel_comments WHERE id=:id')
            ->execute([':id' => $id]);
    }

    /**
     * @param array $row
     * @return Comment
     */
    protected function create(array $row): Comment
    {
        $comment = new Comment();
        return $comment
            ->setId($row['id'])
            ->setAuthorId($row['author_id'])
            ->setTravelId($row['travel_id'])
            ->setText($row['text'])
            ->setCreated(new DateTime($row['created']))
            ->setUpdated(new DateTime($row['updated']));
    }

    protected function build(array $row)
    {
        /** @var Comment $comment */
        /** @var User $author */
        list($comment, $author) = $this->createFromJoined($row, $this, $this->user_mapper);
        $comment->setAuthor($author);
        return $comment;
    }
}
