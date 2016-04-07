<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Comment;
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
        $insert = $this->pdo->prepare(
            'INSERT INTO travel_comments '
            . '(author_id, travel_id, text)'
            . ' VALUES '
            . '(:author_id, :travel_id, :text) RETURNING id'
        );
        $insert->execute([
            ':author_id' => $comment->getAuthorId(),
            ':travel_id' => $comment->getTravelId(),
            ':text' => $comment->getText(),
        ]);
        $id = $insert->fetchColumn();
        $comment->setId($id);
    }

    /**
     * @param array $row
     * @return Comment
     */
    public function create(array $row) {
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
