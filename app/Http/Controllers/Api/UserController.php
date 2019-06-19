<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Notifications\UserUpdated;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        //  Invoice Instance
        $data = ( new User() )->initiateGetAll();
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

    public function show($user_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO VIEW USER         *
         ******************************************************/

        try {
            //  Fetch the user
            $user = User::find($user_id);

            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $user->load(oq_url_to_array(request('connections')));
            }

            //  If the user was found successfully
            if ($user) {
                //  Action was executed successfully
                return oq_api_notify($user, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
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
    
}
