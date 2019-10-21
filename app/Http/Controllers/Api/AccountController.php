<?php

namespace App\Http\Controllers\Api;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getAccounts()
    {
        //  Check if the user is authourized to view all accounts
        if ($this->user->can('viewAll', Account::class)) {
        
            //  Get the accounts
            $accounts = Account::paginate();

            //  Check if the accounts exist
            if ($accounts) {

                //  Return an API Readable Format of the Account Instance
                return ( new \App\Account )->convertToApiFormat($accounts);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getAccount($account_id)
    {
        //  Get the account
        $account = Account::where('id', $account_id)->first() ?? null;

        //  Check if the account exists
        if ($account) {

            //  Check if the user is authourized to view the account
            if ($this->user->can('view', $account)) {

                //  Return an API Readable Format of the Account Instance
                return $account->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getAccountSettings($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account settings
        $settings = $account->settings ?? null;

        //  Check if the settings exist
        if ($settings) {

            //  Check if the user is authourized to view the account settings
            if ($this->user->can('view', $account)) {

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
    public function getAccountLogo($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account logo
        $logo = $account->logo ?? null;

        //  Check if the logo exists
        if ($logo) {

            //  Check if the user is authourized to view the account logo
            if ($this->user->can('view', $account)) {

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

    public function getAccountDocuments($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account documents
        $documents = $account->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the account documents
            if ($this->user->can('view', $account)) {

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

    public function getAccountDocument($account_id, $document_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account document
        $document = $account->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the account document
            if ($this->user->can('view', $account)) {

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

    public function getAccountPhones($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account phones
        $phones = $account->phones()->paginate() ?? null;

        //  Check if the phones exist
        if ($phones) {

            //  Check if the user is authourized to view the account phones
            if ($this->user->can('view', $account)) {

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

    public function getAccountPhone($account_id, $phone_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account phone
        $phone = $account->phones()->where('id', $phone_id)->first() ?? null;

        //  Check if the phone exists
        if ($phone) {

            //  Check if the user is authourized to view the account phone
            if ($this->user->can('view', $account)) {

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

    public function getAccountUsers($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account users
        $users = $account->users()->paginate() ?? null;

        //  Check if the users exist
        if ($users) {

            //  Check if the user is authourized to view the account users
            if ($this->user->can('view', $account)) {

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

    public function getAccountAdmins($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account admins
        $admins = $account->admins()->paginate() ?? null;

        //  Check if the admins exist
        if ($admins) {

            //  Check if the user is authourized to view the account admins
            if ($this->user->can('view', $account)) {

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

    public function getAccountStaff($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account staff
        $staff = $account->staff()->paginate() ?? null;

        //  Check if the staff exists
        if ($staff) {

            //  Check if the user is authourized to view the account staff
            if ($this->user->can('view', $account)) {

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

    public function getAccountUserCustomers($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account user customers
        $userCustomers = $account->userCustomers()->paginate() ?? null;

        //  Check if the user customers exist
        if ($userCustomers) {

            //  Check if the user is authourized to view the account user customers
            if ($this->user->can('view', $account)) {

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

    public function getAccountUserVendors($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account user vendors
        $userVendors = $account->userVendors()->paginate() ?? null;

        //  Check if the user vendors exist
        if ($userVendors) {

            //  Check if the user is authourized to view the account user vendors
            if ($this->user->can('view', $account)) {

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

    public function getAccountUser($account_id, $user_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account phone
        $user = $account->users()->where('users.id', $user_id)->first() ?? null;

        //  Check if the user exists
        if ($user) {

            //  Check if the user is authourized to view the account user
            if ($this->user->can('view', $account)) {

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

    public function getAccountProducts($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account products
        $products = $account->products()->paginate() ?? null;

        //  Check if the products exist
        if ($products) {

            //  Check if the user is authourized to view the account products
            if ($this->user->can('view', $account)) {

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

    public function getAccountProduct($account_id, $product_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account product
        $product = $account->products()->where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the account product
            if ($this->user->can('view', $account)) {

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

    public function getAccountTaxes($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account taxes
        $taxes = $account->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the account taxes
            if ($this->user->can('view', $account)) {

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

    public function getAccountTax($account_id, $tax_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account tax
        $tax = $account->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the account tax
            if ($this->user->can('view', $account)) {

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

    public function getAccountDiscounts($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account discounts
        $discounts = $account->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the account discounts
            if ($this->user->can('view', $account)) {

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

    public function getAccountDiscount($account_id, $discount_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account discount
        $discount = $account->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the account discount
            if ($this->user->can('view', $account)) {

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

    public function getAccountCoupons($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account coupons
        $coupons = $account->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the account coupons
            if ($this->user->can('view', $account)) {

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

    public function getAccountCoupon($account_id, $coupon_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account coupons
        $coupons = $account->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the account coupons
            if ($this->user->can('view', $account)) {

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

    public function getAccountStores($account_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account stores
        $stores = $account->stores()->paginate() ?? null;

        //  Check if the stores exist
        if ($stores) {

            //  Check if the user is authourized to view the account stores
            if ($this->user->can('view', $account)) {

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

    public function getAccountStore($account_id, $store_id)
    {
        //  Get the account
        $account = Account::findOrFail($account_id);

        //  Get the account store
        $store = $account->stores()->where('id', $store_id)->first() ?? null;

        //  Check if the store exists
        if ($store) {

            //  Check if the user is authourized to view the account store
            if ($this->user->can('view', $account)) {

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
        $data = ( new Account() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the accounts were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of accounts
            $accounts = $response;

            //  Action was executed successfully
            return oq_api_notify($accounts, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the account
        $accountInstance = new Account();
        $data = $accountInstance->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the account was created successfully
        if ($success) {
            //  If this is a success then we have a account returned
            $account = $response;

            //  Action was executed successfully
            return oq_api_notify($account, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($account_id)
    {
        //  Account Instance
        $data = ( new Account() )->initiateUpdate($account_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the account was updated successfully
        if ($success) {
            //  If this is a success then we have a account returned
            $account = $response;

            //  Action was executed successfully
            return oq_api_notify($account, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($account_id)
    {
        //  Current authenticated account
        $account = auth('api')->account();

        try {
            //  refetch the updated account
            $account = Account::findOrFail($account_id);

            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $account->load(oq_url_to_array(request('connections')));
            }

            //  If the account was found successfully
            if ($account) {
                //  Action was executed successfully
                return oq_api_notify($account, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function approve($account_id)
    {
        //  Account Instance
        $data = ( new Account() )->initiateApprove($account_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the account was approved successfully
        if ($success) {
            //  If this is a success then we have the account
            $account = $response;

            //  Action was executed successfully
            return oq_api_notify($account, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getStaff()
    {
        //  Invoice Instance
        $data = ( new Account() )->initiateGetStaff();
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

    public function getClients(Request $request, $account_id)
    {
        try {
            //  Get the associated account clients
            $account = Account::where('id', $account_id)->first();
            $accountClients = $account->accountClients();
            $accountClients = $account->accountClients();

            //  If we have any account so far

            $count = request('count');

            if (isset($count) && $count == 1) {
                //  Get the account wallets
                $accountClients = $accountClients->count();
                $accountClients = $accountClients->count();
            }

            $response = [
                'accounts' => $accountClients,
                'accounts' => $accountClients,
                'total' => $accountClients + $accountClients
            ];

            //  Action was executed successfully
            return oq_api_notify($response, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function getLogo(Request $request, $account_id)
    {
        try {
            //  Get the associated account
            $account = Account::where('id', $account_id)->first();
            $accountLogo = $account->logo;

            //  Action was executed successfully
            return oq_api_notify($accountLogo, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function getWallets(Request $request, $account_id)
    {
        try {
            //  Get the associated account
            $account = Account::where('id', $account_id)->first();

            //  If we have any account so far
            if ($account) {
                //  Get the account wallets
                $wallets = $account->wallets;

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
        //  Start creating the account
        $data = ( new Account() )->getStatistics();
        $success = $data['success'];
        $response = $data['response'];

        //  If the account statistics were found successfully
        if ($success) {
            //  If this is a success then we have the statistics returned
            $stats = $response;

            //  Action was executed successfully
            return oq_api_notify($stats, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function settings(Request $request, $account_id)
    {
        try {
            //  Get the associated account
            $account = Account::where('id', $account_id)->first();

            //  If we have any account so far
            if ($account) {
                //  Get the account settings
                $settings = $account->settings;

                //  Action was executed successfully
                return oq_api_notify($settings, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function delete($account_id)
    {
    }

    */
}
