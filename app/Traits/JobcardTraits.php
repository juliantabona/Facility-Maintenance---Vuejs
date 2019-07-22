<?php

namespace App\Traits;

use DB;
use App\Company;
use App\Notifications\JobcardApproved;
use Illuminate\Pagination\LengthAwarePaginator;

trait JobcardTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of jobcard results.
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
         *  $allocation = all, jobcard, branch
        /*
         *  The $allocation variable is used to determine where the data is being
         *  pulled from. The user may request data from three possible sources.
         *  1) Data may come from the associated authenticated user branch
         *  2) Data may come from the associated authenticated user jobcard
         *  3) Data may come from the whole bucket meaning outside the scope of the
         *     authenticated user. This means we can access all possible records
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = strtolower(request('allocation'));

        /*
         *  $type = client, supplier
        /*
         *  The $type variable is used to determine which type of jobcard to pull.
         *  The user may request data of type.
         *  1) client: A jobcard that is listed as a client/customer
         *  2) supplier: A jobcard that is listed as a supplier/vendor/hawker
         */
        $type = strtolower(request('type'));

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL JOBCARDS         *
            /**********************************************************/

            //  Get the current jobcard instance
            $model = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH JOBCARDS    *
            /*************************************************************/

            // Only get jobcards associated to the jobcard branch
            $model = $auth_user->companyBranch;
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY JOBCARDS    *
            /**************************************************************/

            //  Only get jobcards associated to the jobcard
            $model = $auth_user->company;
        }

        $jobcards = $model->jobcards();

        /*  If user indicated to only return client specific jobcards
        */
        if (request('client_id')) {
            $jobcards = $jobcards->where('client_id', request('client_id'));

        /*  If user indicated to only return supplier specific jobcards
        */
        } elseif (request('supplier_id')) {
            $jobcards = $jobcards->whereHas('suppliersList', function ($query) {
                $query->where('supplier_id', request('supplier_id'));
            });
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

        $order_join = 'jobcards';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $jobcards = $jobcards->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $jobcards = $jobcards->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get all except trashed
            } else {
                //  Run query
                $jobcards = $jobcards->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            }

            //  Filter by status if specified
            if (request('status')) {
                //  Run query
                $stat_name = ucwords(request('status'));

                $jobcards = $jobcards->get();

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $jobcards->load(oq_url_to_array(request('connections')));
                }

                $jobcards = collect($jobcards)->filter(function ($jobcard, $key) use ($stat_name) {
                    return  $jobcard['current_lifecycle_main_status']['name'] == $stat_name || $jobcard['current_lifecycle_sub_status'] == $stat_name;
                });

                $page = request('page', 1);         //  The page number from the pagination list
                $perPage = request('limit', 10);    //  Pagination limit
                $jobcards = new LengthAwarePaginator(
                                    collect($jobcards->forPage($page, $perPage))->values(),
                                    $jobcards->count(),
                                    $perPage,
                                    $page,
                                    ['path' => url('api/jobcards')]
                                );
            } else {
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $jobcards = $jobcards->get();
                } else {
                    $jobcards = $jobcards->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                }

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $jobcards->load(oq_url_to_array(request('connections')));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $jobcards];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateCreate() method:
     *
     *  This is used to create a new appointment. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the appointment.
     *
     */
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $appointment is a collection of the jobcard to be stored.
         */
        $jobcard = request('jobcard');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE JOBCARD    *
         ******************************************************/

        /*********************************************
         *   VALIDATE JOBCARD INFORMATION            *
         ********************************************/

        //  Create a template to hold the appointment details
        $template = $template ?? [
             // 'client_id', 'client_type', 'is_public',

            'title' => $jobcard['title'],
            'description' => $jobcard['description'],
            'start_date' => $jobcard['start_date'],
            'end_date' => $jobcard['end_date'],
            'client_id' => $jobcard['client_id'],
            'client_type' => $jobcard['client_model_type'],
            'company_branch_id' => $auth_user->company_branch_id ?? null,
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Create the jobcard
            $jobcard = $this->create($template);

            //  If the jobcard was created successfully
            if ($jobcard) {
                //  Start with an empty priority, categories, costcenters, assigned_staff
                $priority = [];
                $categories = [];
                $costcenters = [];
                $assignedStaff = [];

                if( isset( request('appointment')['priority'] ) ){
                    if( COUNT( request('appointment')['priority'] ) ){
                        //  Foreach of the priorities
                        foreach (request('jobcard')['priority'] as $key => $id) {
                            //  Store with the following details corresponding to the priority table columns
                            $priority[$key] = [
                                'priority_id' => $id,
                                'trackable_id' => $jobcard->id,
                                'trackable_type' => 'jobcard',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        $priority = DB::table('priority_allocations')->insert($priority);

                    }
                }

                if( isset( request('appointment')['categories'] ) ){
                    if( COUNT( request('appointment')['categories'] ) ){

                        //  Foreach of the categories
                        foreach (request('jobcard')['categories'] as $key => $id) {
                            //  Store with the following details corresponding to the category table columns
                            $categories[$key] = [
                                'category_id' => $id,
                                'trackable_id' => $jobcard->id,
                                'trackable_type' => 'jobcard',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        $categories = DB::table('category_allocations')->insert($categories);

                    }
                }

                if( isset( request('appointment')['costcenters'] ) ){
                    if( COUNT( request('appointment')['costcenters'] ) ){
                        //  Foreach of the costcenters
                        foreach (request('jobcard')['costcenters'] as $key => $id) {
                            //  Store with the following details corresponding to the costcenter table columns
                            $costcenters[$key] = [
                                'cost_center_id' => $id,
                                'trackable_id' => $jobcard->id,
                                'trackable_type' => 'jobcard',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        $costcenters = DB::table('costcenter_allocations')->insert($costcenters);

                    }
                }

                if( isset( request('appointment')['assigned_staff'] ) ){
                    if( COUNT( request('appointment')['assigned_staff'] ) ){
                        //  Foreach of the assigned staff
                        foreach (request('jobcard')['assigned_staff'] as $key => $id) {
                            //  Store with the following details corresponding to the assigned staff table columns
                            $assignedStaff[$key] = [
                                'user_id' => $id,
                                'trackable_id' => $jobcard->id,
                                'trackable_type' => 'jobcard',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                        
                        $assignedStaff = DB::table('staff_allocations')->insert($assignedStaff);

                    }
                }

                //  Get default lifecycle
                $defaultLifecycle = $jobcard->owningCompany->lifecycles()->where('type', 'jobcard')->first();

                //  Add default lifecycle to jobcard
                if ($defaultLifecycle) {
                    $lifecycle = DB::table('lifecycle_allocations')->insert([
                            'lifecycle_id' => $defaultLifecycle->id,
                            'trackable_id' => $jobcard->id,
                            'trackable_type' => 'jobcard',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //$auth_user->notify(new JobcardCreated($jobcard));

                //  re-retrieve the instance to get all of the fields in the table.
                $jobcard = $jobcard->fresh();

                //  If we have any jobcards so far
                if ($jobcard) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $jobcard->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of jobcard created
                $status = 'created';
                $jobcardCreatedActivity = oq_saveActivity($jobcard, $auth_user, $status, ['jobcard' => $jobcard->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $jobcard];
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

    /*  initiateApprove() method:
     *
     *  This is used to approve an existing jobcard. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the approval of
     *  the jobcard.
     *
     */
    public function initiateApprove($jobcard_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO APPROVE JOBCARD   *
         ******************************************************/

        try {
            //  Get the jobcard
            $jobcard = $this->where('id', $jobcard_id)->first();

            //  Check if we have an jobcard
            if ($jobcard) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new JobcardApproved($jobcard));

                //  If we have any jobcards so far
                if ($jobcard) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $jobcard->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of jobcard approved
                $status = 'approved';
                $jobcardApprovedActivity = oq_saveActivity($jobcard, $auth_user, $status, ['jobcard' => $jobcard->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $jobcard];
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
    /*  This method is used to get the overall statistics of the appointments,
     *  showing information of appointments in their respective states such as
     *  1) Name of status
     *  2) Total number of appointments in each respective status
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

        //  Start getting the jobcards
        $data = $this->initiateGetAll(['paginate' => false]);
        $success = $data['success'];
        $response = $data['response'];

        if ($success) {
            try {
                //  Get all the available jobcards so far
                $jobcards = $data['response'];

                //  From the list of jobcards we will group them by their lifecycle status e.g) Open, Closed e.t.c
                //  After this we will map through each group (Open, Closed, e.t.c) and get the status name, and the
                //  total count of grouped jobcards of that status.
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

                $mainAvailableStages = [];
                $inbetweenAvailableStages = [];

                foreach ($jobcards as $key => $jobcard) {
                    if ($jobcard['current_lifecycle_stage']) {
                        $stage = [
                                    'type' => $jobcard['current_lifecycle_stage']['activity']['type'],
                                    'instance' => $jobcard['current_lifecycle_stage']['activity']['instance'],
                                    'instance_type' => $jobcard['current_lifecycle_stage']['activity']['type'].'_'.$jobcard['current_lifecycle_stage']['activity']['instance'],
                                ];

                        if (!empty($jobcard['current_lifecycle_sub_status'])) {
                            $stage['name'] = $jobcard['current_lifecycle_sub_status'];
                            array_push($inbetweenAvailableStages, $stage);
                        } else {
                            array_push($mainAvailableStages, $stage);
                        }
                    } elseif ($jobcard['has_approved']) {
                        array_push($mainAvailableStages, [
                                    'type' => 'open',
                                    'instance' => 1,
                                    'instance_type' => 'open_1',
                                ]);
                    }
                }

                //  Format for main stages
                $mainAvailableStages = collect($mainAvailableStages)->reject(function ($value, $key) {
                    return  $value == null;
                })->groupBy('instance_type')->map(function ($jobcardGroup, $key) {
                    return [
                        'type' => $jobcardGroup[0]['type'],           //  e.g) Open, Closed, e.t.c
                        'instance' => $jobcardGroup[0]['instance'],           //  e.g) Open, Closed, e.t.c
                        'total_count' => collect($jobcardGroup)->count(),        //  12
                    ];
                })->values();

                //  Format for inbetween stages
                $inbetweenAvailableStages = collect($inbetweenAvailableStages)->reject(function ($value, $key) {
                    return  $value == null;
                })->groupBy('name')->map(function ($jobcardGroup, $key) {
                    return [
                        'name' => $jobcardGroup[0]['name'],                      //  e.g) Open, Closed, e.t.c
                        'type' => $jobcardGroup[0]['type'],                      //  e.g) Open, Closed, e.t.c
                        'instance' => $jobcardGroup[0]['instance'],              //  e.g) Open, Closed, e.t.c
                        'total_count' => collect($jobcardGroup)->count(),        //  12
                    ];
                })->values();

                /*  This is a list of all the statistics we want returned in their respective order
                 *  - First get the companies default lifecycle
                 */
                $defaultLifecycle = $auth_user->company->lifecycles()->where('type', 'jobcard')->first();

                $expectedStats = $defaultLifecycle['stages'];

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
                $mainStats = $this->getMainStats($expectedStats, $mainAvailableStages);
                $subStats = $this->getSubStats($inbetweenAvailableStages);

                //  Merge the overview stats, stats and base currency into one collection
                $data = [
                        'overview_stats' => $mainStats,
                        'stats' => $subStats,
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

    public function getMainStats($expectedStats, $availableStages)
    {
        return collect($expectedStats)->map(function ($stage, $key) use ($availableStages) {
            $available = collect($availableStages)->map(function ($availableStage) use ($stage) {
                if ($availableStage['type'] == $stage['type'] && $availableStage['instance'] == $stage['instance']) {
                    return $availableStage;
                }
            })->reject(function ($value, $key) {
                return  $value == null;
            });

            if (count($available)) {
                return [
                            'name' => $stage['name'],        //  e.g) Open, Closed, e.t.c
                            'type' => $stage['type'],
                            'instance' => $stage['instance'],
                            'total_count' => collect($available)->values()[0]['total_count'],
                        ];
            } else {
                return [
                            'name' => $stage['name'],        //  e.g) Open, Closed, e.t.c
                            'type' => $stage['type'],
                            'instance' => $stage['instance'],
                            'total_count' => 0,
                        ];
            }
        });
    }

    public function getSubStats($availableStages)
    {
        //  This is a list of all the statistics we want returned in their respective order
        $expectedStats = ['Expired', 'Pending', 'Cancelled'];

        return collect($expectedStats)->map(function ($stat_name) use ($availableStages) {
            $availableStage = collect($availableStages)->filter(function ($stage) use ($stat_name) {
                return collect($stage)['name'] == $stat_name;
            })->values();

            if (count($availableStage)) {
                return [
                            'name' => $stat_name,        //  e.g) Open, Closed, e.t.c
                            'total_count' => $availableStage[0]['total_count'],
                        ];
            } else {
                return [
                            'name' => $stat_name,        //  e.g) Open, Closed, e.t.c
                            'total_count' => 0,
                        ];
            }
        });
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
