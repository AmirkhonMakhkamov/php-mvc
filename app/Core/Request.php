<?php

namespace App\Core;

class Request
{
    public function getUri(): string
    {
        return filter_input(
            INPUT_SERVER, 'REQUEST_URI'
        );
    }

    public function getMethod(): string
    {
        return strtoupper(
            filter_input(
                INPUT_SERVER, 'REQUEST_METHOD'
            )
        );
    }

    public function getHost(): string
    {
        // logic to handle external hosts here (e.g. white-labeling with custom domain)
        return filter_input(
            INPUT_SERVER, 'HTTP_HOST'
        );
    }

    public function getInput(string $key, string $default = ''): string
    {
        return filter_input(INPUT_POST, $key) ?? filter_input(INPUT_GET, $key) ?? $default;
    }

    public function all(): array
    {
        return $_REQUEST;
    }
}