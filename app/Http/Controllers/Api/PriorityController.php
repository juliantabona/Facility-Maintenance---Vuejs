<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Priority;

class PriorityController extends Controller
{
    public function index()
    {
        //  Priority Instance
        $data = ( new Priority() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the priorities were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of priorities
            $priorities = $response;

            //  Action was executed successfully
            return oq_api_notify($priorities, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }
}
