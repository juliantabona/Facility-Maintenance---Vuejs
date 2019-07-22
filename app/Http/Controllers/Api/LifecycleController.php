<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use App\Lifecycle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LifecycleController extends Controller
{
    public function updateLifecycleStageProgress()
    {
        //  Lifecycle Instance
        $data = ( new Lifecycle() )->initiateUpdateLifecycleProgress();
        $success = $data['success'];
        $response = $data['response'];

        //  If the lifecycle was recorded successfully
        if ($success) {
            //  If this is a success then we have the lifecycle
            $lifecycle = $response;

            //  Action was executed successfully
            return oq_api_notify($lifecycle, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function undoLifecycleStageProgress()
    {
        //  Lifecycle Instance
        $data = ( new Lifecycle() )->initiateUndoLifecycleProgress();
        $success = $data['success'];
        $response = $data['response'];

        //  If the lifecycle was recorded successfully
        if ($success) {
            //  If this is a success then we have the lifecycle
            $lifecycle = $response;

            //  Action was executed successfully
            return oq_api_notify($lifecycle, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }
}
