<?php
namespace Api\Security;

use Api\Mapper\DB\SessionMapper;
use Symfony\Component\HttpFoundation\Request;

class SessionManager
{
    const SHA1_LENGTH = 40;

    /**
     * @var SessionMapper
     */
    private $sessionMapper;

    /**
     * SessionManager constructor.
     *
     * @param SessionMapper $sessionMapper
     */
    public function __construct(SessionMapper $sessionMapper)
    {
        $this->sessionMapper = $sessionMapper;
    }

    /**
     * Create a new session token
     *
     * @param int     $userId
     * @param Request $request
     * @return string session token
     */
    public function createSession(int $userId, Request $request)
    {
        $token = sha1(mt_rand() . $userId);
        $device = $request->headers->get('User-Agent');
        $id = $this->sessionMapper->createSession($userId, $token, $device);
        return $token . $id;
    }

    /**
     * Get user id by session token
     *
     * @param  string $token
     * @return string|false
     */
    public function getUserId(string $token)
    {
        if (strlen($token) <= self::SHA1_LENGTH) {
            return null;
        }
        list($session, $id) = str_split($token, self::SHA1_LENGTH);
        return $this->sessionMapper->getUserId($id, $session);
    }
}
