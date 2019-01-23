<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $limit = request('limit', 15);

        if (request('unread') == 1) {
            $notifications = $user->unreadNotifications->take($limit);
        } else {
            $notifications = $user->notifications->take($limit);
        }

        //  Action was executed successfully
        return oq_api_notify($notifications, 200);
    }
}
