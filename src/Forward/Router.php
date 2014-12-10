<?php

namespace Forward;

use Forward\Message;

interface Router
{
    public function register(callable $worker, $queue);

    public function work(Message $message, $queue);
}
