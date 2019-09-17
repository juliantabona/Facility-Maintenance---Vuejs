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
     *  MY ACCOUNT ROUTES            *
    /*********************************
    *********************************/

    Route::prefix('me')->name('my-')->group(function () {

        Route::get('/', 'Api\UserController@getUser')->name('account');

        //  Settings related resources
        Route::get('/settings', 'Api\UserController@getUserSettings')->name('settings');

        //  Document related resources
        Route::get('/picture', 'Api\UserController@getUserPicture')->name('picture');
        Route::get('/documents', 'Api\UserController@getUserDocuments')->name('documents');
        Route::get('/documents/{document_id}', 'Api\UserController@getUserDocument')->name('document')->where('document_id', '[0-9]+');

        //  Phone related resources
        Route::get('/phones', 'Api\UserController@getUserPhones')->name('phones');
        Route::get('/phones/{phone_id}', 'Api\UserController@getUserPhone')->name('phone')->where('phone_id', '[0-9]+');

        //  Company related resources
        Route::get('/companies', 'Api\UserController@getUserCompanies')->name('companies');
        Route::get('/companies/{company_id}', 'Api\UserController@getUserCompany')->name('company')->where('company_id', '[0-9]+');

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

        //  Single company resources
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

            //  Company related resources
            Route::get('/companies', 'Api\UserController@getUserCompanies')->name('companies');
            Route::get('/companies/{company_id}', 'Api\UserController@getUserCompany')->name('company')->where('company_id', '[0-9]+');

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

    Route::prefix('companies')->group(function () {

        //  Multiple companies
        Route::get('/', 'Api\CompanyController@getCompanies')->name('companies');

        //  Single company
        Route::get('/{company_id}', 'Api\CompanyController@getCompany')->name('company')->where('company_id', '[0-9]+');

        //  Single company resources
        Route::prefix('{company_id}')->name('company-')->group(function ($group) {

            //  Allow only intergers for company_id on all group routes
            foreach ($group->getRoutes() as $route) {
                $route->where('company_id', '[0-9]+');
            }

            //  Settings related resources
            Route::get('/settings', 'Api\CompanyController@getCompanySettings')->name('settings');

            //  Document related resources
            Route::get('/logo', 'Api\CompanyController@getCompanyLogo')->name('logo');
            Route::get('/documents', 'Api\CompanyController@getCompanyDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\CompanyController@getCompanyDocument')->name('document')->where('document_id', '[0-9]+');

            //  Phone related resources
            Route::get('/phones', 'Api\CompanyController@getCompanyPhones')->name('phones');
            Route::get('/phones/{phone_id}', 'Api\CompanyController@getCompanyPhone')->name('phone')->where('phone_id', '[0-9]+');

            //  User related resources
            Route::get('/users', 'Api\CompanyController@getCompanyUsers')->name('users');
            Route::get('/users/admins', 'Api\CompanyController@getCompanyAdmins')->name('admins');
            Route::get('/users/staff', 'Api\CompanyController@getCompanyStaff')->name('staff');
            Route::get('/users/clients', 'Api\CompanyController@getCompanyUserClients')->name('user-clients');
            Route::get('/users/vendors', 'Api\CompanyController@getCompanyUserVendors')->name('user-vendors');
            Route::get('/users/{user_id}', 'Api\CompanyController@getCompanyUser')->name('user')->where('user_id', '[0-9]+');

            //  Product related resources
            Route::get('/products', 'Api\CompanyController@getCompanyProducts')->name('products');
            Route::get('/products/{product_id}', 'Api\CompanyController@getCompanyProduct')->name('product')->where('product_id', '[0-9]+');

            //  Tax related resources
            Route::get('/taxes', 'Api\CompanyController@getCompanyTaxes')->name('taxes');
            Route::get('/taxes/{tax_id}', 'Api\CompanyController@getCompanyTax')->name('tax')->where('tax_id', '[0-9]+');

            //  Discount related resources
            Route::get('/discounts', 'Api\CompanyController@getCompanyDiscounts')->name('discounts');
            Route::get('/discounts/{discount_id}', 'Api\CompanyController@getCompanyDiscount')->name('discount')->where('discount_id', '[0-9]+');

            //  Coupon related resources
            Route::get('/coupons', 'Api\CompanyController@getCompanyCoupons')->name('coupons');
            Route::get('/coupons/{coupon_id}', 'Api\CompanyController@getCompanyCoupon')->name('coupon')->where('coupon_id', '[0-9]+');

            //  Store related resources
            Route::get('/stores', 'Api\CompanyController@getCompanyStores')->name('stores');
            Route::get('/stores/{store_id}', 'Api\CompanyController@getCompanyStore')->name('store')->where('store_id', '[0-9]+');

        });

    });

    //Route::get('companies/stats', 'Api\CompanyController@getEstimatedStats');
    //Route::post('companies/{company_id}/approve', 'Api\CompanyController@approve');
    //Route::get('companies/{company_id}/wallets', 'Api\CompanyController@getWallets');
    //Route::get('companies/{company_id}/clients', 'Api\CompanyController@getClients');

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

            //  Settings related resources
            Route::get('/settings', 'Api\StoreController@getStoreSettings')->name('settings');

            //  Document related resources
            Route::get('/logo', 'Api\StoreController@getStoreLogo')->name('logo');
            Route::get('/documents', 'Api\StoreController@getStoreDocuments')->name('documents');
            Route::get('/documents/{document_id}', 'Api\StoreController@getStoreDocument')->name('document')->where('document_id', '[0-9]+');

            //  Phone related resources
            Route::get('/phones', 'Api\StoreController@getStorePhones')->name('phones');
            Route::get('/phones/{phone_id}', 'Api\StoreController@getStorePhone')->name('phone')->where('phone_id', '[0-9]+');

            //  User related resources
            Route::get('/users', 'Api\StoreController@getStoreUsers')->name('users');
            Route::get('/users/admins', 'Api\StoreController@getStoreAdmins')->name('admins');
            Route::get('/users/staff', 'Api\StoreController@getStoreStaff')->name('staff');
            Route::get('/users/clients', 'Api\StoreController@getStoreClients')->name('user-clients');
            Route::get('/users/vendors', 'Api\StoreController@getStoreVendors')->name('user-vendors');
            Route::get('/users/{user_id}', 'Api\StoreController@getStoreUser')->name('user')->where('user_id', '[0-9]+');

            //  Company related resources
            Route::get('/company', 'Api\StoreController@getStoreCompany')->name('company');

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

            //  Message related resources
            Route::get('/messages', 'Api\ProductController@getProductMessages')->name('messages');
            Route::get('/messages/{message_id}', 'Api\ProductController@getProductMessage')->name('message')->where('message_id', '[0-9]+');

            //  Review related resources
            Route::get('/reviews', 'Api\ProductController@getProductReviews')->name('reviews');
            Route::get('/reviews/{review_id}', 'Api\ProductController@getProductReview')->name('review')->where('review_id', '[0-9]+');

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
Route::get('quotations', 'Api\QuotationController@index');
Route::post('quotations', 'Api\QuotationController@store');  //  ok
Route::get('quotations/stats', 'Api\QuotationController@getEstimatedStats');
Route::get('quotations/{quotation_id}', 'Api\QuotationController@show');  //  ok
Route::post('quotations/{quotation_id}', 'Api\QuotationController@update');
Route::post('quotations/{quotation_id}/approve', 'Api\QuotationController@approve');  //  ok
Route::post('quotations/{quotation_id}/send', 'Api\QuotationController@send');
Route::post('quotations/{quotation_id}/skip-send', 'Api\QuotationController@skipSend');
Route::post('quotations/{quotation_id}/convert', 'Api\QuotationController@convertToInvoice');

/*   INVOICE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
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

/*   INVOICE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('sample-sms', 'Api\SmsController@sendSampleSms');

/*   ORDER RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('orders', 'Api\OrderController@index');
Route::post('orders', 'Api\OrderController@store');
Route::get('orders/{order_id}', 'Api\OrderController@show');
Route::post('orders/{order_id}', 'Api\OrderController@update');
Route::get('orders/{order_id}/documents', 'Api\OrderController@getDocuments');
Route::post('orders/{order}/proof-of-payment', 'Api\OrderController@saveProofOfPayment');

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
Route::get('taxes', 'Api\TaxController@index');

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
