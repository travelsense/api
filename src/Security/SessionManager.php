<?php
namespace Security;

use Mapper\SessionMapper;

class SessionManager
{
    /**
     * @var SessionMapper
     */
    private $sessionMapper;

    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * SessionManager constructor.
     * @param SessionMapper $sessionMapper
     * @param TokenManager $tokenManager
     */
    public function __construct(SessionMapper $sessionMapper, TokenManager $tokenManager)
    {
        $this->sessionMapper = $sessionMapper;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Create a new session token
     * @param string  $userId
     * @param string $device
     * @return string session token
     */
    public function createSession($userId, $device)
    {
        $salt = mt_rand();
        $id = $this->sessionMapper->createSession($userId, $salt, $device);
        return $this->tokenManager->encrypt("$id.$salt");
    }

    /**
     * Get user id by session token
     * @param string $token
     * @return string|null
     */
    public function getUserId($token)
    {
        $decryptedToken = $this->tokenManager->decrypt($token);
        list($id, $salt) = explode('.', $decryptedToken);
        return $this->sessionMapper->getUserId($id, $salt);
    }
}