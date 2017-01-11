<?php
/**
 * Userspace events
 * @var \Api\Application $app
 */

use Api\Event\UserLoggedWithFacebook;
use Api\Job\UpdateAvatar;
use Api\JobQueue\JobQueueInterface;

$app->on(UserLoggedWithFacebook::NAME, function (UserLoggedWithFacebook $event) use ($app) {
    /** @var JobQueueInterface $queue */
    $queue = $app['job.queue'];
    $queue->add(new UpdateAvatar($event->getUserId(), $event->getPicUrl()));
});
