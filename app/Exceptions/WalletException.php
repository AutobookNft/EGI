<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;
use App\DataTransferObjects\Payloads\Wallets\WalletError;

class WalletException extends Exception
{
    protected WalletError $walletError;

    public function __construct(WalletError $walletError, int $code = 0, ?Throwable $previous = null)
    {
        $this->walletError = $walletError;
        parent::__construct($walletError->message, $code, $previous);
    }

    public function getWalletError(): WalletError
    {
        return $this->walletError;
    }
}
