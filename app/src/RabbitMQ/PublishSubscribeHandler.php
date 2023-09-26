<?php

namespace Brueggern\RabbitmqPlayground\RabbitMQ;

use Brueggern\RabbitmqPlayground\Logger\FileLogger;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class PublishSubscribeHandler extends RabbitMQHandler
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
        $this->sendMessage($message, $exchangeName)
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
     * @return $this
     */
    protected function sendMessage(string $message, string $exchangeName): self
    {
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $this->channel->exchange_declare($exchangeName, 'fanout', false, false, false);
        $this->channel->basic_publish($msg, $exchangeName);

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

        // First we declare a fanout exchange.
        $this->channel->exchange_declare($exchangeName, 'fanout', false, false, false);

        // Then we declare a temporary queue (with a generated name) and bind it to the exchange.
        [$queueName] = $this->channel->queue_declare('', false, false, true, false);
        $this->channel->queue_bind($queueName, $exchangeName);

        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

        return $this;
    }
}