Fort-Ward
=========

# What ?

A php5.4+ library that eases passing of messaging between workers.

# Why ?

To ease creation of chained pieces of work.

# How ?

This will register 3 workers:

 - worker1 listens on queue "first", and the result will be passed to worker2, once done.
 - worker2 listens on queue "second", and the result will be passed to worker3, once done.
 - worker3 listens on queue "third".

``` php
<?php

$loop = EventLoop\Factory::create();
$router = new PassesOn($adapter = new Adapter\ReactStomp((new Stomp\Factory($loop))->createClient([
    'host' => 'rabbit',
])));
$router
    ->register(function(Message $message) { var_dump('first worker'); }, 'first', 'second')
    ->register(function(Message $message) use($loop) { var_dump('second worker'); }, 'second', 'third')
    ->register(function(Message $message) use($loop) { var_dump('third worker'); $loop->stop(); }, 'third')
;

$adapter->push(new Message\Generic(['test']), 'first');

$loop->run();

```

