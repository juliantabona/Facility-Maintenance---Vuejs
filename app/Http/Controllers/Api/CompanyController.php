<?php

namespace App\Http\Controllers\Api;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no jobcards
        $companies = [];

        //  Query data
        $modelType = request('model', 'branch');      //  e.g) company, branch
        $type = request('type');                      //  e.g) client or contractor

        /*  First thing is first, we need to understand one of 3 scenerios, Either we want:
         *
         *  1) Only company directory for a related COMPANY of the authenticated user
         *  2) Only company directory for a related BRANCH of the authenticated user
         *  3) Only client directory for a related COMPANY of the authenticated user
         *  4) Only client directory for a related BRANCH of the authenticated user
         *  5) Only supplier directory for a related COMPANY of the authenticated user
         *  6) Only supplier directory for a related BRANCH of the authenticated user
         *
         *  Once we have those companies we will determine whether we want any of the following
         *
         *  1) All companies aswell as the trashed ones
         *  2) Only companies that are trashed
         *  3) Only companies that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific directory
         *  If the user indicated that they want company directory listings in
         *  relation to their company. They must indicate using the query
         *  "model" set to "company".
         */
        if ($modelType == 'company') {
            /******************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW COMPANY DIRECTORIES    *
            /*****************************************************************/

            $model = $user->companyBranch->company()->first();

        /*  User Branch specific directory
         *  If the user indicated that they want company directory listings in
         *  relation to their branch. They must indicate using the query
         *  "model" set to "branch".
         */
        } elseif ($modelType == 'branch') {
            /******************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW BRANCH DIRECTORIES     *
            /*****************************************************************/

            $model = $user->companyBranch()->first();

        /*  For ALL directories
         *  If the user indicated that they all drirectories. They must indicate using the
         *  query "model" set to "all".
         */
        } elseif ($modelType == 'all') {
            /******************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL DIRECTORIES        *
            /*****************************************************************/

            $companies = new Company();
        }

        if ($modelType != 'all') {
            /*  If user indicated to only return client dierctories
            */
            if ($type == 'client') {
                $companies = $model->clients();

            /*  If user indicated to only return contractor dierctories
            */
            } elseif ($type == 'contractor') {
                $companies = $model->contractors();

            /*  If user did not indicate any specific group
            */
            } else {
                $companies = $model->companyDirectory();
            }
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

        $order_join = 'companies';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $companies = $companies->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $companies = $companies->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query
                $companies = $companies->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any companies so far
            if (count($companies)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $companies->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($companies, 200);
    }

    public function show($model_id)
    {
        $clients = [];
        $model_type = request('model', 'CompanyBranch');

        $user = auth('api')->user();

        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW CLIENT          *
        /**********************************************************/

        //  If we have the model and id specified
        if (!empty($model_id)) {
            /*
             *  Get the associated model otherwise default to the "CompanyBranch" model.
             */

            $DynamicModel = '\\App\\'.$model_type;

            /*
             *  Lets make sure that the class does exist.
             *  Also make sure that the model can only be a "Company" or "CompanyBranch" resource
             */
            if (class_exists($DynamicModel) && in_array($model_type, ['Company', 'CompanyBranch'])) {
                //  Create a new Company/CompanyBranch instance
                $model = new $DynamicModel();

                if ($model_type == 'CompanyBranch') {
                    $client = $model->where('id', $model_id)->first()->company();
                } else {
                    $client = $model->where('id', $model_id);
                }

                return $client->first();

                try {
                    //  Get all and trashed
                    if (request('withtrashed') == 1) {
                        //  Run query
                        $client = $client->withTrashed()->first();
                    //  Get only trashed
                    } elseif (request('onlytrashed') == 1) {
                        //  Run query
                        $client = $client->onlyTrashed()->first();
                    //  Get all except trashed
                    } else {
                        //  Run query
                        $client = $client->first();
                    }

                    //  If we have any client so far
                    if (count($client)) {
                        //  Eager load other relationships wanted if specified
                        if (request('connections')) {
                            $client->load(oq_url_to_array(request('connections')));
                        }

                        //  Action was executed successfully
                        return oq_api_notify($client, 200);
                    }
                } catch (\Exception $e) {
                    return oq_api_notify_error('Query Error', $e->getMessage(), 404);
                }

                //  No resource found
                return oq_api_notify_no_resource();
            } else {
                //  No such class, the user provided incorrect details
                return oq_api_notify_error('Class does not exist. Only company or companyBranch are accepted as models', null, 404);
            }
        } else {
            //  No branch id specified error
            return oq_api_notify_error('include branch or company id', null, 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $company_id)
    {
    }

    public function delete($company_id)
    {
    }
}
