<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\User;
use App\Mail\ActivateAccount;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function sendEmail()
    {
        $user = User::find(1);

        Mail::to($user->email)->send(new ActivateAccount($user));

        return 'Email sent successfully!';
    }
}
