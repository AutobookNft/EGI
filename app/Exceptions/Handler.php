<?php

namespace App\Exceptions;


use Ultra\ErrorManager\Exceptions\UltraErrorException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        UltraErrorException::class, // <-- Aggiungi questa riga
    ];

    
}
