<?php

namespace Forward\Router\Adapter;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Forward\Message;
use Forward\Router\Adapter;
use PhpAmqpLib\Connection\AMQPConnection;

class Rabbit implements Adapter
{
    private $connection;
    private $channel;

    public function __construct(AMQPConnection $connection)
    {
        $this->connection = $connection;
        $this->channel = $connection->channel();
    }

    public function push(Message $message, $recipient)
    {
        $this->channel->exchange_declare($recipient, 'direct');
        $this->channel->queue_declare($recipient);
        $this->channel->queue_bind($recipient, $recipient);

        $this->channel->basic_publish(new AMQPMessage(json_encode($message)), $recipient);
    }

    public function on($queue, callable $worker)
    {
        $this->channel->queue_declare($queue);

        $this->channel->basic_consume($queue, '', false, true, false, true, function(AMQPMessage $message) use($worker, $queue) {
            return call_user_func($worker, new Message\Generic(json_decode($message->body)), $queue);
        });


        var_dump((count($this->channel->callbacks)));
        //while (count($this->channel->callbacks)) {
        //    $read = [$socket = $this->connection->getSocket()];
        //    stream_set_blocking($socket, 0);
        //    $write = $except = null;
        //    if ($nb = stream_select($read, $write, $except, 60) > 0) {
        //        $this->channel->wait();
        //    }
        //}
    }
}
