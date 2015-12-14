<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/8/15
 * Time: 3:08 PM
 */

namespace Mapper\DB;


class SessionMapper extends AbstractMapper
{
    /**
     * @param string $userId
     * @param string $token
     * @param string $device
     * @return string session id
     */
    public function createSession($userId, $token, $device)
    {
        $insert = $this->pdo
            ->prepare('INSERT INTO sessions (user_id, token, device) VALUES (:user_id, :token, :device) RETURNING id');
        $insert->execute([
            ':user_id' => $userId,
            ':token' => $token,
            ':device' => $device,
        ]);
        return $insert->fetchColumn();
    }

    /**
     * @param $id
     * @param $token
     * @return string|null
     */
    public function getUserId($id, $token)
    {
        $select = $this->pdo
            ->prepare('SELECT user_id FROM sessions WHERE id = :id AND token = :token');
        $select->execute([
            ':id' => $id,
            ':token' => $token,
        ]);
        return $select->fetchColumn(0) ?: null;
    }
}