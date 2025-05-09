<?php

namespace Illuminate\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Support\Traits\Macroable;

class Request extends SymfonyRequest
{
    use Macroable;

    public static function create(
        string $uri,
        string $method = 'GET',
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ): static {
        file_put_contents(
            base_path('who-called-request.log'),
            "Request::create called with URI: $uri\n",
            FILE_APPEND
        );

        return parent::create($uri, $method, $parameters, $cookies, $files, $server, $content);
    }
}

