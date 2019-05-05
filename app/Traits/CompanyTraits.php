<?php

namespace App\Traits;

use App\Notifications\CompanyCreated;
use App\Notifications\CompanyApproved;

trait CompanyTraits
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
        $requestPagination = request('paginate');
        if (isset($requestPagination) && ($requestPagination == 0 || $requestPagination == 1)) {
            $config['paginate'] = $requestPagination == 1 ? true : false;
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
         *  $type = client, supplier
        /*
         *  The $type variable is used to determine which type of company to pull.
         *  The user may request data of type.
         *  1) client: A company that is listed as a client/customer
         *  2) supplier: A company that is listed as a supplier/vendor/hawker
         */
        $type = strtolower(request('type'));

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL COMPANIES         *
            /**********************************************************/

            //  Get the current company instance
            $model = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH COMPANIES    *
            /*************************************************************/

            // Only get companies associated to the company branch
            $model = $auth_user->companyBranch;
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY COMPANIES    *
            /**************************************************************/

            //  Only get companies associated to the company
            $model = $auth_user->company;
        }

        /*  If user indicated to only return client dierctories
        */
        if ($type == 'client') {
            $companies = $model->companyClients();

        /*  If user indicated to only return supplier dierctories
        */
        } elseif ($type == 'supplier') {
            $companies = $model->companySuppliers();

        /*  If user did not indicate any specific group
        */
        } else {
            //  If the $type is a list e.g) client,supplier
            $type = explode(',', $type);

            if (count($type)) {
                $companies = $model->companyDirectory()->whereIn('company_directory.type', $type);
            } else {
                $companies = $model->companyDirectory();
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

        $order_join = 'companies';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $companies = $companies->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $companies = $companies->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $companies = $companies->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $companies = $companies->get();
            }

            //  If we have any companies so far
            if ($companies) {
                //  Eager load other relationships wanted if specified
                if (strtolower(request('connections'))) {
                    $companies->load(oq_url_to_array(strtolower(request('connections'))));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $companies];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    public function initiateGetStaff($options = array())
    {
        //  Default settings
        $defaults = array(
            'paginate' => false,
        );

        //  Replace defaults with any provided options
        $config = array_merge($defaults, $options);

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

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL STAFF         *
            /********************************************************/

            //  Get the current company instance
            $model = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH STAFF    *
            /*************************************************************/

            // Only get companies associated to the company branch
            $model = $auth_user->companyBranch;
        } else {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY STAFF    *
            /***********************************************************/

            //  Only get companies associated to the company
            $model = $auth_user->company;
        }

        $staff = $model->userStaff();

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

        $order_join = 'user_directory';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $staff = $staff->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $staff = $staff->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $staff = $staff->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $staff = $staff->get();
            }

            //  If we have any staff so far
            if ($staff) {
                //  Eager load other relationships wanted if specified
                if (strtolower(request('connections'))) {
                    $staff->load(oq_url_to_array(strtolower(request('connections'))));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $staff];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

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

    /*  initiateApprove() method:
     *
     *  This is used to approve an existing company. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the approval of
     *  the company.
     *
     */
    public function initiateApprove($company_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO APPROVE QUOTATION   *
         ******************************************************/

        try {
            //  Get the company
            $company = $this->where('id', $company_id)->first();

            //  Check if we have an company
            if ($company) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new CompanyApproved($company));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of company approved
                $status = 'approved';
                $companyApprovedActivity = oq_saveActivity($company, $auth_user, $status, ['company' => $company->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $company];
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_no_resource()];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  getStatistics() method:
     *
    /*  This method is used to get the overall statistics of the companies,
     *  showing information of companies in their respective states such as
     *  1) Name of status
     *  2) Total number of companies in each respective status
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

        //  Start getting the companies
        $data = $this->initiateGetAll(['paginate' => false]);
        $success = $data['success'];
        $response = $data['response'];

        if ($success) {
            try {
                //  Get all the available companies so far
                $companies = $data['response'];

                //  From the list of companies we will group them by their directory_type e.g) client, supplier, e.t.c
                //  After this we will map through each group (client, supplier, e.t.c) and get the status name, total sum of
                //  the grand totals as well as the total count of grouped companies of that activity.
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

                $availableStats = collect($companies)->groupBy('directory_type')->map(function ($companyGroup, $key) {
                    return [
                        'name' => ucwords($key),                                //  e.g) Client, Supplier, e.t.c
                        'total_count' => collect($companyGroup)->count(),       //  12
                    ];
                });

                //  This is a list of all the statistics we want returned in their respective order
                $expectedStats = ['Supplier', 'Client'];

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
                                    'name' => $stat_name,         //  e.g) Supplier, Client
                                    'total_count' => 0,
                                ];
                    }
                });

                //  Calculate the overall stats e.g) Total Count
                $totalCount = ['name' => 'Total Companies', 'total_count' => 0];

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
            return ['success' => false, 'response' => $response];
        }
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific
     *  columns that can then be used for storage. We may only want to summarize
     *  the data to very important information, rather than storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want (we access the fillable columns of the model), while also
     *  removing any custom attributes we do not want to store
     *  (we access the appends columns of the model),
     */
    public function summarize()
    {
        //  Collect and select table columns
        return collect($this->fillable)
                //  Remove all custom attributes since the are all based on recent activities
                ->forget($this->appends);
    }
}
