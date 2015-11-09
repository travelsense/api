<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/8/15
 * Time: 3:08 PM
 */

namespace Mapper;


class SessionMapper extends AbstractMapper
{
    /**
     * @param string $userId
     * @param string $salt
     * @param string $device
     * @return string session id
     */
    public function createSession($userId, $salt, $device)
    {
        $insert = $this->pdo
            ->prepare('INSERT INTO sessions (user_id, salt, device) VALUES (:user_id, :salt, :device) RETURNING id');
        $insert->execute([
            ':user_id' => $userId,
            ':salt' => $salt,
            ':device' => $device,
        ]);
        return $insert->fetchColumn();
    }

    /**
     * @param $sessionId
     * @param $salt
     * @return string|null
     */
    public function getUserId($sessionId, $salt)
    {
        $select = $this->pdo
            ->prepare('SELECT user_id FROM sessions WHERE id = :session_id AND salt = :salt');
        $select->execute([
            ':session_id' => $sessionId,
            ':salt' => $salt,
        ]);
        return $select->fetchColumn(0) ?: null;
    }
}