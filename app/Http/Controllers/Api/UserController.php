<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Phone;
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

    public function update(Request $request, $user_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $profile = request('profile');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE PROFILE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE USER INFORMATION               *
         ********************************************/

        if (!empty($profile)) {
            try {
                $template = [
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'date_of_birth' => $profile['date_of_birth'],
                    'gender' => $profile['gender'],
                    'address' => $profile['address'],
                    'country' => $profile['country'],
                    'provience' => $profile['provience'],
                    'city' => $profile['city'],
                    'postal_or_zipcode' => $profile['postal_or_zipcode'],
                    'email' => $profile['email'],
                    'additional_email' => $profile['additional_email'],
                    'facebook_link' => $profile['facebook_link'],
                    'twitter_link' => $profile['twitter_link'],
                    'linkedin_link' => $profile['linkedin_link'],
                    'instagram_link' => $profile['instagram_link'],
                    'bio' => $profile['bio'],
                    'position' => $profile['position'],
                    'accessibility' => $profile['accessibility'],
                    'company_branch_id' => $user->companyBranch->id,
                    'company_id' => $user->companyBranch->company->id,
                ];

                //  Update the profile
                $profile = User::where('id', $user_id)->update($template);

                //  If the profile was updated successfully
                if ($profile) {
                    //  refetch the updated profile
                    $user = User::find($user_id);

                    /*****************************
                     *   SEND NOTIFICATIONS      *
                     *****************************/

                    $user->notify(new UserUpdated($user));

                    //  Record activity of a profile created
                    $status = 'updated';

                    $profileCreatedActivity = oq_saveActivity($user, $user, $status, $template);

                    $user = $user->fresh();
                }

                //  If the profile was updated successfully
                if ($user) {
                    //  Action was executed successfully
                    return oq_api_notify($user, 200);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
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

    public function create(Request $request)
    {
        //  Start creating the user
        $userInstance = new User();
        $data = $userInstance->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the user was created successfully
        if ($success) {
            //  If this is a success then we have a user returned
            $user = $response;
            //  Get any associated phones if any
            $phones = request('profile')['phones'];
            if (isset($phones)) {
                //  Add new phone numbers
                $phoneInstance = new Phone();

                $data = $phoneInstance->addAndReplace($user, $phones);
                $success = $data['success'];
                $phones = $data['response'];
            }

            //  Get a fresh instance of the user
            $user = $user->fresh()->load('phones');

            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $user->load(oq_url_to_array(request('connections')));
            }

            //  Action was executed successfully
            return oq_api_notify($user, 200);
        }

        //  If the data was not a success then return the response
        return $response;
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
}
