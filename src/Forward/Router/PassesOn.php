<?php

namespace Forward\Router;

use Forward\Router\Adapter;
use Forward\Enveloppe;
use Forward\Router;
use Forward\Message;

final class PassesOn implements Router
{
    private $workers = [];
    private $chain = [];
    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function register(callable $worker, $queue, $next = null)
    {
        $this->workers[$queue][] = $worker;
        if (!empty($next)) {
            $this->chain[$queue] = $next;
        }

        $this->adapter->on($queue, [$this, 'work']);

        return $this;
    }

    public function work(Message $message, $queue)
    {
        foreach ($this->workers[$queue] as $worker) {
            $enveloppe = $worker->__invoke($message);
            if ($enveloppe instanceof Enveloppe) {
                return $this->adapter->push($enveloppe->getMessage(), $enveloppe->getRecipient());
            }

            if (!empty($this->chain[$queue])) {
                return $this->adapter->push($message, $this->chain[$queue]);
            }
        }
    }
}
