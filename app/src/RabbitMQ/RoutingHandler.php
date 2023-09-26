<?php

namespace Brueggern\RabbitmqPlayground\RabbitMQ;

use Brueggern\RabbitmqPlayground\Logger\FileLogger;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class RoutingHandler extends RabbitMQHandler
{
    /**
     * @param string $message
     * @param string|null $queueName
     * @param string|null $exchangeName
     * @param string|null $routingKey
     * @return void
     * @throws Exception
     */
    function produce(
        string  $message,
        ?string $queueName = null,
        ?string $exchangeName = null,
        ?string $routingKey = null
    ): void
    {
        $this->sendMessage($message, $exchangeName, $routingKey)
            ->close();
    }

    /**
     * @param string|null $queueName
     * @param string|null $exchangeName
     * @return void
     * @throws Exception
     */
    function consume(?string $queueName = null, ?string $exchangeName = null): void
    {
        $this->readMessages($exchangeName)
            ->close();
    }

    /**
     * Send a message.
     *
     * @param string $message
     * @param string $exchangeName
     * @param string $routingKey
     * @return $this
     */
    protected function sendMessage(string $message, string $exchangeName, string $routingKey): self
    {
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        // First we need to declare a direct exchange.
        $this->channel->exchange_declare($exchangeName, 'direct', false, false, false);

        $this->channel->basic_publish($msg, $exchangeName, $routingKey);

        return $this;
    }

    /**
     * @param string $exchangeName
     * @return $this
     */
    protected function readMessages(string $exchangeName): self
    {
        $callback = function ($msg) {
            (new FileLogger())->log($msg->body);
            echo ' [x] Received ', $msg->body, "\n";
            $msg->ack();
        };

        // This tells RabbitMQ not to give more than one message to a worker at a time.
        $this->channel->basic_qos(0, 1, false);

        // First we declare a direct exchange.
        $this->channel->exchange_declare($exchangeName, 'direct', false, false, false);

        // Then we declare a temporary queue (with a generated name) and bind it to the exchange.
        [$queueName] = $this->channel->queue_declare('', false, false, true, false);
        
        // Create a binding for each severity we're interested in.
        $severities = match ($exchangeName) {
            'exchange-calendar' => ['info'],
            'exchange-activities' => ['info', 'warning', 'error'],
            default => [],
        };
        foreach ($severities as $severity) {
            $this->channel->queue_bind($queueName, $exchangeName, $severity);
        }

        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

        return $this;
    }
}