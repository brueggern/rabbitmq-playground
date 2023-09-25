<?php

use Brueggern\RabbitmqPlayground\Logger\FileLogger;

require_once __DIR__ . '/../vendor/autoload.php';

// Takes raw data from the request
$queue = $_GET['queue'] ?? null;

if (!$queue) {
    header("Content-Type: application/json");
    echo json_encode([
        'error' => true,
        'message' => 'Missing queue',
    ]);
    exit();
}

$logs = (new FileLogger())->read($queue);
header("Content-Type: application/json");
echo json_encode(explode(PHP_EOL, $logs));
exit();