<?php

namespace Forward\Router;

use Forward\Message;

interface Adapter
{
    public function push(Message $message, $recipient);

    public function on($queue, callable $worker);
}
