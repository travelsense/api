<?php
namespace Api\Security;

use LogicException;

class Credentials
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @return int
     */
    public function getUserId():int 
    {
        if (empty($this->userId)) {
            throw new LogicException('User id not set');
        }
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return Credentials
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
        return $this;
    }
    

}