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
     * @param string|null $queueName
     * @param string|null $exchangeName
     * @param string|null $routingKey
     * @return void
     * @throws Exception
     */
    abstract function produce(
        string  $message,
        ?string $queueName = null,
        ?string $exchangeName = null,
        ?string $routingKey = null
    ): void;

    /**
     * @param string|null $queueName
     * @param string|null $exchangeName
     * @return void
     * @throws Exception
     */
    abstract function consume(
        ?string $queueName = null,
        ?string $exchangeName = null
    ): void;

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
            'routing' => RoutingHandler::class,
            'topics' => TopicsHandler::class,
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