<?php

namespace Brueggern\RabbitmqPlayground\RabbitMQ;

use Brueggern\RabbitmqPlayground\Logger\FileLogger;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class HelloWorldHandler extends RabbitMQHandler
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
        $this->declareQueue($queueName)
            ->sendMessage($message, $queueName)
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
        $this->declareQueue($queueName)
            ->readMessages($queueName)
            ->close();
    }

    /**
     * Declare new queue. If it does not exist, it will be created.
     *
     * @param string $queueName
     * @return $this
     */
    protected function declareQueue(string $queueName): self
    {
        $this->channel->queue_declare(
            $queueName,
            false,
            true,
            false,
            false
        );

        return $this;
    }

    /**
     * Send a message.
     *
     * @param string $message
     * @param string $queueName
     * @return $this
     */
    protected function sendMessage(string $message, string $queueName): self
    {
        $this->channel->basic_publish(
            new AMQPMessage($message),
            '',
            $queueName
        );

        return $this;
    }

    /**
     * @param string $queueName
     * @return $this
     */
    protected function readMessages(string $queueName): self
    {
        $callback = function ($msg) {
            (new FileLogger())->log($msg->body);
            echo '[x] Received: ' . $msg->body . PHP_EOL;
        };

        $this->channel->basic_consume(
            $queueName,
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

        return $this;
    }
}