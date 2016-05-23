<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;

class FlaggedCommentMapper extends AbstractPDOMapper
{
    /**
     * @param int $userId
     * @param int $commentId
     * @return void
     */
    public function flagComment(int $userId, int $commentId)
    {
        $insert = $this->pdo->prepare('
            INSERT INTO flagged_comments 
            (comment_id, user_id)
            VALUES 
            (:comment_id, :user_id) RETURNING id
        ');
        $insert->execute([
            ':comment_id' => $commentId,
            ':user_id' => $userId,
        ]);
    }

    protected function create(array $row)
    {
        // TODO: Implement create() method.
    }
}