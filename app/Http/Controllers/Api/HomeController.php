<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home as HomeResource;

class HomeController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function home()
    {
        //  Return the home Api Resource
        return new HomeResource($this);
    }

}
