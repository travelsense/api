<?php
namespace Api\Service;

use Api\Event\UpdatePicEvent;
use Api\Mapper\DB\UserMapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserPicUpdater
{
    /**
     * @var UserMapper
     */
    private $user_mapper;

    /**
     * @var ImageCopier
     */
    private $image_copier;

    /**
     * UserPicUpdater constructor.
     * @param UserMapper  $user_mapper
     * @param ImageCopier $image_copier
     */
    public function __construct(
        UserMapper $user_mapper,
        ImageCopier $image_copier
    ) {
        $this->user_mapper = $user_mapper;
        $this->image_copier = $image_copier;
    }

    /**
     * Update the user avatar.
     *
     * @param UpdatePicEvent $event
     */
    public function updateUserPic(UpdatePicEvent $event)
    {
        $this->user_mapper->updatePic($event->getUserId(), $this->image_copier->copyFrom($event->getPicUrl()));
    }
}
