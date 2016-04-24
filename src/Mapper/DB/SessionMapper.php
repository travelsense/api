<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use BadMethodCallException;

class SessionMapper extends AbstractPDOMapper
{
    /**
     * @param int    $userId
     * @param string $token
     * @param string $device
     * @return int session id
     */
    public function createSession(int $userId, string $token, string $device = null): int
    {
        $sql = 'INSERT INTO sessions (user_id, token, device) VALUES (:user_id, :token, :device) RETURNING id';
        $insert = $this->pdo->prepare($sql);
        $insert->execute(
            [
                ':user_id' => $userId,
                ':token'   => $token,
                ':device'  => $device,
            ]
        );
        return $insert->fetchColumn();
    }

    /**
     * @param int    $id
     * @param string $token
     * @return int|false
     */
    public function getUserId(int $id, string $token)
    {
        $select = $this->pdo->prepare('SELECT user_id FROM sessions WHERE id = :id AND token = :token');
        $select->execute([
            ':id'    => $id,
            ':token' => $token,
        ]);
        return $select->fetchColumn(0) ?: null;
    }

    /**
     * @inheritdoc
     */
    protected function create(array $row)
    {
        throw new BadMethodCallException;
    }
}
