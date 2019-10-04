<?php

namespace App\Http\Controllers\Api;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getCompanies()
    {
        //  Check if the user is authourized to view all companies
        if ($this->user->can('viewAll', Company::class)) {
        
            //  Get the companies
            $companies = Company::paginate();

            //  Check if the companies exist
            if ($companies) {

                //  Return an API Readable Format of the Company Instance
                return ( new \App\Company )->convertToApiFormat($companies);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getCompany($company_id)
    {
        //  Get the company
        $company = Company::where('id', $company_id)->first() ?? null;

        //  Check if the company exists
        if ($company) {

            //  Check if the user is authourized to view the company
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Company Instance
                return $company->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanySettings($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company settings
        $settings = $company->settings ?? null;

        //  Check if the settings exist
        if ($settings) {

            //  Check if the user is authourized to view the company settings
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Setting Instance
                return $settings->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DOCUMENT RELATED RESOURCES   *
    *********************************/
    public function getCompanyLogo($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company logo
        $logo = $company->logo ?? null;

        //  Check if the logo exists
        if ($logo) {

            //  Check if the user is authourized to view the company logo
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Document Instance
                return $logo->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyDocuments($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company documents
        $documents = $company->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the company documents
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document )->convertToApiFormat($documents);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyDocument($company_id, $document_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company document
        $document = $company->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the company document
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Document Instance
                return $document->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  PHONE RELATED RESOURCES      *
    *********************************/

    public function getCompanyPhones($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company phones
        $phones = $company->phones()->paginate() ?? null;

        //  Check if the phones exist
        if ($phones) {

            //  Check if the user is authourized to view the company phones
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Phone Instance
                return ( new \App\Phone )->convertToApiFormat($phones);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyPhone($company_id, $phone_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company phone
        $phone = $company->phones()->where('id', $phone_id)->first() ?? null;

        //  Check if the phone exists
        if ($phone) {

            //  Check if the user is authourized to view the company phone
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Phone Instance
                return $phone->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  USER RELATED RESOURCES       *
    *********************************/

    public function getCompanyUsers($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company users
        $users = $company->users()->paginate() ?? null;

        //  Check if the users exist
        if ($users) {

            //  Check if the user is authourized to view the company users
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User )->convertToApiFormat($users);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyAdmins($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company admins
        $admins = $company->admins()->paginate() ?? null;

        //  Check if the admins exist
        if ($admins) {

            //  Check if the user is authourized to view the company admins
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User )->convertToApiFormat($admins);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyStaff($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company staff
        $staff = $company->staff()->paginate() ?? null;

        //  Check if the staff exists
        if ($staff) {

            //  Check if the user is authourized to view the company staff
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User )->convertToApiFormat($staff);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyUserCustomers($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company user customers
        $userCustomers = $company->userCustomers()->paginate() ?? null;

        //  Check if the user customers exist
        if ($userCustomers) {

            //  Check if the user is authourized to view the company user customers
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User )->convertToApiFormat($userCustomers);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyUserVendors($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company user vendors
        $userVendors = $company->userVendors()->paginate() ?? null;

        //  Check if the user vendors exist
        if ($userVendors) {

            //  Check if the user is authourized to view the company user vendors
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User )->convertToApiFormat($userVendors);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyUser($company_id, $user_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company phone
        $user = $company->users()->where('users.id', $user_id)->first() ?? null;

        //  Check if the user exists
        if ($user) {

            //  Check if the user is authourized to view the company user
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the User Instance
                return $user->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  PRODUCT RELATED RESOURCES    *
    *********************************/

    public function getCompanyProducts($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company products
        $products = $company->products()->paginate() ?? null;

        //  Check if the products exist
        if ($products) {

            //  Check if the user is authourized to view the company products
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Product Instance
                return ( new \App\Product )->convertToApiFormat($products);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyProduct($company_id, $product_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company product
        $product = $company->products()->where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the company product
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Product Instance
                return $product->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  TAX RELATED RESOURCES        *
    *********************************/

    public function getCompanyTaxes($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company taxes
        $taxes = $company->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the company taxes
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Tax Instance
                return  ( new \App\Tax )->convertToApiFormat($taxes);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyTax($company_id, $tax_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company tax
        $tax = $company->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the company tax
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Tax Instance
                return $tax->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DISCOUNT RELATED RESOURCES   *
    *********************************/

    public function getCompanyDiscounts($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company discounts
        $discounts = $company->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the company discounts
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Discount Instance
                return  ( new \App\Discount )->convertToApiFormat($discounts);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyDiscount($company_id, $discount_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company discount
        $discount = $company->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the company discount
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Discount Instance
                return $discount->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  COUPONS RELATED RESOURCES    *
    *********************************/

    public function getCompanyCoupons($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company coupons
        $coupons = $company->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the company coupons
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Coupon Instance
                return  ( new \App\Coupon )->convertToApiFormat($coupons);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyCoupon($company_id, $coupon_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company coupons
        $coupons = $company->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the company coupons
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Coupon Instance
                return $coupons->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  STORE RELATED RESOURCES      *
    *********************************/

    public function getCompanyStores($company_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company stores
        $stores = $company->stores()->paginate() ?? null;

        //  Check if the stores exist
        if ($stores) {

            //  Check if the user is authourized to view the company stores
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Store Instance
                return  ( new \App\Store )->convertToApiFormat($stores);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCompanyStore($company_id, $store_id)
    {
        //  Get the company
        $company = Company::findOrFail($company_id);

        //  Get the company store
        $store = $company->stores()->where('id', $store_id)->first() ?? null;

        //  Check if the store exists
        if ($store) {

            //  Check if the user is authourized to view the company store
            if ($this->user->can('view', $company)) {

                //  Return an API Readable Format of the Store Instance
                return $store->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*

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
        //  Current authenticated company
        $company = auth('api')->company();

        try {
            //  refetch the updated company
            $company = Company::findOrFail($company_id);

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
            $companyClients = $company->companyClients();

            //  If we have any company so far

            $count = request('count');

            if (isset($count) && $count == 1) {
                //  Get the company wallets
                $companyClients = $companyClients->count();
                $companyClients = $companyClients->count();
            }

            $response = [
                'companys' => $companyClients,
                'companies' => $companyClients,
                'total' => $companyClients + $companyClients
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

    */
}
