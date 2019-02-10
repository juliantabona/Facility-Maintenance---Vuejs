<?php

namespace App\Traits;

use DB;

trait RecentActivityTraits
{
    public function getActivities($modelId = null, $modelType = null, $activityType = null, $allocation = null, $count = null, $groupBy = null)
    {
        /*
        Params:
            $modelId                      //  The id of the associated model
            $modelType                    //  Associated model e.g) user, company, invoice e.t.c
            $activityType                 //  Specific activity e.g) created, updated, e.t.c - (Optional)
        */

        //  Current authenticated user
        $user = auth('api')->user();

        //  Create the dynamic model using the $modelType otherwise return the recentActivity() instance
        if ($modelId && $modelType) {
            $dynamicModel = $this->generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Get the associated activities
                $activities = $dynamicModel::find($modelId)->recentActivities();
            }
        } else {
            $activities = $this;
        }

        if ($activities) {
            $order_join = 'recent_activities';

            //  Filter by allocation - If specified
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL ACTIVITIES       *
                /**********************************************************/

                //  Will all activities
                $activities = $activities->where('company_id', $user->company_id);
            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH ACTIVITIES  *
                /*************************************************************/

                //  Will only get all user company branch related activities
                $activities = $activities->where('company_branch_id', $user->company_branch_id);
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY ACTIVITIES  *
                /**************************************************************/

                //  Will only get all user company related activities
                $activities = $activities->where('company_id', $user->company_id);
            }

            //  Filter by type - If specified
            if ($activityType) {
                $activities = $activities->where('type', $activityType);
            }

            try {
                //  Perform groupBy and Count
                if ($count == 1) {
                    $groupBy = $groupBy ? $groupBy : 'type';
                    $activities = $activities->select($groupBy.' as group', DB::raw('count(*) as total_activities'))
                                             ->groupBy($groupBy)
                                             ->advancedFilter(['order_join' => $order_join]);

                    //  Lets change the pagination data to only return the group name and total activities only
                    //  This is so that we don't return other useless data that we do not need
                    $paginationData = collect($activities->getCollection())->transform(function ($activity) {
                        return $activity->only(['group', 'total_activities']);
                    });

                    //  Now that we have changed the pagination data, we need to rebuild the paginated results
                    //  so that we can include the total, per page, current page, e.t.c
                    $activities = new \Illuminate\Pagination\LengthAwarePaginator(
                        $paginationData,
                        $activities->total(),
                        $activities->perPage(),
                        $activities->currentPage(), [
                            'path' => \Request::url(),
                            'query' => [
                                'page' => $activities->currentPage(),
                            ],
                        ]
                    );
                } else {
                    //  Get all activities
                    $activities = $activities->advancedFilter(['order_join' => $order_join]);

                    //  If we have any activities so far
                    if (count($activities)) {
                        //  Eager load other relationships wanted if specified
                        if (request('connections')) {
                            $activities->load(oq_url_to_array(request('connections')));
                        }
                    }
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $activities];
            } catch (\Exception $e) {
                $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                return ['success' => false, 'response' => $response];
            }
        }
    }

    public function generateDynamicModel($modelType)
    {
        //  Create the dynamic model
        $dynamicModel = ('\App\\'.ucfirst($modelType));  //  \App\User

        //  Check if this is a valid dynamic class
        if (class_exists($dynamicModel)) {
            //  Model class does exist

            return $dynamicModel;
        } else {
            return false;
        }
    }
}
