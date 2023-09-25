<?php

use Brueggern\RabbitmqPlayground\RabbitMQ\RabbitMQHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$queueName = $_ENV['RABBITMQ_QUEUE'] ?? 'default';

try {
    echo '[x] Start consuming on ' . $queueName . PHP_EOL;
    RabbitMQHandler::resolve($_ENV['HANDLER'])->consume($queueName);
    echo '[x] Closed connection on ' . $queueName . PHP_EOL;
} catch (Exception $e) {
    echo '[x] Error: ' . $e->getMessage() . PHP_EOL;
}
