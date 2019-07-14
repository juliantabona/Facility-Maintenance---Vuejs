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
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');

Route::middleware('auth:api')->get('/user', 'Api\UserController@getUser');
Route::middleware('auth:api')->get('/user/settings', 'Api\UserController@getUserSettings');
Route::middleware('auth:api')->post('/user/settings', 'Api\UserController@updateUserSettings');

Route::middleware('auth:api')->get('/users', 'Api\UserController@index');
Route::middleware('auth:api')->post('/users', 'Api\UserController@create');
Route::middleware('auth:api')->get('/users/stats', 'Api\UserController@getEstimatedStats');
Route::middleware('auth:api')->get('/users/{user_id}', 'Api\UserController@show');
Route::middleware('auth:api')->post('/users/{user_id}', 'Api\UserController@update');
Route::middleware('auth:api')->get('/users/{user_id}/image', 'Api\UserController@getImage');




/*   APPOINTMENT RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('/upload', 'Api\UploadController@upload');

/*   COMPANY RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('companies', 'Api\CompanyController@index');
Route::post('companies', 'Api\CompanyController@store');
Route::get('companies/stats', 'Api\CompanyController@getEstimatedStats');
Route::get('companies/{company_id}', 'Api\CompanyController@show');
Route::post('companies/{company_id}', 'Api\CompanyController@update');
Route::post('companies/{company_id}/approve', 'Api\CompanyController@approve');  //  ok
Route::get('companies/{company_id}/settings', 'Api\CompanyController@settings');
Route::get('companies/{company_id}/wallets', 'Api\CompanyController@getWallets');
Route::get('companies/{company_id}/clients', 'Api\CompanyController@getClients');
Route::get('companies/{company_id}/logo', 'Api\CompanyController@getLogo');

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
Route::get('jobcards/lifecycle/templates', 'Api\JobcardController@getLifecycleTemplates');
Route::get('jobcards/lifecycle/stages', 'Api\JobcardController@getLifecycleStages');
Route::get('jobcards/{jobcard_id}/lifecycle', 'Api\JobcardController@getLifecycle');
Route::post('jobcards/{jobcard_id}/lifecycle', 'Api\JobcardController@updateLifecycle');
Route::post('jobcards/{jobcard_id}/addLifecycle', 'Api\JobcardController@addLifecycle');
Route::post('jobcards/{jobcard_id}/lifecycle/stages', 'Api\JobcardController@updateLifecycleProgress');
Route::post('jobcards/{jobcard_id}/lifecycle/stages/undo', 'Api\JobcardController@undoLifecycleProgress');

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

/*   PRODUCT/SERVICE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('products', 'Api\ProductController@index');
Route::post('products', 'Api\ProductController@store');
Route::get('products/{product_id}', 'Api\ProductController@show');
Route::post('products/{product_id}', 'Api\ProductController@update');
Route::get('products/{product_id}/image', 'Api\ProductController@getImage');

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
