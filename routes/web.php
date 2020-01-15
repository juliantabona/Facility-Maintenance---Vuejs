<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Web\HomeController@home')->name('web-home');

/*

use App\Events\TestEvent;

Route::get('/send', function () {
    //  Simulate user logging In
    Auth::loginUsingId(1, true);

    //  Get the logged In user
    $user = Auth::user();

    //  Make the test pusher event
    event(new TestEvent($user));

    //  Notify success
    return 'success!';
});

*/