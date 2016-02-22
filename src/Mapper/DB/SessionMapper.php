<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use BadMethodCallException;

class SessionMapper extends AbstractPDOMapper
{
    /**
     * @param string $userId
     * @param string $token
     * @param string $device
     * @return string session id
     */
    public function createSession($userId, $token, $device)
    {
        $sql = <<<SQL
INSERT INTO
  sessions (user_id, token, device)
VALUES
  (:user_id, :token, :device)
  RETURNING id
SQL;

        $insert = $this->prepare($sql);
        $insert->execute(
            [
            ':user_id' => $userId,
            ':token' => $token,
            ':device' => $device,
            ]
        );
        return $insert->fetchColumn();
    }

    /**
     * @param $id
     * @param $token
     * @return string|null
     */
    public function getUserId($id, $token)
    {
        $select = $this->prepare('SELECT user_id FROM sessions WHERE id = :id AND token = :token');
        $select->execute(
            [
            ':id' => $id,
            ':token' => $token,
            ]
        );
        return $select->fetchColumn(0) ?: null;
    }

    /**
     * @inheritdoc
     */
    public function create(array $row)
    {
        throw new BadMethodCallException;
    }
}
