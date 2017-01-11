<?php
/**
 * Userspace events
 * @var \Api\Application $app
 */

use Api\Job\UpdateAvatar;
use Api\JobQueue\BernardJobQueue;
use Api\JobQueue\JobProcessor;
use Api\JobQueue\JobProcessorInterface;
use Api\JobQueue\JobQueueInterface;
use Bernard\Driver\FlatFileDriver;
use Bernard\Queue\PersistentQueue;
use Bernard\Serializer;

$app['job.queue'] = function ($app): JobQueueInterface {
    return new BernardJobQueue(
        new PersistentQueue(
            'api_job_queue',
            new FlatFileDriver($app['config']['jobs']['event_storage_dir']),
            new Serializer()
        )
    );
};

$app['job.processor'] = function ($app): JobProcessorInterface {
    return new JobProcessor([
        UpdateAvatar::NAME => function(int $user_id, string $url) use ($app) {
            $app['mapper.db.user']->updatePic($user_id, $app['image_copier']->copyFrom($url));
        }
    ]);
};

$app['job.front_controller']= function ($app) {
    return new \Api\JobQueue\FrontController(
        $app['job.queue'],
        $app['job.processor'],
        new \F3\Flock\Lock($app['config']['jobs']['cron_lock'])

    );

};
