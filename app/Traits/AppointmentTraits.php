<?php

namespace App\Traits;

use App\Company;
use Illuminate\Pagination\LengthAwarePaginator;

trait AppointmentTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of appointment results.
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
         *  $allocation = all, appointment, branch
        /*
         *  The $allocation variable is used to determine where the data is being
         *  pulled from. The user may request data from three possible sources.
         *  1) Data may come from the associated authenticated user branch
         *  2) Data may come from the associated authenticated user appointment
         *  3) Data may come from the whole bucket meaning outside the scope of the
         *     authenticated user. This means we can access all possible models
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = strtolower(request('allocation'));

        /*
         *  $type = client, supplier
        /*
         *  The $type variable is used to determine which type of appointment to pull.
         *  The user may request data of type.
         *  1) client: A appointment that is listed as a client/customer
         *  2) supplier: A appointment that is listed as a supplier/vendor/hawker
         */
        $type = strtolower(request('type'));

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL APPOINTMENTS         *
            /**************************************************************/

            //  Get the current appointment instance
            $model = $this;
        } elseif ($allocation == 'branch') {
            /*****************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH APPOINTMENTS    *
            /****************************************************************/

            // Only get appointments associated to the appointment branch
            $model = $auth_user->companyBranch;
        } else {
            /******************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY APPOINTMENTS    *
            /*****************************************************************/

            //  Only get appointments associated to the appointment
            $model = $auth_user->company;
        }

        $appointments = $model->appointments();

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

        $order_join = 'appointments';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $appointments = $appointments->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $appointments = $appointments->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get all except trashed
            } else {
                //  Run query
                $appointments = $appointments->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            }

            //  Filter by status if specified
            if (request('status')) {
                //  Run query
                $stat_name = ucwords(request('status'));

                $appointments = $appointments->get();

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $appointments->load(oq_url_to_array(request('connections')));
                }

                $appointments = collect($appointments)->filter(function ($appointment, $key) use ($stat_name) {
                    return  $appointment['current_lifecycle_main_status']['name'] == $stat_name || $appointment['current_lifecycle_sub_status'] == $stat_name;
                });

                $page = request('page', 1);         //  The page number from the pagination list
                $perPage = request('limit', 10);    //  Pagination limit
                $appointments = new LengthAwarePaginator(
                                    collect($appointments->forPage($page, $perPage))->values(),
                                    $appointments->count(),
                                    $perPage,
                                    $page,
                                    ['path' => url('api/appointments')]
                                );
            } else {
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $appointments = $appointments->get();
                } else {
                    $appointments = $appointments->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                }

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $appointments->load(oq_url_to_array(request('connections')));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $appointments];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateShow() method:
     *
     *  This is used to return only one specific appointment.
     *
     */
    public function initiateShow($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed appointment
            if (request('withtrashed') == 1) {
                //  Run query
                $appointment = $this->withTrashed()->where('id', $appointment_id)->first();

            //  Get the non-trashed appointment
            } else {
                //  Run query
                $appointment = $this->where('id', $appointment_id)->first();
            }

            //  If we have any appointment so far
            if (count($appointment)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $appointment->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $appointment];
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
     *  This is used to approve an existing appointment. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the approval of
     *  the appointment.
     *
     */
    public function initiateApprove($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***********************************************************
         *   CHECK IF USER HAS PERMISSION TO APPROVE APPOINTMENT   *
         **********************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            //  Check if we have an appointment
            if ($appointment) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new AppointmentApproved($appointment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment approved
                $status = 'approved';
                $appointmentApprovedActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $appointment];
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

    /*  initiateUpdateRecurringSettingsSchedulePlan() method:
     *
     *  This is used to update the schedule plan of an existing appointment.
     *  The schedule plan is the (date, time and frequency) of how the appointments
     *  are sent to receipients over a time period. It also works to store the update
     *  activity and broadcasting of notifications to users concerning the updating of
     *  the appointment schedule.
     *
     */
    public function initiateUpdateRecurringSettingsSchedulePlan($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /******************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING APPOINTMENT SCHEDULES     *
         *****************************************************************************/

        /*************************************************
         *   VALIDATE APPOINTMENT INFORMATION            *
         *************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            $settingsData['editing']['schedulePlan'] = false;

            //  Mark the next stage with a status of editting
            if (!$appointment->has_set_recurring_delivery_plan) {
                $settingsData['editing']['deliveryPlan'] = true;
            }

            //  Create a template to hold the setting details
            $template = [
                'recurring_settings' => $settingsData,
            ];

            //  Update the appointment
            $appointment = $appointment->update($template);

            //  If the appointment was updated successfully
            if ($appointment) {
                //  re-retrieve the instance to get all of the fields in the table.
                $appointment = $this->where('id', $appointment_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new AppointmentRecurringSettingsSchedulePlanUpdated($appointment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring schedule plan updated
                $status = 'updated recurring schedule';
                $appointmentUpdatedActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $appointment];
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
