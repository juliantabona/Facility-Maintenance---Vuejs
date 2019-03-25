<?php

namespace App\Traits;

use App\Notifications\JobcardApproved;

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
        if (request('paginate') == 0 || request('paginate') == 1) {
            $config['paginate'] = request('paginate') == 1 ? true : false;
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
         *     authenticated user. This means we can access all possible models
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

        if (request('step')) {
            try {
                /*  This is how we get the jobcard step allocation template
                 *  It must be of "type" equal to "jobcard", and "selected" equal to "1"
                 */
                $jobcardTemplateId = $user->company->formTemplate
                                          ->where('type', 'jobcard')
                                          ->where('selected', 1)
                                          ->first()
                                          ->id;

                /*  Filter only to the jobcards beloging to the specified step.
                 */
                $jobcards = $jobcards->whereHas('statusLifecycle', function ($query) use ($jobcardTemplateId) {
                    $query->where('step', request('step'))
                          ->where('form_template_id', $jobcardTemplateId);
                });
            } catch (\Exception $e) {
                //  Log the error
                $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
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

        $order_join = 'jobcards';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $jobcards = $jobcards->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $jobcards = $jobcards->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $jobcards = $jobcards->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $jobcards = $jobcards->get();
            }

            //  If we have any jobcards so far
            if (count($jobcards)) {
                //  Eager load other relationships wanted if specified
                if (strtolower(request('connections'))) {
                    $jobcards->load(oq_url_to_array(strtolower(request('connections'))));
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

    /*  summarize() method:
     *
     *  This is used to limit the information of an quotation to very specific
     *  columns that can then be used for storage e.g) in the instance of
     *  adding a recent activity. We may only want to summarize the quotation
     *  to very important information, rather tha storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want, while also removing any custom attributes we do not
     *  want to store.
     *
     */
    public function summarize()
    {
        //  Collect and select table columns
        return collect(
            $this->select(
                'title', 'description', 'start_date', 'end_date', 'company_branch_id', 'company_id', 'client_id', 'is_public'
            )->first())
            //  Remove all custom attributes since the are all based on recent activities
            ->forget(['createdBy', 'authourizedBy', 'deadline', 'deadlineArray', 'deadlineInWords', 'statusSummary',
                      'last_approved_activity', 'has_approved', 'current_activity_status', 'activity_count',
                      'recent_activities',
            ]);
    }
}
