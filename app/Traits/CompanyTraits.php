<?php

namespace App\Traits;

use App\Notifications\CompanyCreated;

trait CompanyTraits
{
    public function initiateCreate()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        //  Query data
        $company = request('company');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE A COMPANY  *
         ******************************************************/

        /*********************************************
         *   VALIDATE COMPANY INFORMATION            *
         ********************************************/

        if (!empty($company)) {
            try {
                $template = [
                    'name' => $company['name'],
                    'description' => $company['description'],
                    'date_of_incorporation' => $company['date_of_incorporation'],
                    'type' => $company['type'],
                    'address' => $company['address'],
                    'country' => $company['country'],
                    'provience' => $company['provience'],
                    'city' => $company['city'],
                    'postal_or_zipcode' => $company['postal_or_zipcode'],
                    'email' => $company['email'],
                    'additional_email' => $company['additional_email'],
                    'website_link' => $company['website_link'],
                    'facebook_link' => $company['facebook_link'],
                    'twitter_link' => $company['twitter_link'],
                    'linkedin_link' => $company['linkedin_link'],
                    'instagram_link' => $company['instagram_link'],
                    'bio' => $company['bio'],
                ];

                //  Create the company
                $company = $this->create($template);

                //  If the company was created successfully
                if ($company) {
                    /*****************************
                     *   SEND NOTIFICATIONS      *
                     *****************************/

                    $auth_user->notify(new CompanyCreated($company));

                    /*****************************
                     *   RECORD ACTIVITY         *
                     *****************************/

                    $status = 'created';

                    $companyCreatedActivity = oq_saveActivity($company, $auth_user, $status, $template);

                    //  refetch the created company
                    $company = $company->fresh();

                    //  If the company was updated successfully
                    if ($company) {
                        //  Action was executed successfully
                        return ['success' => true, 'response' => $company];
                    }
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
