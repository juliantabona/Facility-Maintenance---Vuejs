<?php

namespace App\Http\Controllers\Api;

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

            //  Action was executed successfully
            return oq_api_notify($company, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($company_id)
    {
        //  Company Instance
        $data = ( new Company() )->initiateUpdate($company_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the company was updated successfully
        if ($success) {
            //  If this is a success then we have a company returned
            $company = $response;

            //  Action was executed successfully
            return oq_api_notify($company, 200);
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

    public function getClients(Request $request, $company_id)
    {
        try {
            //  Get the associated company clients
            $company = Company::where('id', $company_id)->first();
            $companyClients = $company->companyClients();
            $userClients = $company->userClients();

            //  If we have any company so far
            
            $count = request('count');

            if (isset($count) && $count == 1) {
                //  Get the company wallets
                $companyClients = $companyClients->count();
                $userClients = $userClients->count();
            }

            $response = [
                'users' => $userClients,
                'companies' => $companyClients,
                'total' => $companyClients + $userClients
            ];

            //  Action was executed successfully
            return oq_api_notify($response, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }


    public function getLogo(Request $request, $company_id)
    {
        try {
            //  Get the associated company
            $company = Company::where('id', $company_id)->first();
            $companyLogo = $company->logo;

            //  Action was executed successfully
            return oq_api_notify($companyLogo, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function getWallets(Request $request, $company_id)
    {
        try {
            //  Get the associated company
            $company = Company::where('id', $company_id)->first();

            //  If we have any company so far
            if ($company) {
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

    public function settings(Request $request, $company_id)
    {
        try {
            //  Get the associated company
            $company = Company::where('id', $company_id)->first();

            //  If we have any company so far
            if ($company) {
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

    public function delete($company_id)
    {
    }
}
