<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Company;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getUsers()
    {
        //  Check if the user is authourized to view all users
        if ($this->user->can('viewAll', User::class)) {

            //  Get the users
            $users = User::paginate();

            //  Check if the users exist
            if ($users) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User() )->convertToApiFormat($users);

            } else {

                //  Not Found
                return oq_api_notify_no_resource();

            }
        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getUser(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::where('id', $user_id)->first() ?? null;

        //  Check if the user exists
        if ($user) {

            //  Check if the user is authourized to view the user
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Company Instance
                return $user->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getUserSettings(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user settings
        $settings = $user->settings ?? null;

        //  Check if the settings exists
        if ($settings) {

            //  Check if the user is authourized to view the user settings
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Setting Instance
                return $settings->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getUserPicture(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user profile image
        $profile_image = $user->profile_image ?? null;

        //  Check if the profile image exists
        if ($profile_image) {

            //  Check if the user is authourized to view the user's profile image
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Document Instance
                return $profile_image->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getUserDocuments(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user documents
        $documents = $user->documents()->paginate() ?? null;

        //  Check if the documents exists
        if ($documents) {

            //  Check if the user is authourized to view the user's documents
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document() )->convertToApiFormat($documents);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();
            
        }
    }

    public function getUserDocument(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the document id
        $document_id = $request->route('document_id');

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user document
        $document = $user->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the user document
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Document Instance
                return $document->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }


    /*********************************
     *  PHONE RELATED RESOURCES      *
    *********************************/

    public function getUserPhones(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user phones
        $phones = $user->phones()->paginate() ?? null;

        //  Check if the phones exist
        if ($phones) {

            //  Check if the user is authourized to view the user phones
            if ($this->user->can('view', $user)) {

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

    public function getUserPhone(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the phone id
        $phone_id = $request->route('phone_id');

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user phone
        $phone = $user->phones()->where('id', $phone_id)->first() ?? null;

        //  Check if the phone exists
        if ($phone) {

            //  Check if the user is authourized to view the user phone
            if ($this->user->can('view', $user)) {

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

    public function getUserCompanies(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user companies
        $companies = $user->companies()->paginate() ?? null;

        //  Check if the companies exists
        if ($companies) {

            //  Check if the user is authourized to view the user companies
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Company Instance
                return ( new \App\Company() )->convertToApiFormat($companies);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getUserCompany(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the company id
        $company_id = $request->route('company_id');

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user company
        $company = $user->companies()->where('companies.id', $company_id)->first() ?? null;

        //  Check if the company exists
        if ($company) {

            //  Check if the user is authourized to view the user company
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Company Instance
                return $company->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getUserStores(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user stores
        $stores = $user->stores()->paginate() ?? null;
        
        //  Check if the stores exists
        if ($stores) {

            //  Check if the user is authourized to view the user stores
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Store Instance
                return ( new \App\Store() )->convertToApiFormat($stores);
                
            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getUserStore(Request $request)
    {
        //  Get the specified users id or use the authenticated users id
        $user_id = $request->route('user_id') ?? auth('api')->user()->id;

        //  Get the store id
        $store_id = $request->route('store_id');

        //  Get the user
        $user = User::findOrFail($user_id);

        //  Get the user store
        $store = $user->stores()->where('stores.id', $store_id)->first() ?? null;

        //  Check if the store exists
        if ($store) {

            //  Check if the user is authourized to view the user store
            if ($this->user->can('view', $user)) {

                //  Return an API Readable Format of the Store Instance
                return $store->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*
    public function index()
    {
        //  User Instance and Method to fetch users
        $data = ( new User() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the users were found successfully
        if ($success) {
            //  If this is a success then we have a list of users
            $companies = $response;

            //  Action was executed successfully
            return oq_api_notify($companies, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function create(Request $request)
    {
        //  Start creating the user
        $data = ( new User() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the user was created successfully
        if ($success) {
            //  If this is a success then we have a user returned
            $user = $response;

            //  Action was executed successfully
            return oq_api_notify($user, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update(Request $request, $user_id)
    {
        //  Start creating the user
        $data = ( new User() )->initiateUpdate($user_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the user was created successfully
        if ($success) {
            //  If this is a success then we have a user returned
            $user = $response;

            //  Action was executed successfully
            return oq_api_notify($user, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

        //  DONE
    public function show($user_id)
    {
        //  Start creating the user
        $data = ( new User() )->initiateShow($user_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the user was created successfully
        if ($success) {
            //  If this is a success then we have a user returned
            $user = $response;

            //  Action was executed successfully
            return oq_api_notify($user, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getImage(Request $request, $user_id)
    {
        try {
            //  Get the associated user
            $user = User::where('id', $user_id)->first();
            $userImage = $user->profileImage;

            //  Action was executed successfully
            return oq_api_notify($userImage, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function getUser(Request $request)
    {
        $user = auth()->user();

        //  If we have any jobcard so far
        if ($user) {
            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $user->load(oq_url_to_array(request('connections')));
            }

            return $user;
        }
    }

    public function getUserSettings(Request $request)
    {
        return oq_api_notify(auth()->user()->settings, 200);
    }

    public function updateUserSettings(Request $request)
    {
        $allocationType = request('allocationType');

        if (!empty($allocationType)) {
            $updated = auth()->user()->settings()->update([
                'details->allocationType' => $allocationType,
            ]);

            if ($updated) {
                $updatedSettings = auth()->user()->settings;

                return oq_api_notify($updatedSettings, 200);
            }
        } else {
            return oq_api_notify_error('include allocationType', null, 404);
        }

        return oq_api_notify_error('Update Error', null, 404);
    }

    public function getEstimatedStats()
    {
        //  Start creating the company
        $data = ( new User() )->getStatistics();
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
    */
}
