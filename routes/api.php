<?php

use Twilio as Twilio;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
    HTTP Status Codes and the Response Format - FOR MY OWN GOOD :)

    200: OK. The standard success code and default option.
    201: Object created. Useful for the store actions.
    204: No content. When an action was executed successfully, but there is no content to return.
    206: Partial content. Useful when you have to return a paginated list of resources.
    400: Bad request. The standard option for requests that fail to pass validation.
    401: Unauthorized. The user needs to be authenticated.
    403: Forbidden. The user is authenticated, but does not have the permissions to perform an action.
    404: Not found. Usually returned automatically by Laravel when the resource is not found.
    405: Indicates that the request method is known by the server but is not supported by the target resource e.g) GET, POST, PUT, DELETE
    500: Internal server error. Ideally you're not going to be explicitly returning this, but if something unexpected breaks, this is what your user is going to receive.
    503: Service unavailable. Pretty self explanatory, but also another code that is not going to be returned explicitly by the application.
*/

/*   AUTH ROUTES
     -  Login
     -  Register
     -  Reset Password
     -  Account Activation
     -  Get Authenticated User
    -   Logout
*/

/*****************************************************
 * ONLY AUTHENTICATED USER ROUTES                    *
 *****************************************************
 * THE Authenticated user is only allowed a maximum of
 * 3 calls per minute on the listed API routes so as
 * to control and limit excessive traffic requests.
 */
Route::group(['middleware' => ['auth:api', 'throttle:60,1']], function () {

    /*********************************
    /*********************************
     *  API HOME ROUTES              *
    /*********************************
    *********************************/
    
    Route::get('/', 'Api\HomeController@home')->name('home');

    /*********************************
    /*********************************
     *  MY ACCOUNT ROUTES            *
    /*********************************
    *********************************/

    Route::prefix('me')->name('my-')->group(function () {

        Route::get('/', 'Api\UserController@getUser')->name('profile');

        //  Settings related resources
        Route::get('/settings', 'Api\UserController@getUserSettings')->name('settings');

        //  Document related resources
        Route::get('/picture', 'Api\UserController@getUserPicture')->name('picture');
        Route::get('/documents', 'Api\UserController@getUserDocuments')->name('documents');
        Route::get('/documents/{document_id}', 'Api\UserController@getUserDocument')->name('document')->where('document_id', '[0-9]+');

        //  Phone related resources
        Route::get('/phones', 'Api\UserController@getUserPhones')->name('phones');
        Route::get('/phones/{phone_id}', 'Api\UserController@getUserPhone')->name('phone')->where('phone_id', '[0-9]+');

        //  Account related resources
        Route::get('/accounts', 'Api\UserController@getUserAccounts')->name('accounts');
        Route::get('/accounts/{account_id}', 'Api\UserController@getUserAccount')->name('account')->where('account_id', '[0-9]+');

        //  Store related resources
        Route::get('/stores', 'Api\UserController@getUserStores')->name('stores');
        Route::get('/stores/{store_id}', 'Api\UserController@getUserStore')->name('store')->where('store_id', '[0-9]+');

    });

    /*********************************
    /*********************************
     *  USER RESOURCE ROUTES         *
    /*********************************
    *********************************/

    Route::prefix('users')->group(function () {

        //  Multiple users
        Route::get('/', 'Api\UserController@getUsers')->name('users');

        //  Single user
        Route::get('/{user_id}', 'Api\UserController@getUser')->name('user')->where('user_id', '[0-9]+');

        //  Single account resources
        Route::prefix('{user_id}')->name('user-')->group(function ($group) {
            //  Allow only intergers for user_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('user_id', '[0-9]+');
            }

            //  Settings related resources
            Route::get('/settings', 'Api\UserController@getUserSettings')->name('settings');

            //  Document related resources
            Route::get('/picture', 'Api\UserController@getUserPicture')->name('picture');
            Route::get('/documents', 'Api\UserController@getUserDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\UserController@getUserDocument')->name('document')->where('document_id', '[0-9]+');

            //  Phone related resources
            Route::get('/phones', 'Api\UserController@getUserPhones')->name('phones');
            Route::get('/phones/{phone_id}', 'Api\UserController@getUserPhone')->name('phone')->where('phone_id', '[0-9]+');

            //  Account related resources
            Route::get('/accounts', 'Api\UserController@getUserAccounts')->name('accounts');
            Route::get('/accounts/{account_id}', 'Api\UserController@getUserAccount')->name('account')->where('account_id', '[0-9]+');

            //  Store related resources
            Route::get('/stores', 'Api\UserController@getUserStores')->name('stores');
            Route::get('/stores/{store_id}', 'Api\UserController@getUserStore')->name('store')->where('store_id', '[0-9]+');
        });

    });

    /*********************************
    /*********************************
     *  COMPANY RESOURCE ROUTES      *
    /*********************************
    *********************************/

    Route::prefix('accounts')->group(function () {

        //  Multiple accounts
        Route::get('/', 'Api\AccountController@getAccounts')->name('accounts');

        //  Single account
        Route::get('/{account_id}', 'Api\AccountController@getAccount')->name('account')->where('account_id', '[0-9]+');

        //  Single account resources
        Route::prefix('{account_id}')->name('account-')->group(function ($group) {

            //  Allow only intergers for account_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('account_id', '[0-9]+');
            }

            //  Settings related resources
            Route::get('/settings', 'Api\AccountController@getAccountSettings')->name('settings');

            //  Document related resources
            Route::get('/logo', 'Api\AccountController@getAccountLogo')->name('logo');
            Route::get('/documents', 'Api\AccountController@getAccountDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\AccountController@getAccountDocument')->name('document')->where('document_id', '[0-9]+');

            //  Phone related resources
            Route::get('/phones', 'Api\AccountController@getAccountPhones')->name('phones');
            Route::get('/phones/{phone_id}', 'Api\AccountController@getAccountPhone')->name('phone')->where('phone_id', '[0-9]+');

            //  User related resources
            Route::get('/users', 'Api\AccountController@getAccountUsers')->name('users');
            Route::get('/users/admins', 'Api\AccountController@getAccountAdmins')->name('admins');
            Route::get('/users/staff', 'Api\AccountController@getAccountStaff')->name('staff');
            Route::get('/users/customers', 'Api\AccountController@getAccountuserCustomers')->name('user-customers');
            Route::get('/users/vendors', 'Api\AccountController@getAccountUserVendors')->name('user-vendors');
            Route::get('/users/{user_id}', 'Api\AccountController@getAccountUser')->name('user')->where('user_id', '[0-9]+');

            //  Product related resources
            Route::get('/products', 'Api\AccountController@getAccountProducts')->name('products');
            Route::get('/products/{product_id}', 'Api\AccountController@getAccountProduct')->name('product')->where('product_id', '[0-9]+');

            //  Tax related resources
            Route::get('/taxes', 'Api\AccountController@getAccountTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\AccountController@getAccountTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\AccountController@getAccountDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\AccountController@getAccountDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\AccountController@getAccountCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\AccountController@getAccountCoupon')->name('coupon')->where('coupon_id', '[0-9]+');

            //  Store related resources
            Route::get('/stores', 'Api\AccountController@getAccountStores')->name('stores');
            Route::get('/stores/{store_id}', 'Api\AccountController@getAccountStore')->name('store')->where('store_id', '[0-9]+');

        });

    });

    //Route::get('accounts/stats', 'Api\AccountController@getEstimatedStats');
    //Route::post('accounts/{account_id}/approve', 'Api\AccountController@approve');
    //Route::get('accounts/{account_id}/wallets', 'Api\AccountController@getWallets');
    //Route::get('accounts/{account_id}/customers', 'Api\AccountController@getCustomers');

    /*********************************
    /*********************************
     *  STORE RESOURCE ROUTES        *
    /*********************************
    *********************************/

    Route::prefix('stores')->group(function () {

        //  Multiple stores
        Route::get('/', 'Api\StoreController@getStores')->name('stores');

        //  Single store
        Route::get('/{store_id}', 'Api\StoreController@getStore')->name('store')->where('store_id', '[0-9]+');

        //  Single store resources
        Route::prefix('{store_id}')->name('store-')->group(function ($group) {

            //  Allow only intergers for store_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('store_id', '[0-9]+');
            }

            //  Account related resources
            Route::get('/account', 'Api\StoreController@getStoreAccount')->name('account');

            //  Document related resources
            Route::get('/logo', 'Api\StoreController@getStoreLogo')->name('logo');
            Route::get('/documents', 'Api\StoreController@getStoreDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\StoreController@getStoreDocument')->name('document')->where('document_id', '[0-9]+');

            //  Phone related resources
            Route::get('/phones', 'Api\StoreController@getStorePhones')->name('phones');
            Route::get('/phones?type=mobile', 'Api\StoreController@getStorePhones')->name('mobiles');
            Route::get('/phones?type=tel', 'Api\StoreController@getStorePhones')->name('telephones');
            Route::get('/phones?type=fax', 'Api\StoreController@getStorePhones')->name('fax');
            Route::get('/phones/{phone_id}', 'Api\StoreController@getStorePhone')->name('phone')->where('phone_id', '[0-9]+');

            //  Address related resources
            Route::get('/addresses', 'Api\StoreController@getStoreAddresses')->name('addresses');
            Route::get('/addresses/{address_id}', 'Api\StoreController@getStoreAddress')->name('phone')->where('address_id', '[0-9]+');

            //  Address related resources
            Route::get('/emails', 'Api\StoreController@getStoreEmails')->name('emails');
            Route::get('/emails/{email_id}', 'Api\StoreController@getStoreEmail')->name('phone')->where('email_id', '[0-9]+');

            //  Contact related resources
            Route::get('/contacts', 'Api\StoreController@getStoreContacts')->name('contacts');
            Route::get('/contacts?withMobile=true', 'Api\StoreController@getStoreContacts')->name('contacts-with-mobiles');
            Route::get('/contacts?type=customer', 'Api\StoreController@getStoreContacts')->name('customer-contacts');
            Route::get('/contacts?type=customer&withMobile=true', 'Api\StoreController@getStoreContacts')->name('customer-contacts-with-mobiles');
            Route::get('/contacts?type=vendor', 'Api\StoreController@getStoreContacts')->name('vendor-contacts');
            Route::get('/contacts?type=vendor&withMobile=true', 'Api\StoreController@getStoreContacts')->name('vendor-contacts-with-mobiles');
            Route::get('/contacts/{contact_id}', 'Api\StoreController@getStoreContact')->name('contact')->where('contact_id', '[0-9]+');

            //  User related resources
            Route::get('/users', 'Api\StoreController@getStoreUsers')->name('users');
            Route::get('/users/admins', 'Api\StoreController@getStoreAdmins')->name('admins');
            Route::get('/users/staff', 'Api\StoreController@getStoreStaff')->name('staff');
            Route::get('/users/{user_id}', 'Api\StoreController@getStoreUser')->name('user')->where('user_id', '[0-9]+');

            //  USSD Interface related resources
            Route::get('/ussd-interface', 'Api\StoreController@getStoreUssdInterface')->name('ussd-interface');

            //  Product related resources
            Route::get('/products', 'Api\StoreController@getStoreProducts')->name('products');
            Route::get('/products/{product_id}', 'Api\StoreController@getStoreProduct')->name('product')->where('product_id', '[0-9]+');

            //  Order related resources
            Route::get('/orders', 'Api\StoreController@getStoreOrders')->name('orders');
            Route::get('/orders/{order_id}', 'Api\StoreController@getStoreOrder')->name('order')->where('order_id', '[0-9]+');

            //  Tax related resources
            Route::get('/taxes', 'Api\StoreController@getStoreTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\StoreController@getStoreTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\StoreController@getStoreDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\StoreController@getStoreDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\StoreController@getStoreCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\StoreController@getStoreCoupon')->name('coupon')->where('coupon_id', '[0-9]+');

            //  Message related resources
            Route::get('/messages', 'Api\StoreController@getStoreMessages')->name('messages');
            Route::get('/messages/{message_id}', 'Api\StoreController@getStoreMessage')->name('message')->where('message_id', '[0-9]+');

            //  Review related resources
            Route::get('/reviews', 'Api\StoreController@getStoreReviews')->name('reviews');
            Route::get('/reviews/{review_id}', 'Api\StoreController@getStoreReview')->name('review')->where('review_id', '[0-9]+');

            //  Settings related resources
            Route::get('/settings', 'Api\StoreController@getStoreSettings')->name('settings');

        });
    }); 

    /*********************************
    /*********************************
     *  PRODUCT RESOURCE ROUTES      *
    /*********************************
    *********************************/

    Route::prefix('products')->group(function () {

        //  Multiple products
        Route::get('/', 'Api\ProductController@getProducts')->name('products');

        //  Single product
        Route::get('/{product_id}', 'Api\ProductController@getProduct')->name('product')->where('product_id', '[0-9]+');

        //  Single product resources
        Route::prefix('{product_id}')->name('product-')->group(function ($group) {

            //  Allow only intergers for product_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('product_id', '[0-9]+');
            }


            //  Owner related resources
            Route::get('/owner', 'Api\ProductController@getProductOwner')->name('owner');

            //  Document related resources
            Route::get('/picture', 'Api\ProductController@getProductPicture')->name('picture');
            Route::get('/gallery', 'Api\ProductController@getProductGallery')->name('gallery');
            Route::get('/downloads', 'Api\ProductController@getProductDownloads')->name('downloads');
            Route::get('/documents', 'Api\ProductController@getProductDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\ProductController@getProductDocument')->name('document')->where('document_id', '[0-9]+');
            /*
            //  Order related resources
            Route::get('/orders', 'Api\ProductController@getProductOrders')->name('orders');
            Route::get('/orders/{order_id}', 'Api\ProductController@getProductOrder')->name('order');
            */
            //  Tax related resources
            Route::get('/taxes', 'Api\ProductController@getProductTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\ProductController@getProductTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\ProductController@getProductDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\ProductController@getProductDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\ProductController@getProductCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\ProductController@getProductCoupon')->name('coupon')->where('coupon_id', '[0-9]+');

            //  Review related resources
            Route::get('/reviews', 'Api\ProductController@getProductReviews')->name('reviews');
            Route::get('/reviews/{review_id}', 'Api\ProductController@getProductReview')->name('review')->where('review_id', '[0-9]+');

        });
    });

    /*********************************
    /*********************************
     *  ORDER RESOURCE ROUTES        *
    /*********************************
    *********************************/

    Route::prefix('orders')->group(function () {

        //  Multiple orders
        Route::get('/', 'Api\OrderController@getOrders')->name('orders');

        //  Single order
        Route::get('/{order_id}', 'Api\OrderController@getOrder')->name('order')->where('order_id', '[0-9]+');

        //  Single order resources
        Route::prefix('{order_id}')->name('order-')->group(function ($group) {

            //  Allow only intergers for order_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('order_id', '[0-9]+');
            }

            //  Merchant related resources
            Route::get('/merchant', 'Api\OrderController@getOrderMerchant')->name('merchant');

            //  Customer related resources
            Route::get('/customer', 'Api\OrderController@getOrderCustomer')->name('customer');

            //  Reference related resources
            Route::get('/reference', 'Api\OrderController@getOrderReference')->name('reference');

            //  Document related resources
            Route::get('/documents', 'Api\OrderController@getOrderDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\OrderController@getOrderDocument')->name('document')->where('document_id', '[0-9]+');

            //  Invoice related resources
            Route::get('/invoices', 'Api\OrderController@getOrderInvoices')->name('invoices');
            Route::get('/invoices/{invoice_id}', 'Api\OrderController@getOrderInvoice')->name('invoice')->where('invoice_id', '[0-9]+');

            //  Tax related resources
            Route::get('/taxes', 'Api\OrderController@getOrderTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\OrderController@getOrderTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\OrderController@getOrderDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\OrderController@getOrderDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\OrderController@getOrderCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\OrderController@getOrderCoupon')->name('coupon')->where('coupon_id', '[0-9]+');

            //  Message related resources
            Route::get('/messages', 'Api\OrderController@getOrderMessages')->name('messages');
            Route::get('/messages/{message_id}', 'Api\OrderController@getOrderMessage')->name('message')->where('message_id', '[0-9]+');

            //  Review related resources
            Route::get('/reviews', 'Api\OrderController@getOrderReviews')->name('reviews');
            Route::get('/reviews/{review_id}', 'Api\OrderController@getOrderReview')->name('review')->where('review_id', '[0-9]+');

            //  Route::post('/proof-of-payment', 'Api\OrderController@saveProofOfPayment');

            //  Lifecycle related resources
            Route::prefix('lifecycle')->name('lifecycle-')->group(function ($group) {

                $endpoints = ['resume', 'approve', 'decline', 'proceed', 'undo', 'update', 'pending', 'skip', 'cancel', 'notify'];

                foreach ($endpoints as $endpoint) {

                    Route::post('/'.$endpoint, 'Api\LifecycleController@'.$endpoint)->name($endpoint);

                }     

            });    

        });
    });

    /*********************************
    /*********************************
     *  INVOICE RESOURCE ROUTES      *
    /*********************************
    *********************************/

    Route::prefix('invoices')->group(function () {

        //  Multiple invoices
        Route::get('/', 'Api\InvoiceController@getInvoices')->name('invoices');

        //  Single invoice
        Route::get('/{invoice_id}', 'Api\InvoiceController@getInvoice')->name('invoice')->where('invoice_id', '[0-9]+');

        //  Single invoice resources
        Route::prefix('{invoice_id}')->name('invoice-')->group(function ($group) {

            //  Allow only intergers for invoice_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('invoice_id', '[0-9]+');
            }

            //  Owner related resources
            Route::get('/owner', 'Api\InvoiceController@getInvoiceOwner')->name('owner');

            //  Merchant related resources
            Route::get('/merchant', 'Api\InvoiceController@getInvoiceMerchant')->name('merchant');

            //  Customer related resources
            Route::get('/customer', 'Api\InvoiceController@getInvoiceCustomer')->name('customer');

            //  Reference related resources
            Route::get('/reference', 'Api\InvoiceController@getInvoiceReference')->name('reference');

            //  Quotation related resources
            Route::get('/quotation', 'Api\InvoiceController@getInvoiceQuotation')->name('quotation');

            //  Child invoice(s) related resources
            Route::get('/child-invoices', 'Api\InvoiceController@getChildInvoices')->name('child-invoices');
            
            //  Parent invoice related resources
            Route::get('/parent-invoice', 'Api\InvoiceController@getParentInvoice')->name('parent-invoice');

            //  Document related resources
            Route::get('/proof-of-payment', 'Api\InvoiceController@getInvoiceProofOfPayment')->name('proof-of-payment');
            Route::get('/documents', 'Api\InvoiceController@getInvoiceDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\InvoiceController@getInvoiceDocument')->name('document')->where('document_id', '[0-9]+');
            
            //  Transaction related resources
            Route::get('/transactions', 'Api\InvoiceController@getInvoiceTransactions')->name('transactions');
            Route::get('/transactions/{transaction_id}', 'Api\InvoiceController@getInvoiceTransaction')->name('transaction')->where('transaction_id', '[0-9]+');

            //  Payment related resources
            Route::get('/payments', 'Api\InvoiceController@getInvoicePayments')->name('payments');
            Route::get('/payments/{payment_id}', 'Api\InvoiceController@getInvoicePayment')->name('payment')->where('payment_id', '[0-9]+');

            //  Refund related resources
            Route::get('/refunds', 'Api\InvoiceController@getInvoiceRefunds')->name('refunds');
            Route::get('/refunds/{refund_id}', 'Api\InvoiceController@getInvoiceRefund')->name('refund')->where('refund_id', '[0-9]+');

            //  Tax related resources
            Route::get('/taxes', 'Api\InvoiceController@getInvoiceTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\InvoiceController@getInvoiceTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\InvoiceController@getInvoiceDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\InvoiceController@getInvoiceDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\InvoiceController@getInvoiceCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\InvoiceController@getInvoiceCoupon')->name('coupon')->where('coupon_id', '[0-9]+');    

        });
    });


    /*********************************
    /*********************************
     *  QUOTATION RESOURCE ROUTES    *
    /*********************************
    *********************************/

    Route::prefix('quotations')->group(function () {

        //  Multiple quotations
        Route::get('/', 'Api\QuotationController@getQuotations')->name('quotations');

        //  Single quotation
        Route::get('/{quotation_id}', 'Api\QuotationController@getQuotation')->name('quotation')->where('quotation_id', '[0-9]+');

        //  Single quotation resources
        Route::prefix('{quotation_id}')->name('quotation-')->group(function ($group) {

            //  Allow only intergers for quotation_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('quotation_id', '[0-9]+');
            }

            //  Owner related resources
            Route::get('/owner', 'Api\QuotationController@getQuotationOwner')->name('owner');

            //  Merchant related resources
            Route::get('/merchant', 'Api\QuotationController@getQuotationMerchant')->name('merchant');

            //  Customer related resources
            Route::get('/customer', 'Api\QuotationController@getQuotationCustomer')->name('customer');

            //  Reference related resources
            Route::get('/reference', 'Api\QuotationController@getQuotationReference')->name('reference');

            //  Quotation related resources
            Route::get('/invoices', 'Api\QuotationController@getQuotationInvoices')->name('invoices');

            //  Document related resources
            Route::get('/documents', 'Api\QuotationController@getQuotationDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\QuotationController@getQuotationDocument')->name('document')->where('document_id', '[0-9]+');
            
            //  Tax related resources
            Route::get('/taxes', 'Api\QuotationController@getQuotationTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\QuotationController@getQuotationTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\QuotationController@getQuotationDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\QuotationController@getQuotationDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\QuotationController@getQuotationCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\QuotationController@getQuotationCoupon')->name('coupon')->where('coupon_id', '[0-9]+');    

        });
    });

    /*********************************
    /*********************************
     *  TAX RESOURCE ROUTES          *
    /*********************************
    *********************************/

    Route::prefix('taxes')->group(function () {

        //  Multiple taxes
        Route::get('/', 'Api\TaxController@getTaxes')->name('taxes');

        //  Single tax
        Route::get('/{tax_id}', 'Api\TaxController@getTax')->name('tax')->where('tax_id', '[0-9]+');

        //  Single tax resources
        Route::prefix('{tax_id}')->name('tax-')->group(function ($group) {

            //  Allow only intergers for tax_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('tax_id', '[0-9]+');
            }

            //  Owner related resources
            Route::get('/owner', 'Api\TaxController@getTaxOwner')->name('owner');

            //  Product related resources
            Route::get('/products', 'Api\TaxController@getTaxProducts')->name('products');
            Route::get('/products/{product_id}', 'Api\TaxController@getTaxProduct')->name('product')->where('product_id', '[0-9]+');

        });
    });

    /*********************************
    /*********************************
     *  DISCOUNT RESOURCE ROUTES     *
    /*********************************
    *********************************/

    Route::prefix('discounts')->group(function () {

        //  Multiple discounts
        Route::get('/', 'Api\DiscountController@getDiscounts')->name('discounts');

        //  Single discount
        Route::get('/{discount_id}', 'Api\DiscountController@getDiscount')->name('discount')->where('discount_id', '[0-9]+');

        //  Single discount resources
        Route::prefix('{discount_id}')->name('discount-')->group(function ($group) {

            //  Allow only intergers for discount_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('discount_id', '[0-9]+');
            }

            //  Owner related resources
            Route::get('/owner', 'Api\DiscountController@getDiscountOwner')->name('owner');

            //  Product related resources
            Route::get('/products', 'Api\DiscountController@getDiscountProducts')->name('products');
            Route::get('/products/{product_id}', 'Api\DiscountController@getDiscountProduct')->name('product')->where('product_id', '[0-9]+');

        });
    });

    /*********************************
    /*********************************
     *  COUPON RESOURCE ROUTES     *
    /*********************************
    *********************************/

    Route::prefix('coupons')->group(function () {

        //  Multiple coupons
        Route::get('/', 'Api\CouponController@getCoupons')->name('coupons');

        //  Single coupon
        Route::get('/{coupon_id}', 'Api\CouponController@getCoupon')->name('coupon')->where('coupon_id', '[0-9]+');

        //  Single coupon resources
        Route::prefix('{coupon_id}')->name('coupon-')->group(function ($group) {

            //  Allow only intergers for coupon_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('coupon_id', '[0-9]+');
            }

            //  Owner related resources
            Route::get('/owner', 'Api\CouponController@getCouponOwner')->name('owner');

            //  Product related resources
            Route::get('/products', 'Api\CouponController@getCouponProducts')->name('products');
            Route::get('/products/{product_id}', 'Api\CouponController@getCouponProduct')->name('product')->where('product_id', '[0-9]+');

        });
    });

    /*********************************
    /*********************************
     *  PHONE RESOURCE ROUTES     *
    /*********************************
    *********************************/

    Route::prefix('phones')->group(function () {

        //  Multiple phones
        Route::get('/', 'Api\PhoneController@getPhones')->name('phones');

        //  Single phone
        Route::get('/{phone_id}', 'Api\PhoneController@getPhone')->name('phone')->where('phone_id', '[0-9]+');

        //  Single phone resources
        Route::prefix('{phone_id}')->name('phone-')->group(function ($group) {

            //  Allow only intergers for phone_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('phone_id', '[0-9]+');
            }

            //  Owner related resources
            Route::get('/owner', 'Api\PhoneController@getPhoneOwner')->name('owner');

            //  Wallet related resources
            Route::get('/wallet', 'Api\PhoneController@getPhoneWallet')->name('wallet');

        });

    });

});



    /*********************************
    /*********************************
     *  USSD RESOURCE ROUTES     *
    /*********************************
    *********************************/

    Route::prefix('ussd')->group(function () {

        Route::post('/customer', 'Api\UssdController@home')->name('ussd-customer-home');
        Route::post('/merchant', 'Api\UssdMerchantController@merchantHome')->name('ussd-merchant-home');

        Route::prefix('stores')->group(function () {

            //  Multiple USSD Stores
            Route::get('/', 'Api\UssdController@getStores')->name('ussd-stores');

            //  Single USSD Store
            Route::get('/{store_code}', 'Api\UssdController@getStore')->name('ussd-store')->where('store_code', '[0-9]+');

            //  Single Store resources
            Route::prefix('{store_code}')->name('ussd-store-')->group(function ($group) {

                //  Allow only intergers for store_code on all group routes
                foreach ($group->getRoutes() as $route) {
                    $route->where('store_code', '[0-9]+');
                }

                //  Owner related resources
                Route::get('/owner', 'Api\UssdController@getUssdStoreOwner')->name('owner');

                //  Product related resources
                Route::get('/products', 'Api\UssdController@getUssdStoreProducts')->name('products');
                Route::get('/products/{product_id}', 'Api\UssdController@getUssdStoreProduct')->name('products')->where('product_id', '[0-9]+');

            });
        });

    });

Route::post('/pusher/auth', function (Request $request) {
    $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));

    return $pusher->socket_auth($request->request->get('channel_name'), $request->request->get('socket_id'));
});

Route::middleware('auth:api')->group(function () {
    //  Logout for all devices
    Route::post('/logout', 'Auth\LoginController@logout');
});

Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\RegisterController@register');
Route::post('/activate-account', 'Auth\AccountActivation@activate');
Route::post('/resend-activation', 'Auth\AccountActivation@resend');
Route::post('/setup-completed', 'Auth\AccountActivation@completeSetup');

/*   PASSWORD RESET ROUTES  */

//  Send email to user requesting password reset
Route::post('/password/email', 'Auth\ForgotPasswordController@customSendForgotPasswordEmail');
//  Reset using token, email and new password
Route::post('/password/reset', 'Auth\ForgotPasswordController@customResetPassword');

/*   UPLOAD RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('/upload', 'Api\UploadController@upload');
Route::delete('/upload/{doc_id}', 'Api\UploadController@delete');

/*   STAFF RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('staff', 'Api\StaffController@index');

/*   CUSTOMER RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('/customers', 'Api\CustomerController@index');

/*   JOBCARD RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('jobcards', 'Api\JobcardController@index');
Route::post('jobcards', 'Api\JobcardController@store');
Route::get('jobcards/stats', 'Api\JobcardController@getEstimatedStats');
Route::get('jobcards/{jobcard_id}', 'Api\JobcardController@show');
Route::post('jobcards/{jobcard_id}/approve', 'Api\JobcardController@approve');  //  ok
Route::get('jobcards/{jobcard_id}/suppliers', 'Api\JobcardController@suppliers');

/*   LIFECYCLE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('lifecycles/stage', 'Api\LifecycleController@updateLifecycleStageProgress');
Route::post('lifecycles/stage/undo', 'Api\LifecycleController@undoLifecycleStageProgress');

/*   APPOINTMENT RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('appointments', 'Api\AppointmentController@index');
Route::post('appointments', 'Api\AppointmentController@store');
Route::get('appointments/{appointment_id}', 'Api\AppointmentController@show');
Route::post('appointments/{appointment_id}', 'Api\AppointmentController@update');
Route::post('appointments/{appointment_id}/approve', 'Api\AppointmentController@approve');  //  ok
Route::post('appointments/{appointment_id}/send', 'Api\AppointmentController@send');
Route::post('appointments/{appointment_id}/skip-send', 'Api\AppointmentController@skipSend');
Route::post('appointments/{appointment_id}/confirm', 'Api\AppointmentController@confirm');
Route::post('appointments/{appointment_id}/cancel-confirmation', 'Api\AppointmentController@cancelConfirmation');
Route::post('appointments/{appointment_id}/reminders', 'Api\AppointmentController@updateReminders');
Route::post('appointments/{appointment_id}/recurring/update-schedule-plan', 'Api\AppointmentController@updateRecurringSettingsSchedulePlan');
Route::post('appointments/{appointment_id}/recurring/update-delivery-plan', 'Api\AppointmentController@updateRecurringSettingsDeliveryPlan');
Route::post('appointments/{appointment_id}/recurring/approve', 'Api\AppointmentController@approveRecurringSettings');

/*
Route::post('invoices/{invoice_id}/approve', 'Api\InvoiceController@approve');
Route::post('invoices/{invoice_id}/send', 'Api\InvoiceController@send');
Route::post('invoices/{invoice_id}/skip-send', 'Api\InvoiceController@skipSend');
Route::post('invoices/{invoice_id}/payment', 'Api\InvoiceController@recordPayment');
Route::post('invoices/{invoice_id}/cancel-payment', 'Api\InvoiceController@cancelPayment');
Route::post('invoices/{invoice_id}/reminders', 'Api\InvoiceController@updateReminders');
Route::post('invoices/{invoice_id}/receipts/send', 'Api\InvoiceController@sendReceipt');
Route::post('invoices/{invoice_id}/recurring/update-schedule-plan', 'Api\InvoiceController@updateRecurringSettingsSchedulePlan');
Route::post('invoices/{invoice_id}/recurring/update-delivery-plan', 'Api\InvoiceController@updateRecurringSettingsDeliveryPlan');
Route::post('invoices/{invoice_id}/recurring/update-payment-plan', 'Api\InvoiceController@updateRecurringSettingsPaymentPlan');
Route::post('invoices/{invoice_id}/recurring/approve', 'Api\InvoiceController@approveRecurringSettings');
*/

/*   DIRECTORY RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('directories', 'Api\DirectoryController@index');

/*   CATEGORY RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('categories', 'Api\CategoryController@index');

/*   CATEGORY RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('tags', 'Api\TagController@index');
Route::post('tags', 'Api\TagController@store');

/*   COSTCENTERS RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('costcenters', 'Api\CostCenterController@index');

/*   PRIORITIES RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('priorities', 'Api\PriorityController@index');

/*   NOTIFICATIONS RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('notifications', 'Api\NotificationsController@index');

/*   QUOTATION RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
/*
Route::get('quotations', 'Api\QuotationController@index');
Route::post('quotations', 'Api\QuotationController@store');  //  ok
Route::get('quotations/stats', 'Api\QuotationController@getEstimatedStats');
Route::get('quotations/{quotation_id}', 'Api\QuotationController@show');  //  ok
Route::post('quotations/{quotation_id}', 'Api\QuotationController@update');
Route::post('quotations/{quotation_id}/approve', 'Api\QuotationController@approve');  //  ok
Route::post('quotations/{quotation_id}/send', 'Api\QuotationController@send');
Route::post('quotations/{quotation_id}/skip-send', 'Api\QuotationController@skipSend');
Route::post('quotations/{quotation_id}/convert', 'Api\QuotationController@convertToInvoice');
*/

/*   INVOICE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
/*
Route::get('invoices', 'Api\InvoiceController@index');
Route::post('invoices', 'Api\InvoiceController@store');
Route::get('invoices/stats', 'Api\InvoiceController@getEstimatedStats');
Route::get('invoices/{invoice_id}', 'Api\InvoiceController@show');
Route::post('invoices/{invoice_id}', 'Api\InvoiceController@update');
Route::post('invoices/{invoice_id}/approve', 'Api\InvoiceController@approve');
Route::post('invoices/{invoice_id}/send', 'Api\InvoiceController@send');
Route::post('invoices/{invoice_id}/skip-send', 'Api\InvoiceController@skipSend');
Route::post('invoices/{invoice_id}/payment', 'Api\InvoiceController@recordPayment');
Route::post('invoices/{invoice_id}/cancel-payment', 'Api\InvoiceController@cancelPayment');
Route::post('invoices/{invoice_id}/reminders', 'Api\InvoiceController@updateReminders');
Route::post('invoices/{invoice_id}/receipts/send', 'Api\InvoiceController@sendReceipt');
Route::post('invoices/{invoice_id}/recurring/update-schedule-plan', 'Api\InvoiceController@updateRecurringSettingsSchedulePlan');
Route::post('invoices/{invoice_id}/recurring/update-delivery-plan', 'Api\InvoiceController@updateRecurringSettingsDeliveryPlan');
Route::post('invoices/{invoice_id}/recurring/update-payment-plan', 'Api\InvoiceController@updateRecurringSettingsPaymentPlan');
Route::post('invoices/{invoice_id}/recurring/approve', 'Api\InvoiceController@approveRecurringSettings');
*/
/*   INVOICE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('sample-sms', 'Api\SmsController@sendSampleSms');

/*   COMMENT RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('comments', 'Api\CommentController@index');
Route::post('comments', 'Api\CommentController@store');
Route::get('comments/{comment_id}', 'Api\CommentController@show');
Route::post('comments/{comment_id}', 'Api\CommentController@update');

/*   CART RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('cart', 'Api\CartController@index');
Route::post('cart', 'Api\CartController@store');
Route::post('cart/{item_id}', 'Api\CartController@update');
Route::delete('cart/{item_id}', 'Api\CartController@delete');
Route::delete('cart/empty', 'Api\CartController@empty');

/*   TAXES RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
/*
Route::get('taxes', 'Api\TaxController@index');
*/
/*   PHONE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('phones', 'Api\PhoneController@index');
Route::post('phones', 'Api\PhoneController@store');
Route::post('phones/{phone_id}', 'Api\PhoneController@update');
Route::delete('phones/{phone_id}', 'Api\PhoneController@delete');

/*   RECENT ACTIVITIES RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('recentactivities', 'Api\RecentActivityController@getActivities');

/*   MISC RESOURCE ROUTES
     -  Visit: https://github.com/antonioribeiro/countries
     -  taxes, geometry maps, topology maps, currencies, countries, timezones, borders, flags, states, cities, timezones times
*/
Route::get('countries', 'Api\CountryController@countries');
Route::get('states', 'Api\CountryController@states');
Route::get('cities', 'Api\CountryController@cities');
Route::get('currencies', 'Api\CountryController@currencies');
Route::get('callingcodes', 'Api\CountryController@callingCodes');
Route::get('exchangerates', 'Api\CountryController@exchangeRates');

/*   DOWNLOAD RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/

Route::get('downloadFile', 'Api\DownloadController@download');

Route::get('download/invoices/{invoice_id}', 'Api\DownloadController@downloadInvoice');
Route::get('download/quotations/{quotation_id}', 'Api\DownloadController@downloadQuotation');

/*
 *  TEST ROUTES - DELETE EVERYTHING BELOW HERE
 *  TEST ROUTES - DELETE EVERYTHING BELOW HERE
 *  TEST ROUTES - DELETE EVERYTHING BELOW HERE
 */

Route::get('send-email', 'Api\TestController@sendEmail');
Route::get('send-sms', function () {
    Twilio::message('+26775993221', 'Hi julian, how are you');

    return 'Message Sent!';
});

use App\User;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Users as UsersResource;

Route::get('json', function () {
    //return new UserResource(User::find(1));
    //return UserResource::collection(User::all());
    return new UsersResource(User::all());
    /*
    return response()->json(
        [
            '_links' => [
                'self' => [
                    'href' => 'http://facility.dev2/api/json',
                ],
            ],
            'id' => 'matthew',
            'name' => "Matthew Weier O'Phinney",
            '_embedded' => [
                'contacts' => [
                    [
                        '_links' => [
                            'self' => [
                                'href' => 'http://facility.dev2/api/json',
                            ],
                        ],
                        'id' => 'mac_nibblet',
                        'name' => 'Antoine Hedgecock',
                    ],
                    [
                        '_links' => [
                            'self' => [
                                'href' => 'http://facility.dev2/api/json',
                            ],
                        ],
                        'id' => 'spiffyjr',
                        'name' => 'Kyle Spraggs',
                    ],
                ],
                'website' => [
                    '_links' => [
                        'self' => [
                            'href' => 'http://facility.dev2/api/json',
                        ],
                    ],
                    'id' => 'mwop',
                    'url' => 'http =>//www.mwop.net',
                ],
            ],
        ]
    );
    */
});
