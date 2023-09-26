<?php

use Brueggern\RabbitmqPlayground\Logger\FileLogger;

require_once __DIR__ . '/../vendor/autoload.php';

(new FileLogger())->clear();
header("Content-Type: application/json");
echo json_encode([
    'message' => 'Logs cleared',
]);
exit();