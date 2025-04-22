<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardStaticController extends Controller
{
    public function index()
    {
        Log::channel('florenceegi')->info('DashboardStaticController: index');

        $collectionsCount = Collection::where('creator_id', Auth::id())->count();
        $collectionMembersCount = CollectionUser::whereHas('collection', function ($query) {
            $query->where('creator_id', Auth::id());
        })
        ->where('user_id', '!=', Auth::id())
        ->count();

        $pendingNotifications = Auth::user()
            ->customNotifications()
            ->where(function ($query) {
                $query->where('outcome', 'LIKE', '%pending%')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereIn('outcome', ['accepted', 'rejected', 'expired'])
                                ->whereNull('read_at');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->with('model')
            ->get();

        $activeNotificationId = $pendingNotifications->isNotEmpty() ? $pendingNotifications->first()->id : null;

        return view('dashboard-static', [
            'collectionsCount' => $collectionsCount,
            'collectionMembersCount' => $collectionMembersCount,
            'pendingNotifications' => $pendingNotifications,
            'activeNotificationId' => $activeNotificationId,
        ]);
    }
}
