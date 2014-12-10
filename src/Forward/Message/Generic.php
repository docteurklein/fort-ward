<?php

namespace Forward\Message;

use Forward\Message;

class Generic implements Message
{
    private $body;

    public function __construct(array $body)
    {
        $this->body = $body;
    }

    public function jsonSerialize()
    {
        return $this->body;
    }
}
