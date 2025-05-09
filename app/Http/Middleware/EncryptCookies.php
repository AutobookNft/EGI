<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Illuminate\Support\Facades\Log;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'connected_wallet'
    ];

    public function handle($request, \Closure $next)
    {
        Log::channel('upload')->info('EncryptCookies: except array', ['except' => $this->except]);
        return parent::handle($request, $next);
    }
}
