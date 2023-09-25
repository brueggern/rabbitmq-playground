<?php

namespace Brueggern\RabbitmqPlayground\RabbitMQ;

use Exception;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class RabbitMQHandler
{
    /**
     * @var AMQPStreamConnection
     */
    protected AMQPStreamConnection $connection;

    /**
     * @var AbstractChannel|AMQPChannel
     */
    protected AbstractChannel|AMQPChannel $channel;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            $_ENV["RABBITMQ_HOST"],
            $_ENV["RABBITMQ_PORT"],
            $_ENV["RABBITMQ_USER"],
            $_ENV["RABBITMQ_PASSWORD"]
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * @param string $message
     * @param string $queueName
     * @return void
     * @throws Exception
     */
    abstract function produce(string $message, string $queueName): void;

    /**
     * @param string $queueName
     * @return void
     * @throws Exception
     */
    abstract function consume(string $queueName): void;

    /**
     * @param string $key
     * @return RabbitMQHandler
     * @throws Exception
     */
    public static function resolve(string $key): RabbitMQHandler
    {
        $handlerName = match ($key) {
            'hello-world' => HelloWorldHandler::class,
            'work-queues' => WorkQueuesHandler::class,
            'publish-subscribe' => PublishSubscribeHandler::class,
            default => null,
        };
        if (!$handlerName) {
            throw new Exception('Handler not found');
        }
        return new $handlerName();
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function close(): self
    {
        $this->channel->close();
        $this->connection->close();

        return $this;
    }
}