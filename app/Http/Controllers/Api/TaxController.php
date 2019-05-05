<?php

namespace App\Http\Controllers\Api;

use App\Tax;
use App\Company;
use App\CompanyBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no taxes
        $taxes = [];

        //  Query data
        $modelType = request('model', 'company');           //  e.g) company, branch, all
        $model_id = request('modelId');                     //  The id of the company/branch for getting related taxes

        /*  First thing is first, we need to understand one of 3 scenerios, Either we want:
         *
         *  1) Only taxes for a related COMPANY of the authenticated user
         *  2) Only taxes for a specified COMPANY ID
         *  3) Only taxes for a related BRANCH by using a unique identifyer
         *  4) Only taxes for a specified BRANCH ID
         *  5) All taxes in the system
         *
         *  We will then determine whether we want any of the following
         *
         *  1) All taxes aswell as the trashed ones
         *  2) Only taxes that are trashed
         *  3) Only taxes that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific taxes
         *  If the user indicated that they want taxes related to their company,
         *  or any taxes related to a specified company of their choice.
         *  They must indicate using the query "model" set to "company" and they can
         *  set "model_id" to specify an exact company id to target.
         */
        if ($modelType == 'company') {
            //  Check if the user specified the "model_id"
            if (empty($model_id)) {
                /********************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW SPECIFIED COMPANY TAXES  *
                /********************************************************************/

                $companyId = $user->companyBranch->company->id;
            } else {
                /********************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW THEIR COMPANY TAXES      *
                /********************************************************************/

                $companyId = $model_id;
            }

            $company = Company::where('id', $companyId)->first();

            if ($company) {
                $taxes = $company->taxes();
            } else {
                return oq_api_notify_error('Specified company was not found', 1, 404);
            }

            /*  User Branch specific taxes
            *  If the user indicated that they want taxes related to their branch,
            *  or any taxes related to a specified branch of their choice.
            *  They must indicate using the query "model" set to "branch" and they can
            *  set "model_id" to specify an exact branch id to target.
            */
        } elseif ($modelType == 'branch') {
            //  Check if the user specified the "model_id"
            if (empty($model_id)) {
                /********************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW SPECIFIED BRANCH TAXES   *
                /********************************************************************/

                $branchId = $user->company_branch_id;
            } else {
                /********************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW THEIR BRANCH TAXES       *
                /********************************************************************/

                $branchId = $model_id;
            }

            $branch = CompanyBranch::where('id', $branchId)->first();

            if ($branch) {
                $taxes = $branch->taxes();
            } else {
                return oq_api_notify_error('Specified branch was not found', null, 404);
            }

            /*  ALL taxes
             *  If the user wants all the taxes in the system. They must indicate
             *  using the query "model" set to "all". This is normaly used by authorized
             *  superadmins to access all taxes resources in the system.
             */
        } elseif ($modelType == 'all') {
            /********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL TAXES    *
            /********************************************************/

            /*   Create a new Tax() instance that can be used to retrieve all taxes
             */
            $taxes = new Tax();
        } else {
            return oq_api_notify_error('Model query must be "company" or "branch". Provided model is incorrect', null, 404);
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

        $order_join = 'taxes';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $taxes = $taxes->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $taxes = $taxes->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query
                $taxes = $taxes->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any products or services so far
            if ($taxes) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $taxes->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($taxes, 200);
    }
}
