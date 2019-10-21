<?php

namespace App\Traits;

use DB;

trait RecentActivityTraits
{
    /*  saveActivity()
     *
     *  Saves the activity of a given model with a specified status
     *  as well as the authenticated user responsible for the activity.
     */
    function saveActivity($model=null, $status=null, $activity_details = false)
    {
        /*  If the model is provided  */
        if ($model != null) {

            /*  Get the authenticated user  */
            $user = auth('api')->user() ?? null;
    
            /*  
             *  If the activity_details variable is set to false. Then save the entire model data.
             *  We will use the model class name and model details to create a key-value dataset
             */
            if ($activity_details === false) {

                $details = [

                    strtolower(snake_case(class_basename($model))) => $model,    //  'document' => [Document Object]

                ];

            /*  
             *  If the activity_details variable is not null. Then save the data provided
             */
            } elseif ($activity_details != null) {

                $details = $activity_details;

            /*  
             *  If the activity_details variable is null. Then do not save any data
             */
            } else {

                $details = null;

            }
    
            /*  
             *  Save the activity
             */
            $activity = $model->recentActivities()->create([

                'type' => $status,
                'activity' => $details,
                'user_id' => !empty($user) ? $user->id : null

            ]);

            return $activity ? true : false;

        }
    
        return false;
    }

}
