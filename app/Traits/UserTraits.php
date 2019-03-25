<?php

namespace App\Traits;

use Illuminate\Support\Facades\URL;
use App\Notifications\UserCreated;

trait UserTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of company results.
     *
     */
    public function initiateGetAll($options = array())
    {
        //  Default settings
        $defaults = array(
            'paginate' => true,
        );

        //  Replace defaults with any provided options
        $config = array_merge($defaults, $options);

        //  If we overide using the request
        if (request('paginate') == 0 || request('paginate') == 1) {
            $config['paginate'] = request('paginate') == 1 ? true : false;
        }

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  $allocation = all, company, branch
        /*
         *  The $allocation variable is used to determine where the data is being
         *  pulled from. The user may request data from three possible sources.
         *  1) Data may come from the associated authenticated user branch
         *  2) Data may come from the associated authenticated user company
         *  3) Data may come from the whole bucket meaning outside the scope of the
         *     authenticated user. This means we can access all possible models
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = strtolower(request('allocation'));

        /*
         *  $type = client, supplier, staff
        /*
         *  The $type variable is used to determine which type of company to pull.
         *  The user may request data of type.
         *  1) client: A company that is listed as a client/customer
         *  2) supplier: A company that is listed as a supplier/vendor/hawker
         */
        $type = strtolower(request('type'));

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL USERS         *
            /********************************************************/

            //  Get the current company instance
            $model = $this;
        } elseif ($allocation == 'branch') {
            /**********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH USERS    *
            /**********************************************************/

            // Only get users associated to the company branch
            $model = $auth_user->companyBranch;
        } else {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY USERS    *
            /***********************************************************/

            //  Only get users associated to the company
            $model = $auth_user->company;
        }

        /*  If user indicated to only return client dierctories
        */
        if ($type == 'client') {
            $users = $model->userClients();

        /*  If user indicated to only return supplier dierctories
        */
        } elseif ($type == 'supplier') {
            $users = $model->userSuppliers();

        /*  If user indicated to only return staff dierctories
        */
        } elseif ($type == 'staff') {
            $users = $model->userStaff();

        /*  If user did not indicate any specific group
        */
        } else {
            //  If the $type is a list e.g) client,supplier
            $type = explode(',', $type);

            if (count($type)) {
                $users = $model->userDirectory()->whereIn('user_directory.type', $type);
            } else {
                $users = $model->userDirectory();
            }
        }

        /*  To avoid sql order_by error for ambigious fields e.g) created_at
         *  we must specify the order_join.
         *
         *  Order joins help us when using the "advancedFilter()" method. Usually
         *  we need to specify the joining table so that the system is not confused
         *  by similar column names that exist on joining tables. E.g) the column
         *  "created_at" can exist in multiple table and the system might not know
         *  whether the "order_by" is for table_1 created_at or table 2 created_at.
         *  By specifying this we end up with "table_1.created_at"
         *
         *  If we don't have any special order_joins, lets default it to nothing
         */

        $order_join = 'users';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $users = $users->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $users = $users->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $users = $users->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $users = $users->get();
            }

            //  If we have any users so far
            if (count($users)) {
                //  Eager load other relationships wanted if specified
                if (strtolower(request('connections'))) {
                    $users->load(oq_url_to_array(strtolower(request('connections'))));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $users];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  getStatistics() method:
     *
    /*  This method is used to get the overall statistics of the users,
     *  showing information of users in their respective states such as
     *  1) Name of status
     *  2) Total number of users in each respective status
     *  3) Total sum of the grand totals in each respective status
     *  4) The base currency used by the associated company
     *
     *  Example of returned output:
        {
            "stats": [
                {
                    "grand_total": null,
                    "total_count": 0,
                    "name": "Draft"
                },
                {
                    "grand_total": 23450,
                    "total_count": 6,
                    "name": "Approved"
                },
                {
                    "grand_total": 45240,
                    "total_count": 2,
                    "name": "Sent"
                },
                {
                    "grand_total": 1250,
                    "total_count": 1,
                    "name": "Cancelled"
                },
                {
                    "grand_total": 18560,
                    "total_count": 5,
                    "name": "Expired"
                },
                {
                    "grand_total": 75880,
                    "total_count": 12,
                    "name": "Paid"
                }
            ],
            "base_currency": {
                "country": "Botswana",
                "currency": {
                    "iso": {
                        "code": "BWP",
                        "number": "072"
                    },
                    "name": "Pula",
                    "symbol": "P"
                }
            }
        }
     *
     */

    public function getStatistics()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        //  Start getting the users
        $data = $this->initiateGetAll(['paginate' => false]);
        $success = $data['success'];
        $response = $data['response'];

        if ($success) {
            try {
                //  Get all the available users so far
                $users = $data['response'];

                //  From the list of users we will group them by their directory_type e.g) client, supplier, e.t.c
                //  After this we will map through each group (client, supplier, e.t.c) and get the status name, total sum of
                //  the grand totals as well as the total count of grouped users of that activity.
                /*
                 *  Example of returned output:
                 *
                    {
                        "Paid": {
                            "name": "Paid",
                            "grand_total": 44520,
                            "total_count": 5
                        },
                        "Sent": {
                            "name": "Sent",
                            "grand_total": 14000,
                            "total_count": 1
                        }
                    }
                 *
                 */

                $availableStats = collect($users)->groupBy('directory_type')->map(function ($companyGroup, $key) {
                    return [
                        'name' => ucwords($key),                                //  e.g) Client, Supplier, Staff e.t.c
                        'total_count' => collect($companyGroup)->count(),       //  12
                    ];
                });

                //  This is a list of all the statistics we want returned in their respective order
                $expectedStats = ['Supplier', 'Client', 'Staff'];

                //  From the list of expected stats, we will map through and inspect if the expected stat
                //  exists in the available stats we have collected. If it does then return back the existing
                //  stat, otherwise we will create a new array that will hold the expected stat name that does
                //  not exist, as well as put a grand total sum of zero and a total count of zero
                /*
                 *  Example of returned output:
                 *
                    [
                        {
                            "name": "Draft",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Approved",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Sent",
                            "grand_total": 14000,
                            "total_count": 1
                        },
                        {
                            "name": "Cancelled",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Expired",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Paid",
                            "grand_total": 44520,
                            "total_count": 5
                        }
                    ]
                 *
                 */
                $stats = collect($expectedStats)->map(function ($stat_name) use ($availableStats) {
                    if (collect($availableStats)->has(strtolower($stat_name))) {
                        return $availableStats[strtolower($stat_name)];
                    } else {
                        return [
                                    'name' => $stat_name,         //  e.g) Supplier, Client, Staff
                                    'total_count' => 0,
                                ];
                    }
                });

                //  Calculate the overall stats e.g) Total Count
                $totalCount = ['name' => 'Total Individuals', 'total_count' => 0];

                foreach ($stats as $stat) {
                    $totalCount['total_count'] += $stat['total_count'];
                }

                //  Merge the overview stats, stats and base currency into one collection
                $data = [
                        'overview_stats' => [$totalCount],
                        'stats' => $stats,
                    ];

                //  Action was executed successfully
                return ['success' => true, 'response' => $data];
            } catch (\Exception $e) {
                //  Log the error
                $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }
        } else {
            return $response;
        }
    }

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
                    'auth' => $response,                                        //  API ACCESS TOKEN
                    'user' => $this->with('settings')->first()->toArray(),
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
