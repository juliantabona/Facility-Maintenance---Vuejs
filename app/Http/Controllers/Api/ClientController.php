<?php

namespace App\Http\Controllers\Api;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = null;
        $model_id = request('id');
        $model_type = request('model', 'CompanyBranch');

        $user = auth('api')->user();

        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW CLIENTS         *
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

                //  Create a new company/Company
                $model = new $DynamicModel();

                $clients = $model->where('id', $model_id)->first()->clients();

                try {
                    //  Get all and trashed
                    if (request('withtrashed') == 1) {
                        //  Run query
                        $clients = $clients->withTrashed()->advancedFilter(['order_join' => $order_join]);
                    //  Get only trashed
                    } elseif (request('onlytrashed') == 1) {
                        //  Run query
                        $clients = $clients->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
                    //  Get all except trashed
                    } else {
                        //  Run query
                        $clients = $clients->advancedFilter(['order_join' => $order_join]);
                    }

                    //  If we have any clients so far
                    if (count($clients)) {
                        //  Eager load other relationships wanted if specified
                        if (request('connections')) {
                            $clients->load(oq_url_to_array(request('connections')));
                        }
                    }
                } catch (\Exception $e) {
                    return oq_api_notify_error('Query Error', $e->getMessage(), 404);
                }

                if (count($clients)) {
                    //  Eager load other relationships wanted if specified
                    if (request('connections')) {
                        $clients->load(oq_url_to_array(request('connections')));
                    }

                    //  Action was executed successfully
                    return oq_api_notify($clients, 200);
                }
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

    public function show($company_id)
    {
    }

    public function clientJobcards(Request $request, $client_id)
    {
        $user = auth('api')->user();

        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW JOBCARDS        *
        /**********************************************************/

        //  We start with no jobcards
        $jobcards = [];

        $jobcards = Company::where('id', $client_id)->first()->jobcards();

        $order_join = 'jobcards';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $jobcards = $jobcards->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $jobcards = $jobcards->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query
                $jobcards = $jobcards->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any jobcards so far
            if (count($jobcards)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $jobcards->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($jobcards, 200);
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
