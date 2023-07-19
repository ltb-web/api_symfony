<?php

namespace App\Model;

class ErrorResponce
{
    public function __construct(private string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
