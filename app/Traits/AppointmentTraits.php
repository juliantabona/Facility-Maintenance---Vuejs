<?php

namespace App\Traits;

use DB;
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
         *     authenticated user. This means we can access all possible records
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
         *  The $appointment is a collection of the appointment to be stored.
         */
        $appointment = request('appointment');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE APPOINTMENT    *
         ******************************************************/

        /*********************************************
         *   VALIDATE APPOINTMENT INFORMATION            *
         ********************************************/

        //  Create a template to hold the appointment details
        $template = $template ?? [
            'subject' => $appointment['subject'],
            'agenda' => $appointment['agenda'],
            'start_date' => $appointment['start_date'],
            'end_date' => $appointment['end_date'],
            'location' => $appointment['location'],
            'client_id' => $appointment['client_id'] ?? null,
            'client_type' => $appointment['client_model_type'] ?? '',
            'company_branch_id' => $auth_user->company_branch_id,
            'company_id' => $auth_user->company_id,
        ];

        try {
            //  Create the appointment
            $appointment = $this->create($template)->fresh();

            //  If the appointment was created successfully
            if ($appointment) {
                //  Start with an empty categories, assigned_staff
                $categories = [];
                $assignedStaff = [];

                //  Foreach of the categories
                foreach (request('appointment')['categories'] as $key => $id) {
                    //  Store with the following details corresponding to the category table columns
                    $categories[$key] = [
                        'category_id' => $id,
                        'trackable_id' => $appointment->id,
                        'trackable_type' => 'appointment',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }

                $categories = DB::table('category_allocations')->insert($categories);

                //  Foreach of the assigned staff
                foreach (request('appointment')['assigned_staff'] as $key => $id) {
                    //  Store with the following details corresponding to the assigned staff table columns
                    $assignedStaff[$key] = [
                        'user_id' => $id,
                        'trackable_id' => $appointment->id,
                        'trackable_type' => 'appointment',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }

                $assignedStaff = DB::table('staff_allocations')->insert($assignedStaff);

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //$auth_user->notify(new AppointmentCreated($appointment));

                //  re-retrieve the instance to get all of the fields in the table.
                $appointment = $appointment->fresh();

                //  If we have any appointments so far
                if ($appointment) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $appointment->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment created
                $status = 'created';
                $appointmentCreatedActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

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
            if ($appointment) {
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

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing appointment. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the appointment.
     *
     */
    public function initiateUpdate($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $appointment is a collection of the appointment to be stored.
         */
        $appointment = request('appointment');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE APPOINTMENT    *
         ******************************************************/

        /*********************************************
         *   VALIDATE APPOINTMENT INFORMATION            *
         ********************************************/

        //  Create a template to hold the appointment details
        $template = $template ?? [
            'subject' => $appointment['subject'],
            'agenda' => $appointment['agenda'],
            'start_date' => $appointment['start_date'],
            'end_date' => $appointment['end_date'],
            'location' => $appointment['location'],
            'client_id' => $appointment['client_id'] ?? null,
            'client_type' => $appointment['client_model_type'] ?? '',
            'company_branch_id' => $auth_user->company_branch_id,
            'company_id' => $auth_user->company_id,
        ];

        try {
            //  Update the appointment
            $appointment = $this->where('id', $appointment_id)->first()->update($template);

            //  If the appointment was updated successfully
            if ($appointment) {
                //  Re-fresh appointment
                $appointment = $this->where('id', $appointment_id)->first();

                //  Start with an empty categories, assigned_staff
                $categories = [];
                $assignedStaff = [];

                if( isset( request('appointment')['categories'] ) ){
                    if( COUNT( request('appointment')['categories'] ) ){

                        //  Foreach of the categories
                        foreach (request('appointment')['categories'] as $key => $id) {
                            //  Store with the following details corresponding to the category table columns
                            $categories[$key] = [
                                'category_id' => $id,
                                'trackable_id' => $appointment->id,
                                'trackable_type' => 'appointment',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        //  Delete all previous categories
                        DB::table('category_allocations')->where('trackable_type', 'appointment')->where('trackable_id', $appointment->id)->delete();

                        //  Add new categories
                        $categories = DB::table('category_allocations')->insert($categories);
                    }
                }


                if( isset( request('appointment')['assigned_staff'] ) ){
                    if( COUNT( request('appointment')['assigned_staff'] ) ){

                        //  Foreach of the assigned staff
                        foreach (request('appointment')['assigned_staff'] as $key => $id) {
                            //  Store with the following details corresponding to the assigned staff table columns
                            $assignedStaff[$key] = [
                                'user_id' => $id,
                                'trackable_id' => $appointment->id,
                                'trackable_type' => 'appointment',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        //  Delete all previous staff
                        DB::table('staff_allocations')->where('trackable_type', 'appointment')->where('trackable_id', $appointment->id)->delete();

                        //  Add new staff
                        $assignedStaff = DB::table('staff_allocations')->insert($assignedStaff);
                    }
                }
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //$auth_user->notify(new AppointmentCreated($appointment));

                //  re-retrieve the instance to get all of the fields in the table.
                $appointment = $appointment->fresh();

                //  If we have any appointments so far
                if ($appointment) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $appointment->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment created
                $status = 'created';
                $appointmentCreatedActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

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

    /*  initiateSend() method:
     *
     *  This is used to send an existing appointment (via email).
     *  It also works to store the sent activity and broadcasting
     *  of notifications to users concerning the sending of
     *  the appointment.
     *
     */
    public function initiateSendAppointment($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND APPOINTMENT      *
         ******************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            //  Check if we have an appointment
            if ($appointment) {
                /***********************************
                 *   SEND APPOINTMENT VIA EMAIL/SMS    *
                 ***********************************/

                //  Accepted Delivery Methods
                $deliveryMethods = request('deliveryMethods');

                //  If specified to send appointment via sms
                if (in_array('Sms', $deliveryMethods)) {
                    //  Send via sms
                    $this->sendAppointmentAsSMS($appointment);
                }

                //  If specified to send appointment via mail
                if (in_array('Email', $deliveryMethods)) {
                    //  send via email
                    $this->sendAppointmentAsMail($appointment);
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

    /*  sendAppointmentAsSMS() method:
     *
     *  This is used to send the appointment via SMS only.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every sms sent will be
     *  recorded as a recent activity with the phone and sms saved.
     *
     */
    public function sendAppointmentAsSMS($appointment, $phones = null, $smsMessage = null, $user = null)
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /***************************
         *   GET SMS DETAILS       *
         ***************************/
        $phones = $phones ?? request('sms')['phones'];
        $smsMessage = $this->getCompiledSmsMessage($appointment);

        //  Filter and only get the phones set to active

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        if (request('test') == 1) {
            $status = 'sent test sms';
        // $auth_user->notify(new AppointmentTestSmsSent($appointment));
        } else {
            $status = 'sent sms';
            // $auth_user->notify(new AppointmentSmsSent($appointment));
        }

        //  If we have phones and a message to send
        if (!empty($phones) && !empty($smsMessage)) {
            //  Foreach phone number provided
            foreach ($phones as $phone) {
                //  If $phone['show'] = true it is an active phone number
                //  If $phone['show'] = false it is not an active phone number
                //  We only send messages to phone numbers set to active
                if ($phone['show']) {
                    //  Get the calling code
                    $callingCode = '+'.$phone['calling_code'];

                    //  Get the phone number
                    $phoneNumber = $phone['number'];

                    //  Send the sms message to the given number
                    Twilio::message($callingCode.$phoneNumber, $smsMessage);

                    /*****************************
                     *   RECORD ACTIVITY         *
                     *****************************/

                    //  Structure mail template
                    $sms = ['phone' => $phone, 'message' => $smsMessage];

                    //  Record activity of appointment sent
                    $appointmentSentActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize(), 'sms' => $sms]);
                }
            }
        }
    }

    public function getCompiledSmsMessage($appointment)
    {
        $ref_no = $appointment->id;
        $subject = $appointment->subject;
        $agenda = $appointment->agenda;
        $start_date = Carbon::parse($appointment->start_date)->format('H:iA M d Y');
        $end_date = Carbon::parse($appointment->end_date)->format('H:iA M d Y');
        $location = $appointment->location;
        $company = $appointment->company;
        $client = $appointment->client;

        $characterLimit = 160;

        //  Company info text limit = 23
        $company_name = $this->truncateWithDots(trim($company['name']), 21).(strlen($company['name']) <= 21 ? ':' : '');       //  Optimum Quality:
        //  Reference text limit = No limit (est. 16)
        $ref_no = 'Appt #'.$ref_no;                                       //  Appt #xxxxxxxxxxx e.g) Appt #10000000000
        //  Start date text limit = No limit (est. 21)
        $start_date = ' @ '.$start_date;                                  //  at 08:30AM 15 Feb 2018
        //  Location text limit = No limit (est. 21)
        $location_text = '.Address: '.$location;                          //  .Address:Commerce Park unit 2,1st floor,office 3
        //  Reply for payment text limit = No limit (est. 32)
        $reply_with = '.Reply YES to confirm, NO to reschedule';            //  .Reply YES to confirm, NO to reschedule

        //  Subject text limit = Remaining characters left
        $charLeft = ($characterLimit - strlen($company_name.$ref_no.$start_date.$location_text.$reply_with));
        $subject_text = $this->truncateWithDots(' ' + $subject, $charLeft);     //  Adjustment of braces and whitening of teeth

        $message = $company_name.$ref_no.$subject_text.$start_date.$location_text.$reply_with;

        /*
         *  EXAMPLE COMPILED TEXT
         *
         **************************************************************************************************************
         *  Optimum Quality:Appointment #043 Adjustment of braces and whitening of teeth at 08:30AM 15 Feb 2018.      *
         *  Address:Commerce Park unit 2,1st floor,office 3.Reply YES to confirm, NO to reschedule                    *
         **************************************************************************************************************
         */

        //  Return the compiled message
        return message;
    }

    public function truncateWithDots($string, $limit)
    {
        return (strlen($string) > $limit) ? substr($string, $limit - 3).'...' : $string;
    }

    /*  sendAppointmentAsMail() method:
     *
     *  This is used to send the appointment via Email only. It takes the
     *  actual appointment to build a pdf to send to the receipient. It
     *  will also add any CC or BCC within the mail if provided.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every email sent will be
     *  recorded as a recent activity with the mail details saved.
     *
     */

    public function sendAppointmentAsMail($appointment, $primaryEmails = null, $ccEmails = null, $bccEmails = null, $subject = null, $message = null, $user = null)
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /*****************************
         *   GET EMAIL DETAILS       *
         *****************************/

        $primaryEmails = $primaryEmails ?? request('mail')['primaryEmails'];
        $ccEmails = $ccEmails ?? request('mail')['ccEmails'];
        $bccEmails = $bccEmails ?? request('mail')['bccEmails'];
        $subject = $subject ?? request('mail')['subject'];
        $message = $message ?? request('mail')['message'];

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        //  If this is a test email
        if (request('test') == 1) {
            $status = 'sent test email';
        // $auth_user->notify(new AppointmentTestEmailSent($appointment));

        //  Otherwise if this is not a test email
        } else {
            $status = 'sent email';
            // $auth_user->notify(new AppointmentEmailSent($appointment));
        }

        //  Appointment PDF
        $appointmentPDF = PDF::loadView('pdf.appointment', array('appointment' => $appointment));

        //  Get appointment name
        if (!empty($appointment->start_date)) {
            //  Get the appointment reference and date
            $pdfName = 'Appointment - '.
                       $appointment->id.' - '.
                       Carbon::parse($appointment->start_date)->format('M d Y').
                       '.pdf';
        } else {
            //  Otherwise get appointment name from the appointment id
            $pdfName = 'Appointment - '.$appointment->id.'.pdf';
        }

        /***********************************************
         *   REPLACE SHORTCODES WITH ACTUAL CONTENT    *
         ***********************************************/

        $message = $this->replaceShortcodes($appointment, $message);
        $subject = $this->replaceShortcodes($appointment, $subject);

        //  Foreach email
        foreach ($primaryEmails as $primaryEmail) {
            /******************************
             *   SEND APPOINTMENT VIA MAIL    *
             ******************************/

            Mail::to($primaryEmail)->send(new AppointmentMail($subject, $message, $appointmentPDF, $pdfName));

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $mail = ['email' => $primaryEmail, 'subject' => $subject, 'message' => $message];

            //  Record activity of appointment sent
            $appointmentSentActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize(), 'mail' => $mail]);
        }
    }

    /*  replaceShortcodes() method:
     *
     *  This is used to replace all shortcodes within a given message
     *  The method goes and checks for any shortcode that can be converted
     *  to actual information used in the appointment. E.g it would replace
     *  the shortcode [grand_total] with the actual grand total amount
     *  of the appointment.
     *
     */
    public function replaceShortcodes($appointment, $data)
    {
        $ref_no = $appointment->id;
        $subject = $appointment->subject;
        $agenda = $appointment->agenda;
        $start_date = (new Carbon($appointment->start_date))->format('H:iA M d Y');
        $end_date = (new Carbon($appointment->end_date))->format('H:iA M d Y');
        $location = $appointment->location;
        $company = $appointment->company;
        $client = $appointment->client;

        //  Custom Appointment Variables - Shortcodes
        $customFields = [
            '[ref_no]' => $ref_no,
            '[subject]' => $subject,
            '[agenda]' => $agenda,
            '[start_date]' => $start_date,
            '[end_date]' => $end_date,
            '[location]' => $location,
            '[client_company_name]' => $client['name'] ?? '',
            '[client_first_name]' => $client['first_name'] ?? '',
            '[client_last_name]' => $client['last_name'] ?? '',
            '[client_full_name]' => $client['full_name'] ?? '',
            '[client_email]' => $client['email'],
            '[my_company_name]' => $company['name'],
            '[my_company_email]' => $company['email'],
        ];

        $search = array_keys($customFields);
        $replace = array_values($customFields);

        //  Return the replaced data - All shortcodes have been replaced with actual content
        return str_replace($search, $replace, $data);
    }

    /*  initiateSkipSend() method:
     *
     *  This is used to skip sending an existing appointment (via email).
     *  It also works to store the skip send activity.
     *
     */
    public function initiateSkipSend($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***********************************************************
         *   CHECK IF USER HAS PERMISSION TO SKIP SEND APPOINTMENT     *
         **********************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            //  Check if we have an appointment
            if ($appointment) {
                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment skipped sending
                $status = 'skipped sending';
                $appointmentSkipSendActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

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

    /*  initiateConfirmation() method:
     *
     *  This is used to record confirmation of an existing appointment.
     *  It also works to store the paid activity and broadcasting
     *  of notifications to users concerning the confirmation of the appointment.
     *
     */
    public function initiateConfirmation($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /****************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD APPOINTMENT PAYMENT     *
         ****************************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            //  Check if we have an appointment
            if ($appointment) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new AppointmentConfirmed($appointment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment paid
                $status = 'confirmed';
                $appointmentPaidActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

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

    /*  initiateCancelConfirmation() method:
     *
     *  This is used to cancel a recorded confirmation of an existing appointment.
     *  It also works to store the cancelled confirmation activity and broadcasting
     *  of notifications to users concerning the confirmation of the appointment.
     *
     */
    public function initiateCancelConfirmation($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /****************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD APPOINTMENT PAYMENT     *
         ****************************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            //  Check if we have an appointment
            if ($appointment) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new AppointmentConfirmationCancelled($appointment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment cancelled confirmation
                $status = 'cancelled confirmation';
                $appointmentConfirmationCancelledActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

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

    public function initiateUpdateReminders($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /****************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD APPOINTMENT PAYMENT     *
         ****************************************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            //  Check if we have an appointment
            if ($appointment) {
                //  Start with an empty reminder array
                $reminders = [];

                //  Foreach of the reminder days
                foreach (request('reminders')['days'] as $key => $reminder) {
                    //  Assume that we can't email or sms
                    $can_email = 0;
                    $can_sms = 0;

                    //  Now we need to confirm if we can actually email or sms
                    foreach (request('reminders')['method'] as $method) {
                        if ($method == 'email') {
                            $can_email = 1;
                        }

                        if ($method == 'sms') {
                            $can_sms = 1;
                        }
                    }

                    //  Store each reminder with the following details corresponding to the reminder table columns
                    $reminders[$key] = [
                        'days_after' => request('reminders')['days'][$key],
                        'type' => 'confirmation',
                        'can_sms' => $can_sms,
                        'can_email' => $can_email,
                        'email' => $appointment->client['email'],
                        'phone' => $appointment->client['phone'],
                        'company_branch_id' => $auth_user->company_branch_id,
                        'company_id' => $auth_user->company_id,
                        'trackable_id' => $appointment->id,
                        'trackable_type' => 'appointment',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }

                //  Delete old reminders
                $deleted = $appointment->reminders()->delete();

                //  Insert new reminders
                $appointment = $appointment->reminders()->insert($reminders);

                //  Re-fresh appointment to get the latest sent status from our recent activties
                $appointment = $this->where('id', $appointment_id)->with('reminders')->first();

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of appointment updated confirmation reminders
                $status = 'updated reminders';
                $appointmentConfirmationCancelledActivity = oq_saveActivity($appointment, $auth_user, $status, ['appointment' => $appointment->summarize()]);

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

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING APPOINTMENT SCHEDULES *
         **************************************************************************/

        /*********************************************
         *   VALIDATE APPOINTMENT INFORMATION        *
         *********************************************/

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

    public function initiateUpdateRecurringSettingsDeliveryPlan($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING APPOINTMENT SCHEDULES     *
         **************************************************************************/

        /*********************************************
         *   VALIDATE APPOINTMENT INFORMATION            *
         *********************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            $settingsData['editing']['schedulePlan'] = false;
            $settingsData['editing']['deliveryPlan'] = false;

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

                //  $auth_user->notify(new AppointmentRecurringSettingsDeliveryPlanUpdated($appointment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring delivery plan updated
                $status = 'updated recurring delivery';
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

    public function initiateApproveRecurringSettings($appointment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING APPOINTMENT SCHEDULES *
         **************************************************************************/

        /*********************************************
         *   VALIDATE APPOINTMENT INFORMATION        *
         *********************************************/

        try {
            //  Get the appointment
            $appointment = $this->where('id', $appointment_id)->first();

            $settingsData['editing']['schedulePlan'] = false;
            $settingsData['editing']['deliveryPlan'] = false;

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

                //  $auth_user->notify(new AppointmentRecurringSettingsApproved($appointment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring delivery plan updated
                $status = 'approved recurring settings';
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
