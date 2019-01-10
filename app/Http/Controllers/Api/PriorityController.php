<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriorityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no priorities
        $priorities = [];

        //  Query data
        $modelType = request('model', 'company');     //  e.g) company, all
        $priorityType = request('priority_type');     //  e.g) jobcard, asset, e.t.c
        $model_id = request('modelId');               //  The id of the client/supplier for getting related priorities

        /*  First thing is first, we need to understand one of 2 scenerios, Either we want:
         *
         *  1) Only priorities for a related COMPANY of the authenticated user
         *  9) Only priorities for a specified COMPANY by using a unique identifyer
         *  9) All priorities in the system e.g) If SuperAdmin needs access to all data
         *
         *  Once we have those priorities we will determine whether we want any of the following
         *
         *  1) All priorities aswell as the trashed ones
         *  2) Only priorities that are trashed
         *  3) Only priorities that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific priorities
         *  If the user indicated that they want priorities related to their company,
         *  then get the priorities related to the authenticated users company.
         *  They must indicate using the query "model" set to "company".
         */
        if ($modelType == 'company') {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW COMPANY PRIORITIES  *
            /**************************************************************/

            //  Check if the user specified the model_id
            if (empty($model_id)) {
                $companyId = $user->companyBranch->company->id;
            } else {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW SPECIFIED COMPANY PRIORITIES  *
                /************************************************************************/

                $companyId = $model_id;
            }

            $priorities = Company::where('id', $companyId)->first()->priorities();

        /*  ALL priorities
         *  If the user wants all the priorities in the system, they must indicate
         *  using the query "model" set to "all". This is normaly used by authorized
         *  superadmins to access all priorities resources in the system.
         */
        } elseif ($modelType == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL priorities    *
            /**********************************************************/

            /*  ALL priorities
            *  If the user wants all the priorities in the system, they must indicate
            *  using the query "model" set to "all". This is normaly used by authorized
            *  superadmins to access all priorities resources in the system.
            */

            /*   Create a new priorities instance that can be used to retrieve all priorities
             */
            $priorities = new Category();
        }

        /*  If the user specified to get specific types of priorities,
         *  e.g) Only priorities for jobcards or assets, e.t.c
         */
        if (!empty($priorityType)) {
            $priorities = $priorities->where('type', $priorityType);
        }

        /*  To avoid sql order_by error for ambigious fields e.g) created_at
         *  we must specify the order_join.
         *
         *  Order joins help us when using the "advancedFilter()" method. Usually
         *  we need to specify the joining table so that the system is not confused
         *  by similar column names that exist on joining tables. E.g) the column
         *  "created_at" can exist in multiple table and the system might not know
         *  whether the "order_by" is for table_1 created_at or table 2 created_at.
         *  By specifying this we end up with "table_1.created_at"
         *
         *  If we don't have any special order_joins, lets default it to nothing
         */

        $order_join = 'priorities';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $priorities = $priorities->withTrashed()->get();
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $priorities = $priorities->onlyTrashed()->get();
            //  Get all except trashed
            } else {
                //  Run query
                $priorities = $priorities->get();
            }

            //  If we have any priorities so far
            if (count($priorities)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $priorities->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($priorities, 200);
    }
}