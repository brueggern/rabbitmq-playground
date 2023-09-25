<?php

namespace Brueggern\RabbitmqPlayground\RabbitMQ;

use Brueggern\RabbitmqPlayground\Logger\FileLogger;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class WorkQueuesHandler extends RabbitMQHandler
{
    /**
     * @param string $message
     * @param string $queueName
     * @return void
     * @throws Exception
     */
    function produce(string $message, string $queueName): void
    {
        $this->declareQueue($queueName)
            ->sendMessage($message, $queueName)
            ->close();
    }

    /**
     * @param string $queueName
     * @return void
     * @throws Exception
     */
    function consume(string $queueName): void
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
        $data = $message . '...';
        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $this->channel->basic_publish($msg, '', $queueName);

        return $this;
    }

    /**
     * @param string $queueName
     * @return $this
     */
    protected function readMessages(string $queueName): self
    {
        $callback = function ($msg) use ($queueName) {
            (new FileLogger())->log($msg->body, $queueName);
            echo ' [x] Received ', $msg->body, "\n";
            sleep(substr_count($msg->body, '.') + 2);
            echo " [x] Done\n";
            $msg->ack();
        };

        // This tells RabbitMQ not to give more than one message to a worker at a time.
        $this->channel->basic_qos(0, 1, false);

        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

        return $this;
    }
}