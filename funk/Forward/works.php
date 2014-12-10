<?php

namespace funk\Forward;

use Funk\Spec;
use Forward\Message;
use Forward\Router\PassesOn;
use Forward\Router\Adapter;
use React\EventLoop;
use React\Stomp;

class works implements Spec
{
    function it_works()
    {
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

        ob_start();
        $loop->run();

        $out = ob_get_clean();
        expect($out)->toMatch('/first worker/');
        expect($out)->toMatch('/second worker/');
        expect($out)->toMatch('/third worker/');
    }
}
