<?php
namespace Api\Mapper\DB;

use Api\DB\AbstractMapper;

class FlaggedCommentMapper extends AbstractMapper
{
    /**
     * @param int $user_id
     * @param int $comment_id
     * @return void
     */
    public function flagComment(int $user_id, int $comment_id)
    {
        $insert = $this->conn->prepare('
            INSERT INTO flagged_comments 
            (comment_id, user_id)
            VALUES 
            (:comment_id, :user_id)
            ON CONFLICT (comment_id, user_id) DO NOTHING
        ');
        $insert->execute([
            ':comment_id' => $comment_id,
            ':user_id' => $user_id,
        ]);
    }
}
