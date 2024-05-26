<?php

require_once __DIR__ . '/../../../lib/base.php';

$app = new \OCA\Checkin\AppInfo\Application();
$container = $app->getContainer();
$controller = $container->query('PageController');

$response = $controller->index();
$response->render();
