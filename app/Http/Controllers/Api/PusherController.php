<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PusherController extends Controller
{
    public function index(Request $request)
    {
        
        $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));

        return $pusher->socket_auth($request->request->get('channel_name'), $request->request->get('socket_id'));

    }
}
