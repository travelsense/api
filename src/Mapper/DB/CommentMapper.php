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
    private $userMapper;

    /**
     * @param UserMapper $userMapper
     */
    public function setUserMapper(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
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
     * Get all comments by travel id
     * @param int $travelId
     * @param int $limit
     * @param int $offset
     * @return Comment[]
     */
    public function fetchByTravelId(int $travelId, int $limit, int $offset): array
    {
        $select = $this->pdo->prepare('
            SELECT c.*, u.* FROM travel_comments c 
            JOIN users u ON u.id = c.author_id
            WHERE c.travel_id = :id 
            ORDER BY c.id DESC LIMIT :limit OFFSET :offset
        ');
        $select->execute([
            ':id'     => $travelId,
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);

        $comments = [];
        while ($row = $select->fetch(PDO::FETCH_NAMED)) {
            /** @var Comment $comment */
            /** @var User $author */
            list($comment, $author) = $this->createFromJoined($row, $this, $this->userMapper);
            $comment->setAuthor($author);
            $comments[] = $comment;
        }
        return $comments;
    }
    
    public function delete(int $id)
    {
        // TODO implement
    }

    /**
     * @param array $row
     * @return Comment
     */
    public function create(array $row): Comment
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
}
