<?php
namespace Api\Security;

use Symfony\Component\EventDispatcher\Event;

class AuthenticationEvent extends Event
{
    const NAME = 'api.security.authentication';
    
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
