<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home as HomeResource;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function home()
    {
        return view('welcome');
    }
}
