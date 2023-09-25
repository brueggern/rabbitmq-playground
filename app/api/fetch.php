<?php

use Brueggern\RabbitmqPlayground\Logger\FileLogger;

require_once __DIR__ . '/../vendor/autoload.php';

$logs = (new FileLogger())->read();
header("Content-Type: application/json");
echo json_encode(explode(PHP_EOL, $logs));
exit();