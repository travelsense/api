<?php
namespace Mapper\DB;

use AbstractPDOMapper;
use Model\TravelComment;
use PDO;

class TravelCommentMapper extends AbstractPDOMapper
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @param UserMapper $userMapper
     */
    public function setUserMapper($userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * @param $id
     * @param int $limit
     * @param int $offset
     * @return \Model\TravelComment[]
     */
    public function fetchByTravelId($id, $limit, $offset = 0)
    {
        $select = $this->prepare('SELECT * FROM travel_comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.id = :id LIMIT :lim OFFSET :off');
        $select->execute([
            'id' => $id,
            'lim' => $limit,
            'off' => $offset,
        ]);
        $comments = [];
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            /** @var TravelComment $comment */
            $comment = $this->createJoined($row, 'c.');
            $comment->setAuthor($this->userMapper->createJoined($row, 'u.'));
            $comments[] = $comment;
        }
        return $comments;
    }

    /**
     * @inheritdoc
     */
    public function create(array $row)
    {
        $comment = new TravelComment();
        return $comment
            ->setId($row['id'])
            ->setText($row['text'])
            ;
    }
}