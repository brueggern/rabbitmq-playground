<?php

namespace Brueggern\RabbitmqPlayground\Logger;

class FileLogger
{
    /**
     * @param string $message
     * @param string $queue
     * @return void
     */
    public function log(string $message, string $queue): void
    {
        file_put_contents(
            __DIR__ . "/../../logs/" . $queue . "-" . date("Y-m-d") . ".log",
            date('Y-m-d\TH:i:s') . ': ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * @param string $queue
     * @return string
     */
    public function read(string $queue): string
    {
        return file_get_contents(
            __DIR__ . "/../../logs/" . $queue . "-" . date("Y-m-d") . ".log"
        );
    }
}