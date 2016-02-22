<?php
namespace Api\Security\Authentication;

use LogicException;

class Credentials
{
    /**
     * @var string
     */
    private $user = null;

    /**
     * @return mixed
     */
    public function getUser()
    {
        if (null === $this->user) {
            throw new LogicException('No credentials available');
        }
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
