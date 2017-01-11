<?php
namespace Api\Event;

use Symfony\Component\EventDispatcher\Event;

class UserLoggedWithFacebook extends Event
{
    const NAME = 'user_logged_with_facebook';

    private $user_id;
    private $pic_url;

    public function __construct(int $user_id, string $pic_url)
    {
        $this->user_id = $user_id;
        $this->pic_url = $pic_url;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getPicUrl(): string
    {
        return $this->pic_url;
    }
}
