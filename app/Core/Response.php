<?php

namespace App\Core;

class Response
{
    protected int $statusCode = 200;
    protected string $content = '';

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        echo $this->content;
    }
}