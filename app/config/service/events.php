<?php
/**
 * Userspace events
 * @var \Api\Application $app
 */

use Api\Event\UpdatePicEvent;

$app->on(UpdatePicEvent::UPDATE_USER_PIC, function (UpdatePicEvent $event) use ($app) {
    $app['user_pic_updater']->updateUserPic($event);
});
