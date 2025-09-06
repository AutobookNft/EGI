<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPendingWallet
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Assumiamo che l'ID del Creator sia disponibile, ad esempio dal request o dall'utente autenticato.
        $proposerId = Auth::id(); // oppure prendi il creatorId dal contesto specifico

        if (hasPendingWallet($proposerId)) {
            Log::channel('florenceegi')->error('Non è possibile eseguire l\'azione perché esiste già un wallet pending.', [
                'proposerId' => $proposerId
            ]);
            return response()->json([
                'message' => __('collection.wallet.validation.check_pending_wallet'),
            ], 422);
        }

        return $next($request);
    }
}