<?php

namespace App\Http\Controllers\Api;

use App\RecentActivity;
use App\Http\Controllers\Controller;

class RecentActivityController extends Controller
{
    public function getActivities()
    {
        //  Start getting the recent activities instance
        $activityInstance = new RecentActivity();

        $data = $activityInstance->getActivities(
                    request('modelId'), request('modelType'), request('type'), request('allocation'),
                    request('count'), request('groupBy')
                );

        $success = $data['success'];
        $response = $data['response'];

        //  If the activities were captured successfully
        if ($success) {
            //  If this is a success then we have a activities returned
            $activities = $response;

            //  Action was executed successfully
            return oq_api_notify($activities, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }
}
