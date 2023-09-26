<?php

namespace Brueggern\RabbitmqPlayground\RabbitMQ;

use Exception;

class RoutingHandler extends RabbitMQHandler
{
    /**
     * @param string $message
     * @param string|null $queueName
     * @param string|null $exchangeName
     * @return void
     * @throws Exception
     */
    function produce(string $message, ?string $queueName = null, ?string $exchangeName = null): void
    {
        $this->declareQueue($queueName)
            ->sendMessage($message, $exchangeName)
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
            ->readMessages($exchangeName)
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


        return $this;
    }

    /**
     * @param string $queueName
     * @return $this
     */
    protected function readMessages(string $queueName): self
    {


        return $this;
    }
}