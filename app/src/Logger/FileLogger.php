<?php

namespace Brueggern\RabbitmqPlayground\Logger;

class FileLogger
{
    /**
     * @param string $message
     * @param string $fileName
     * @return void
     */
    public function log(string $message, string $fileName = 'default'): void
    {
        file_put_contents(
            __DIR__ . "/../../logs/" . $fileName . "-" . date("Y-m-d") . ".log",
            date('Y-m-d\TH:i:s') . ': ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function read(string $fileName = 'default'): string
    {
        return file_get_contents(
            __DIR__ . "/../../logs/" . $fileName . "-" . date("Y-m-d") . ".log"
        );
    }
}