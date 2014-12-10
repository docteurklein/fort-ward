<?php

namespace Forward\Router\Adapter;

use Forward\Message;
use Forward\Router\Adapter;
use React\Stomp\Client;

class ReactStomp implements Adapter
{
    public function __construct(Client $client)
    {
        $this->client = $client;
        $client->connect();
    }

    public function push(Message $message, $recipient)
    {
        $this->client->send('/topic/'.$recipient, json_encode($message));
    }

    public function on($queue, callable $worker)
    {
        $this->client->subscribe('/topic/'.$queue, function($message) use($worker, $queue) {
            return call_user_func($worker, new Message\Generic(json_decode($message->body)), $queue);
        });
    }
}
