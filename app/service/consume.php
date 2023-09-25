<?php

use Brueggern\RabbitmqPlayground\RabbitMQ\RabbitMQHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$queueName = $_ENV['RABBITMQ_QUEUE'] ?? 'default';
$exchangeName = $_ENV['RABBITMQ_EXCHANGE'] ?? 'default';

try {
    echo '[x] Start consuming on (' . $queueName . ', ' . $exchangeName . ')' . PHP_EOL;
    RabbitMQHandler::resolve($_ENV['HANDLER'])
        ->consume($queueName, $exchangeName);
    echo '[x] Closed connection on (' . $queueName . ', ' . $exchangeName . ')' . PHP_EOL;
} catch (Exception $e) {
    echo '[x] Error: ' . $e->getMessage() . PHP_EOL;
}
