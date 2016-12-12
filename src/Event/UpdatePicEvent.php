<?php
namespace Api\Event;

use Symfony\Component\EventDispatcher\Event;

class UpdatePicEvent extends Event
{
    const UPDATE_USER_PIC = 'pic_update';

    private $user_id;
    private $pic_url;

    public function __construct(int $user_id, string $pic_url)
    {
        $this->user_id = $user_id;
        $this->pic_url = $pic_url;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getPicUrl()
    {
        return $this->pic_url;
    }
}