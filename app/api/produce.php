<?php

use Brueggern\RabbitmqPlayground\RabbitMQ\RabbitMQHandler;

require_once __DIR__ . '/../vendor/autoload.php';

// Takes raw data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$message = $data['message'] ?? null;
$queueName = $data['queue'] ?? 'default';
$exchangeName = $data['exchange'] ?? 'default';
$routingKey = $data['routing_key'] ?? 'info';

if (!$message) {
    header("Content-Type: application/json");
    echo json_encode([
        'error' => true,
        'message' => 'Missing message',
    ]);
    exit();
}

try {
    RabbitMQHandler::resolve($_ENV['HANDLER'])
        ->produce($message, $queueName, $exchangeName, $routingKey);
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
    'queue' => $queueName,
    'exchange' => $exchangeName,
    'routing_key' => $routingKey,
    'rabbitmq_host' => $_ENV["RABBITMQ_HOST"],
]);
exit();