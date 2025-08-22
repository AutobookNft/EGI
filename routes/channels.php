<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to determine if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('price.{egiId}', function ($user = null, $egiId) {
    return true; // public per guest
    // per private: return $user !== null;
});
