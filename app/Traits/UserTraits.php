<?php

namespace App\Traits;

use Illuminate\Support\Facades\URL;
use App\Notifications\UserCreated;

trait UserTraits
{
    /**
     * Generate a new passport token used for authentication
     * during API calls to retrieve or modify records.
     */
    public function generateToken($request)
    {
        $http = new \GuzzleHttp\Client();
        $response = $http->post(URL::to('/').'/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'wosVFuDb7gqFM10AJvixPfyfp2NF0fQvPGidyNJ5',
                'username' => $this->email,
                'password' => $request->input('password'),
                'scope' => '',
            ],
        ]);
        //  Lets get an array instead of a stdObject so that we can return without errors
        $response = json_decode($response->getBody(), true);

        return oq_api_notify([
                    'auth' => $response,            //  API ACCESS TOKEN
                    'user' => $this->toArray(),      //  NEW REGISTERED USER
                ], 201);
    }

    public function initiateCreate()
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $profile = request('profile');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE A USER     *
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
                ];

                //  Create the user
                $profile = $this->create($template);

                //  If the user was created successfully
                if ($profile) {
                    /*****************************
                     *   SEND NOTIFICATIONS      *
                     *****************************/

                    $user->notify(new UserCreated($profile));

                    //  Record activity of a user created
                    $status = 'created';

                    $profileCreatedActivity = oq_saveActivity($profile, $user, $status, $template);

                    //  refetch the updated user
                    $profile = $profile->fresh();
                }

                //  If the profile was updated successfully
                if ($profile) {
                    //  Action was executed successfully
                    return ['success' => true, 'response' => $profile];
                }
            } catch (\Exception $e) {
                $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                return ['success' => false, 'response' => $response];
            }
        } else {
            //  No resource found
            $response = oq_api_notify_no_resource();

            return ['success' => false, 'response' => $response];
        }
    }
}
