<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Api\Application();
$app['stats_service']->buildStats();
$app['stats_service']->sendEmail(new DateTime());
