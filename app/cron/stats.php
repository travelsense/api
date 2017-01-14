<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Api\Application();
/** @var \Api\Service\StatService $statService */
$statService = $app['stats_service'];
$now = new DateTime();
$statService->buildStats($now);
$statService->sendEmail($now);
