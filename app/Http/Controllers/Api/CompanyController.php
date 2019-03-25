<?php

namespace App\Http\Controllers\Api;

use App\Phone;
use App\Company;
use Illuminate\Http\Request;
use App\Notifications\CompanyUpdated;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
        //  Invoice Instance
        $data = ( new Company() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the companies were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of companies
            $companies = $response;

            //  Action was executed successfully
            return oq_api_notify($companies, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getStaff()
    {
        //  Invoice Instance
        $data = ( new Company() )->initiateGetStaff();
        $success = $data['success'];
        $response = $data['response'];

        //  If the staff were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of staff
            $staff = $response;

            //  Action was executed successfully
            return oq_api_notify($staff, 200);
        }

        //  If the data was not a success then return the response
        return $response;
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

    public function approve($company_id)
    {
        //  Company Instance
        $data = ( new Company() )->initiateApprove($company_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the company was approved successfully
        if ($success) {
            //  If this is a success then we have the company
            $company = $response;

            //  Action was executed successfully
            return oq_api_notify($company, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getEstimatedStats()
    {
        //  Start creating the company
        $data = ( new Company() )->getStatistics();
        $success = $data['success'];
        $response = $data['response'];

        //  If the company statistics were found successfully
        if ($success) {
            //  If this is a success then we have the statistics returned
            $stats = $response;

            //  Action was executed successfully
            return oq_api_notify($stats, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function delete($company_id)
    {
    }
}
