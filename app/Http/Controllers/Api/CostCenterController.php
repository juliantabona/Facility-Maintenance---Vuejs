<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\CostCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CostCenterController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no costcenters
        $costcenters = [];

        //  Query data
        $modelType = request('model', 'company');       //  e.g) company, all
        $costcenterType = request('costcenter_type');   //  e.g) jobcard, asset, e.t.c
        $model_id = request('modelId');                 //  The id of the client/supplier for getting related costcenters

        /*  First thing is first, we need to understand one of 2 scenerios, Either we want:
         *
         *  1) Only costcenters for a related COMPANY of the authenticated user
         *  9) Only costcenters for a specified COMPANY by using a unique identifyer
         *  9) All costcenters in the system e.g) If SuperAdmin needs access to all data
         *
         *  Once we have those costcenters we will determine whether we want any of the following
         *
         *  1) All costcenters aswell as the trashed ones
         *  2) Only costcenters that are trashed
         *  3) Only costcenters that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific costcenters
         *  If the user indicated that they want costcenters related to their company,
         *  then get the costcenters related to the authenticated users company.
         *  They must indicate using the query "model" set to "company".
         */
        if ($modelType == 'company') {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW COMPANY COSTCENTERS  *
            /**************************************************************/

            //  Check if the user specified the model_id
            if (empty($model_id)) {
                $companyId = $user->companyBranch->company->id;
            } else {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW SPECIFIED COMPANY COSTCENTERS  *
                /************************************************************************/

                $companyId = $model_id;
            }

            $costcenters = Company::where('id', $companyId)->first()->costcenters();

        /*  ALL costcenters
         *  If the user wants all the costcenters in the system, they must indicate
         *  using the query "model" set to "all". This is normaly used by authorized
         *  superadmins to access all costcenters resources in the system.
         */
        } elseif ($modelType == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL costcenters    *
            /**********************************************************/

            /*  ALL costcenters
            *  If the user wants all the costcenters in the system, they must indicate
            *  using the query "model" set to "all". This is normaly used by authorized
            *  superadmins to access all costcenters resources in the system.
            */

            /*   Create a new costcenters instance that can be used to retrieve all costcenters
             */
            $costcenters = new CostCenter();
        }

        /*  If the user specified to get specific types of categories,
         *  e.g) Only categories for jobcards or assets, e.t.c
         */
        if (!empty($costcenterType)) {
            $costcenters = $costcenters->where('type', $costcenterType);
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

        $order_join = 'costcenters';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $costcenters = $costcenters->withTrashed()->get();
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $costcenters = $costcenters->onlyTrashed()->get();
            //  Get all except trashed
            } else {
                //  Run query
                $costcenters = $costcenters->get();
            }

            //  If we have any costcenters so far
            if (count($costcenters)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $costcenters->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($costcenters, 200);
    }
}
