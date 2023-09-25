# RabbitMQ Playground

All examples are based on the [RabbitMQ Tutorials](https://www.rabbitmq.com/tutorials/tutorial-one-php.html). Please
read the official RabbitMQ documentation for more information.

## Environment file

```
cp .env.example .env
```

## Handler

Please change the handler in the `.env`file to the handler you want to use.

- hello-world
- work-queues
- publish-subscribe

## Run application

Start with local RabbitMQ server

```
./up.sh local
```

Start with remote RabbitMQ server

```
./up.sh remote
```

Shutdown with local RabbitMQ server

```
./down.sh local
```

Shutdown with remote RabbitMQ server

```
./down.sh remote
```