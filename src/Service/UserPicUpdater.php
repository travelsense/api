<?php
namespace Api\Service;

use Api\Mapper\DB\UserMapper;

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
     * @param int    $user_id
     * @param string $pic_url
     */
    public function updateUserPic(int $user_id, string $pic_url)
    {
        $this->user_mapper->updatePic($user_id, $this->image_copier->copyFrom($pic_url));
    }
}
