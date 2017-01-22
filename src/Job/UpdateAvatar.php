<?php
namespace Api\Job;

use Api\JobQueue\GenericJob;

class UpdateAvatar extends GenericJob
{
    const NAME = __CLASS__;

    public function __construct(int $user_id, string $avatar_url)
    {
        parent::__construct(self::NAME, [$user_id, $avatar_url]);
    }
}
