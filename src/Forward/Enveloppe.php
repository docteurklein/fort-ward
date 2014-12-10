<?php

namespace Forward;

final class Enveloppe
{
    private $recipient;
    private $message;

    public function __construct(Message $message, $recipient)
    {
        $this->recipient = $recipient;
        $this->message = $message;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
