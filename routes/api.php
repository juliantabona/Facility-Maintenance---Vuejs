<?php

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
     -  Account Activation
     -  Get Authenticated User
    -   Logout
*/

Route::middleware('auth:api')->group(function () {
    //  Logout for all devices
    Route::post('/logout', 'Auth\LoginController@logout');
});
Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\RegisterController@register');
Route::post('/activate-account', 'Auth\AccountActivation@activate');
Route::post('/resend-activation', 'Auth\AccountActivation@resend');

Route::middleware('auth:api')->get('/user', 'Api\UserController@getUser');
Route::middleware('auth:api')->get('/user/settings', 'Api\UserController@getUserSettings');
Route::middleware('auth:api')->post('/user/settings', 'Api\UserController@updateUserSettings');

/*   PROCESS FORM RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('/process-forms', 'Api\ProcessFormController@store');

/*   JOBCARD RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('jobcards', 'Api\JobcardController@index');
Route::post('jobcards', 'Api\JobcardController@store');
Route::get('jobcards/{jobcard_id}', 'Api\JobcardController@show');
Route::get('jobcards/{jobcard_id}/suppliers', 'Api\JobcardController@suppliers');
Route::get('jobcards/lifecycle/templates', 'Api\JobcardController@getLifecycleTemplates');
Route::get('jobcards/lifecycle/stages', 'Api\JobcardController@getLifecycleStages');
Route::get('jobcards/{jobcard_id}/lifecycle', 'Api\JobcardController@getLifecycle');
Route::post('jobcards/{jobcard_id}/lifecycle', 'Api\JobcardController@updateLifecycle');
Route::post('jobcards/{jobcard_id}/addLifecycle', 'Api\JobcardController@addLifecycle');

/*   JOBCARD RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('companies', 'Api\CompanyController@index');

/*  *** NOTE *** model_id means that this could be a company/branch associated id since
 *  we could be trying to retrieve the company using either its branch/company id.
 */
Route::get('companies/{model_id}', 'Api\CompanyController@show');

/*   CATEGORY RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('categories', 'Api\CategoryController@index');

/*   COSTCENTERS RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('costcenters', 'Api\CostCenterController@index');

/*   PRIORITIES RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('priorities', 'Api\PriorityController@index');

/*   QUOTATION RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::post('quotations', 'Api\QuotationController@store');
Route::post('quotations/{quotation_id}', 'Api\QuotationController@update');

/*   PRODUCT/SERVICE RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('products', 'Api\ProductOrServiceController@index');

/*   TAXES RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/
Route::get('taxes', 'Api\TaxController@index');

/*   DOWNLOAD RESOURCE ROUTES
     -  Get, Show, Update, Trash, Delete
*/

Route::get('downloadFile', 'Api\DownloadController@download');

Route::get('download/quotations/{quotation_id}', 'Api\DownloadController@downloadQuotation');
