<?php
namespace Api\Security;

use LogicException;

class Credentials
{
    /**
     * @var int
     */
    private $user_id;

    /**
     * @return int
     */
    public function getUserId():int
    {
        if (empty($this->user_id)) {
            throw new LogicException('User id not set');
        }
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return Credentials
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }
}
