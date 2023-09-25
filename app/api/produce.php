<?php

use Brueggern\RabbitmqPlayground\RabbitMQ\RabbitMQHandler;

require_once __DIR__ . '/../vendor/autoload.php';

// Takes raw data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$message = $data['message'] ?? null;
$queue = $data['queue'] ?? null;

if (!$message || !$queue) {
    header("Content-Type: application/json");
    echo json_encode([
        'error' => true,
        'message' => 'Missing message or queue',
    ]);
    exit();
}

try {
    RabbitMQHandler::resolve($_ENV['HANDLER'])->produce($message, $queue);
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
    ]);
    exit();
}

header("Content-Type: application/json");
echo json_encode([
    'message' => $message,
    'queue' => $queue,
    'rabbitmq_host' => $_ENV["RABBITMQ_HOST"],
]);
exit();