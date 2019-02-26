<?php

namespace App\Http\Controllers\Api;

use App\Phone;
use App\Company;
use Illuminate\Http\Request;
use App\Notifications\CompanyUpdated;
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
        $type = request('type');                      //  e.g) client or supplier

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

            /*  If user indicated to only return supplier dierctories
            */
            } elseif ($type == 'supplier') {
                $companies = $model->suppliers();

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

    public function show($company_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE COMPANY    *
         ******************************************************/

        try {
            //  refetch the updated company
            $company = Company::find($company_id);

            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $company->load(oq_url_to_array(request('connections')));
            }

            //  If the company was found successfully
            if ($company) {
                //  Action was executed successfully
                return oq_api_notify($company, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function settings(Request $request, $company_id)
    {
        try {
            //  Get the associated company
            $company = Company::where('id', $company_id)->first();

            //  If we have any company so far
            if (count($company)) {
                //  Get the company settings
                $settings = $company->settings;

                //  Action was executed successfully
                return oq_api_notify($settings, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function store(Request $request)
    {
        //  Start creating the company
        $companyInstance = new Company();
        $data = $companyInstance->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the company was created successfully
        if ($success) {
            //  If this is a success then we have a company returned
            $company = $response;

            //  Get any associated phones if any
            $phones = request('company')['phones'];
            if (isset($phones)) {
                //  Add new phone numbers
                $phoneInstance = new Phone();

                $data = $phoneInstance->addAndReplace($company, $phones);
                $success = $data['success'];
                $phones = $data['response'];
            }

            //  Action was executed successfully
            return oq_api_notify($company, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update(Request $request, $company_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $company = request('company');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE COMPANY    *
         ******************************************************/

        /*********************************************
         *   VALIDATE COMPANY INFORMATION            *
         ********************************************/

        if (!empty($company)) {
            try {
                $template = [
                    'name' => $company['name'],
                    'description' => $company['description'],
                    'date_of_incorporation' => $company['date_of_incorporation'],
                    'type' => $company['type'],
                    'address' => $company['address'],
                    'country' => $company['country'],
                    'provience' => $company['provience'],
                    'city' => $company['city'],
                    'postal_or_zipcode' => $company['postal_or_zipcode'],
                    'email' => $company['email'],
                    'additional_email' => $company['additional_email'],
                    'website_link' => $company['website_link'],
                    'facebook_link' => $company['facebook_link'],
                    'twitter_link' => $company['twitter_link'],
                    'linkedin_link' => $company['linkedin_link'],
                    'instagram_link' => $company['instagram_link'],
                    'bio' => $company['bio'],
                ];

                //  Update the company
                $company = Company::where('id', $company_id)->update($template);

                //  If the company was updated successfully
                if ($company) {
                    //  refetch the updated company
                    $company = Company::find($company_id);

                    /*****************************
                     *   SEND NOTIFICATIONS      *
                     *****************************/

                    $user->notify(new CompanyUpdated($company));

                    //  Record activity of a company created
                    $status = 'updated';

                    $companyUpdatedActivity = oq_saveActivity($company, $user, $status, $template);

                    $company = Company::find($company_id);

                    //  Eager load other relationships wanted if specified
                    if (request('connections')) {
                        $company->load(oq_url_to_array(request('connections')));
                    }
                }

                //  If the company was updated successfully
                if ($company) {
                    //  Action was executed successfully
                    return oq_api_notify($company, 200);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }

    public function getWallets(Request $request, $company_id)
    {
        try {
            //  Get the associated company
            $company = Company::where('id', $company_id)->first();

            //  If we have any company so far
            if (count($company)) {
                //  Get the company wallets
                $wallets = $company->wallets;

                //  Action was executed successfully
                return oq_api_notify($wallets, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function delete($company_id)
    {
    }
}
