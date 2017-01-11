<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Api\Application();
$app['job.front_controller']->run();
