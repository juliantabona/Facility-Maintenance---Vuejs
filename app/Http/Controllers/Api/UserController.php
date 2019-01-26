<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Notifications\UserUpdated;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function update(Request $request, $user_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $profile = request('profile');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE PROFILE   *
         ******************************************************/

        /*********************************************
         *   VALIDATE USER INFORMATION              *
         ********************************************/

        if (!empty($profile)) {
            try {
                $template = [
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'date_of_birth' => $profile['date_of_birth'],
                    'gender' => $profile['gender'],
                    'country' => $profile['country'],
                    'city' => $profile['city'],
                    'email' => $profile['email'],
                    'additional_email' => $profile['additional_email'],
                    'company_branch_id' => $user->companyBranch->id,
                    'company_id' => $user->companyBranch->company->id,
                ];

                //  Update the profile
                $profile = User::where('id', $user_id)->update($template);

                //  If the profile was created/updated successfully
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

    public function getUser(Request $request)
    {
        $user = auth()->user();

        //  If we have any jobcard so far
        if (count($user)) {
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
        $resourceType = request('resourceType');

        if (!empty($resourceType)) {
            $updated = auth()->user()->forceFill([
                'settings->resourceType' => $resourceType,
            ])->save();

            if ($updated) {
                $updatedSettings = auth()->user()->fresh()->settings;

                return oq_api_notify($updatedSettings, 200);
            }
        } else {
            return oq_api_notify_error('include resourceType', null, 404);
        }

        return oq_api_notify_error('Update Error', null, 404);
    }
}
