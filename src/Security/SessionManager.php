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
    private $session_mapper;

    /**
     * SessionManager constructor.
     *
     * @param SessionMapper $session_mapper
     */
    public function __construct(SessionMapper $session_mapper)
    {
        $this->session_mapper = $session_mapper;
    }

    /**
     * Create a new session token
     *
     * @param int     $user_id
     * @param Request $request
     * @return string session token
     */
    public function createSession(int $user_id, Request $request)
    {
        $token = sha1(mt_rand() . $user_id);
        $device = $request->headers->get('User-Agent');
        $id = $this->session_mapper->createSession($user_id, $token, $device);
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
        return $this->session_mapper->getUserId($id, $session);
    }
}
