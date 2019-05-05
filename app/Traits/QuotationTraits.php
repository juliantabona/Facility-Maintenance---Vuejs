<?php

namespace App\Traits;

//  Mails
use Mail;
use App\Mail\QuotationMail;
use App\Mail\QuotationReceiptMail;
//  Notifications
use App\Notifications\QuotationCreated;
use App\Notifications\QuotationUpdated;
use App\Notifications\QuotationApproved;
//  Notifications when sending the quotation via email or sms
use App\Notifications\QuotationSmsSent;
use App\Notifications\QuotationTestSmsSent;
use App\Notifications\QuotationEmailSent;
use App\Notifications\QuotationTestEmailSent;
use App\Notifications\QuotationReceiptSmsSent;
use App\Notifications\QuotationReceiptTestSmsSent;
use App\Notifications\QuotationReceiptEmailSent;
use App\Notifications\QuotationReceiptTestEmailSent;
//  Other
use PDF;
use App\Invoice;
use App\Company;
use Carbon\Carbon;
use Twilio as Twilio;
use Illuminate\Pagination\LengthAwarePaginator;

trait QuotationTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of quotation results.
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
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specificaclly related to
         *  the specified company id. It is useful for scenerios where we
         *  want only quotations of that company only
         */
        $companyId = request('companyId');

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL QUOTATIONS         *
            /**********************************************************/

            //  Get the current quotation instance
            $quotations = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH QUOTATIONS    *
            /*************************************************************/

            // Only get quotations associated to the company branch
            $quotations = $auth_user->companyBranch->quotations();
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY QUOTATIONS    *
            /**************************************************************/

            //  Only get quotations associated to the company
            $quotations = $auth_user->company->quotations();
        }

        //  Only get specific company data only if specified
        if ($companyId) {
            /**************************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY QUOTATIONS    *
            /*************************************************************************/

            $quotations = $quotations->where('client_id', $companyId);
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

        $order_join = 'quotations';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $quotations = $quotations->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $quotations = $quotations->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get all except trashed
            } else {
                //  Run query
                $quotations = $quotations->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            }

            //  Filter by status if specified
            if (request('status')) {
                //  Run query
                $stat_name = ucwords(request('status'));

                $quotations = $quotations->get();

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $quotations->load(oq_url_to_array(request('connections')));
                }

                //  If we have a special case to display outstanding quotations, we need to
                //  Select quotations with statuses of 'Draft', 'Approved', 'Sent', 'Expired'
                if ($stat_name == 'Outstanding') {
                    //  List of statuses considered as outstanding
                    $outstanding = ['Draft', 'Approved', 'Sent', 'Expired'];
                    $quotations = collect($quotations)->whereIn('current_activity_status', $outstanding);
                } else {
                    $quotations = collect($quotations)->where('current_activity_status', $stat_name);
                }

                $page = request('page', 1);         //  The page number from the pagination list
                $perPage = request('limit', 10);    //  Pagination limit
                $quotations = new LengthAwarePaginator(
                                    collect($quotations->forPage($page, $perPage))->values(),
                                    $quotations->count(),
                                    $perPage,
                                    $page,
                                    ['path' => url('api/quotations')]
                                );
            } else {
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $quotations = $quotations->get();
                } else {
                    $quotations = $quotations->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                }

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $quotations->load(oq_url_to_array(request('connections')));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $quotations];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateShow() method:
     *
     *  This is used to return only one specific quotation.
     *
     */
    public function initiateShow($quotation_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed quotation
            if (request('withtrashed') == 1) {
                //  Run query
                $quotation = $this->withTrashed()->where('id', $quotation_id)->first();

            //  Get the non-trashed quotation
            } else {
                //  Run query
                $quotation = $this->where('id', $quotation_id)->first();
            }

            //  If we have any quotation so far
            if ($quotation) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $quotation->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $quotation];
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

    /*  initiateCreate() method:
     *
     *  This is used to create a new quotation. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the quotation.
     *
     */
    public function initiateCreate()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $quotation is a collection of the quotation to be stored.
         */
        $quotation = request('quotation');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE QUOTATION    *
         ******************************************************/

        /*********************************************
         *   VALIDATE QUOTATION INFORMATION            *
         ********************************************/

        //  Create a template to hold the quotation details
        $template = [
            'heading' => $quotation['heading'],
            'reference_no_title' => $quotation['reference_no_title'],
            'reference_no_value' => $quotation['reference_no_value'],
            'created_date_title' => $quotation['created_date_title'],
            'created_date_value' => $quotation['created_date_value'],
            'expiry_date_title' => $quotation['expiry_date_title'],
            'expiry_date_value' => $quotation['expiry_date_value'],
            'sub_total_title' => $quotation['sub_total_title'],
            'sub_total_value' => $quotation['sub_total_value'],
            'grand_total_title' => $quotation['grand_total_title'],
            'grand_total_value' => $quotation['grand_total_value'],
            'currency_type' => $quotation['currency_type'],
            'calculated_taxes' => $quotation['calculated_taxes'],
            'quotation_to_title' => $quotation['quotation_to_title'],
            'customized_company_details' => $quotation['customized_company_details'],
            'customized_client_details' => $quotation['customized_client_details'],
            'client_id' => $quotation['customized_client_details']['id'],
            'table_columns' => $quotation['table_columns'],
            'items' => $quotation['items'],
            'notes' => $quotation['notes'],
            'colors' => $quotation['colors'],
            'footer' => $quotation['footer'],
            'company_branch_id' => $auth_user->company_branch_id,
            'company_id' => $auth_user->company_id,
        ];

        try {
            //  Create the quotation
            $quotation = $this->create($template);

            //  If the quotation was created successfully
            if ($quotation) {
                //  Generate a reference number
                $quotationNumber = str_pad($quotation->id, 3, '0', STR_PAD_LEFT);

                //  Update the reference number
                $quotation->update(['reference_no_value' => $quotationNumber]);

                //  re-retrieve the instance to get all of the fields in the table.
                $quotation = $quotation->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new QuotationCreated($quotation));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of quotation created
                $status = 'created';
                $quotationCreatedActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $quotation];
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
     *  This is used to update an existing quotation. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the quotation.
     *
     */
    public function initiateUpdate($quotation_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $quotation is a collection of the quotation to be stored.
         */
        $quotation = request('quotation');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE QUOTATION    *
         ******************************************************/

        /*********************************************
         *   VALIDATE QUOTATION INFORMATION            *
         ********************************************/

        //  Create a template to hold the quotation details
        $template = [
            'heading' => $quotation['heading'],
            'reference_no_title' => $quotation['reference_no_title'],
            'reference_no_value' => $quotation['reference_no_value'],
            'created_date_title' => $quotation['created_date_title'],
            'created_date_value' => $quotation['created_date_value'],
            'expiry_date_title' => $quotation['expiry_date_title'],
            'expiry_date_value' => $quotation['expiry_date_value'],
            'sub_total_title' => $quotation['sub_total_title'],
            'sub_total_value' => $quotation['sub_total_value'],
            'grand_total_title' => $quotation['grand_total_title'],
            'grand_total_value' => $quotation['grand_total_value'],
            'currency_type' => $quotation['currency_type'],
            'calculated_taxes' => $quotation['calculated_taxes'],
            'quotation_to_title' => $quotation['quotation_to_title'],
            'customized_company_details' => $quotation['customized_company_details'],
            'customized_client_details' => $quotation['customized_client_details'],
            'client_id' => $quotation['customized_client_details']['id'],
            'table_columns' => $quotation['table_columns'],
            'items' => $quotation['items'],
            'notes' => $quotation['notes'],
            'colors' => $quotation['colors'],
            'footer' => $quotation['footer'],
            'company_branch_id' => $auth_user->company_branch_id,
            'company_id' => $auth_user->company_id,
        ];

        try {
            //  Update the quotation
            $quotation = $this->where('id', $quotation_id)->first()->update($template);

            //  If the quotation was updated successfully
            if ($quotation) {
                //  re-retrieve the instance to get all of the fields in the table.
                $quotation = $this->where('id', $quotation_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new QuotationUpdated($quotation));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of quotation updated
                $status = 'updated';
                $quotationUpdatedActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $quotation];
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
     *  This is used to approve an existing quotation. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the approval of
     *  the quotation.
     *
     */
    public function initiateApprove($quotation_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO APPROVE QUOTATION   *
         ******************************************************/

        try {
            //  Get the quotation
            $quotation = $this->where('id', $quotation_id)->first();

            //  Check if we have an quotation
            if ($quotation) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new QuotationApproved($quotation));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of quotation approved
                $status = 'approved';
                $quotationApprovedActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $quotation];
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
     *  This is used to send an existing quotation (via email).
     *  It also works to store the sent activity and broadcasting
     *  of notifications to users concerning the sending of
     *  the quotation.
     *
     */
    public function initiateSendQuotation($quotation_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND QUOTATION      *
         ******************************************************/

        try {
            //  Get the quotation
            $quotation = $this->where('id', $quotation_id)->first();

            //  Check if we have an quotation
            if ($quotation) {
                /***********************************
                 *   SEND QUOTATION VIA EMAIL/SMS    *
                 ***********************************/

                //  Accepted Delivery Methods
                $deliveryMethods = request('deliveryMethods');

                //  If specified to send quotation via sms
                if (in_array('Sms', $deliveryMethods)) {
                    //  Send via sms
                    $this->sendQuotationAsSMS($quotation);
                }

                //  If specified to send quotation via mail
                if (in_array('Email', $deliveryMethods)) {
                    //  send via email
                    $this->sendQuotationAsMail($quotation);
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $quotation];
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

    /*  sendQuotationAsSMS() method:
     *
     *  This is used to send the quotation via SMS only.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every sms sent will be
     *  recorded as a recent activity with the phone and sms saved.
     *
     */
    public function sendQuotationAsSMS($quotation, $phones = null, $smsMessage = null, $user = null)
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /***************************
         *   GET SMS DETAILS       *
         ***************************/
        $phones = $phones ?? request('sms')['phones'];
        $smsMessage = $this->getCompiledSmsMessage($quotation);

        //  Filter and only get the phones set to active

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        if (request('test') == 1) {
            $status = 'sent quotation test sms';
            $auth_user->notify(new QuotationTestSmsSent($quotation));
        } else {
            $status = 'sent sms';
            $auth_user->notify(new QuotationSmsSent($quotation));
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
                    $callingCode = '+'.$phone['calling_code']['calling_code'];

                    //  Get the phone number
                    $phoneNumber = $phone['number'];

                    //  Send the sms message to the given number
                    Twilio::message($callingCode.$phoneNumber, $smsMessage);

                    /*****************************
                     *   RECORD ACTIVITY         *
                     *****************************/

                    //  Structure mail template
                    $sms = ['phone' => $phone, 'message' => $smsMessage];

                    //  Record activity of quotation sent receipt
                    $quotationSentActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize(), 'sms' => $sms]);
                }
            }
        }
    }

    public function getCompiledSmsMessage($quotation)
    {
        $items = '';
        $referenceNo = $quotation->reference_no_value;
        $currency = $quotation['currency_type']['currency']['symbol'];
        $grand_total = $currency.number_format($quotation->grand_total_value, 2, ',', '.');
        $expiry_date = (new Carbon($quotation->expiry_date_value))->format('M d Y');
        $client = $quotation->customized_client_details;
        $company = $quotation->customized_company_details;

        foreach ($quotation->items as $x => $item) {
            $x == 0 ? $items .= '' : $items .= ' ';
            $items .= ($item['quantity'].'x '.($item['name']));
        }

        $characterLimit = 160;
        //  Company info text limit = 23
        $companyName = $this->truncateWithDots(trim($company['name']), 21).(strlen($company['name']) <= 21 ? ':' : '');       //  Optimum Quality:
        //  Reference text limit = 16
        $reference = 'Quotation #'.$referenceNo;                        //  Quotation #002
        //  Amount text limit = 20
        $amount = 'Amount '.$grand_total;                           //  Amount P350.00
        //  Due date text limit = 21
        $dueDate = ' due '.$expiry_date;                              //  due on 15 Feb 2018
        //  Reply for payment text limit = 32
        $replyWith = '.Reply with '.$referenceNo.'#<pin> to pay';     //  Reply with 002#<pin> to pay

        //  items text limit = 49
        $charLeft = ($characterLimit - strlen($companyName.$reference.$amount.$dueDate.$replyWith));
        $items = $this->truncateWithDots(' for '.$items.(strlen($items) <= $charLeft ? '.' : ''), $charLeft);    //  for 1x Basic Website, 1x Web Hosting, 5x Emails.

        $message = $companyName.$reference.$items.$amount.$dueDate.$replyWith;

        return $message;
    }

    public function truncateWithDots($string, $limit)
    {
        return (strlen($string) > $limit) ? substr($string, $limit - 3).'...' : $string;
    }

    /*  sendQuotationAsMail() method:
     *
     *  This is used to send the quotation via Email only. It takes the
     *  actual quotation to build a pdf to send to the receipient. It
     *  will also add any CC or BCC within the mail if provided.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every email sent will be
     *  recorded as a recent activity with the mail details saved.
     *
     */

    public function sendQuotationAsMail($quotation, $primaryEmails = null, $ccEmails = null, $bccEmails = null, $subject = null, $message = null, $user = null)
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /******************sendQuotationAsSMS***********
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
            $status = 'sent quotation test email';
            $auth_user->notify(new QuotationTestEmailSent($quotation));

        //  Otherwise if this is not a test email
        } else {
            $status = 'sent email';
            $auth_user->notify(new QuotationEmailSent($quotation));
        }

        //  Quotation PDF
        $quotationPDF = PDF::loadView('pdf.quotation', array('quotation' => $quotation));

        //  Get quotation name
        if (!empty($quotation->details['heading']) && !empty($quotation['reference_no_value'])) {
            //  Get the quotation name from heading, reference and date
            $pdfName = $quotation->details['heading'].' - '.
                       $quotation->details['reference_no_value'].' - '.
                       Carbon::parse($quotation['created_date_value'])->format('M d Y').
                       '.pdf';
        } else {
            //  Otherwise get quotation name from the quotation id
            $pdfName = 'Quotation - '.$quotation->id.'.pdf';
        }

        /***********************************************
         *   REPLACE SHORTCODES WITH ACTUAL CONTENT    *
         ***********************************************/

        $message = $this->replaceShortcodes($quotation, $message);
        $subject = $this->replaceShortcodes($quotation, $subject);

        //  Foreach email
        foreach ($primaryEmails as $primaryEmail) {
            /******************************
             *   SEND QUOTATION VIA MAIL    *
             ******************************/

            Mail::to($primaryEmail)->send(new QuotationMail($subject, $message, $quotationPDF, $pdfName));

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $mail = ['email' => $primaryEmail, 'subject' => $subject, 'message' => $message];

            //  Record activity of quotation sent receipt
            $quotationSentActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize(), 'mail' => $mail]);
        }
    }

    /*  sendQuotationReceiptAsSMS() method:
     *
     *  This is used to send the quotation receipt via SMS only.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every sms sent will be
     *  recorded as a recent activity with the phone and sms saved.
     *
     */
    public function sendQuotationReceiptAsSMS($quotation)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***************************
         *   GET SMS DETAILS       *
         ***************************/
        $phones = request('sms')['phones'];
        $smsMessage = request('sms')['message'];

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        if (request('test') == 1) {
            $status = 'sent quotation receipt test sms';
            $auth_user->notify(new QuotationReceiptTestSmsSent($quotation));
        } else {
            $status = 'sent quotation receipt sms';
            $auth_user->notify(new QuotationReceiptSmsSent($quotation));
        }

        //  Foreach phone number provided
        foreach ($phones as $phone) {
            //  Get the calling code
            $callingCode = '+'.$phone['calling_code']['calling_code'];

            //  Get the phone number
            $phoneNumber = $phone['number'];

            //  Send the sms message to the given number
            Twilio::message($callingCode.$phoneNumber, $smsMessage);

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $sms = ['phone' => $phone, 'message' => $smsMessage];

            //  Record activity of quotation sent receipt
            $quotationSentActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize(), 'sms' => $sms]);
        }
    }

    /*  sendQuotationReceiptAsMail() method:
     *
     *  This is used to send the quotation receipt via Email only. It
     *  takes the actual quotation to build a pdf to send to the receipient.
     *  It will also add any CC or BCC within the mail if provided.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every email sent will be
     *  recorded as a recent activity with the mail details saved.
     *
     */
    public function sendQuotationReceiptAsMail($quotation)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        //  If this is a test email
        if (request('test') == 1) {
            $status = 'sent quotation receipt test email';
            $auth_user->notify(new QuotationReceiptTestEmailSent($quotation));

        //  Otherwise if this is not a test email
        } else {
            $status = 'sent quotation receipt email';
            $auth_user->notify(new QuotationReceiptEmailSent($quotation));
        }

        /*****************************
         *   GET EMAIL DETAILS       *
         *****************************/

        $primaryEmails = request('mail')['primaryEmails'];
        $ccEmails = request('mail')['ccEmails'];
        $bccEmails = request('mail')['bccEmails'];
        $subject = request('mail')['subject'];
        $message = request('mail')['message'];

        //  Get Receipt PDF
        $receiptPDF = PDF::loadView('emails.send_quotation_receipt', array('quotation' => $quotation, 'msg' => null));

        //  Get quotation receipt name
        if (!empty($quotation->details['heading']) && !empty($quotation['reference_no_value'])) {
            //  Get the quotation receipt name from heading, reference and date
            $pdfName = 'Receipt - '.
                        $quotation->details['reference_no_value'].' - '.
                        Carbon::parse($quotation['created_date_value'])->format('M d Y').
                        '.pdf';
        } else {
            //  Otherwise get quotation name from the quotation id
            $pdfName = 'Receipt - '.$quotation->id.'.pdf';
        }

        /***********************************************
         *   REPLACE SHORTCODES WITH ACTUAL CONTENT    *
         ***********************************************/

        $message = $this->replaceShortcodes($quotation, $message);
        $subject = $this->replaceShortcodes($quotation, $subject);

        //  Foreach email
        foreach ($primaryEmails as $primaryEmail) {
            /******************************
             *   SEND QUOTATION VIA MAIL    *
             ******************************/

            Mail::to($primaryEmail)->send(new QuotationReceiptMail($subject, $message, $quotation, $receiptPDF, $pdfName));

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $mail = ['email' => $primaryEmail, 'subject' => $subject, 'message' => $message];

            //  Record activity of quotation sent receipt
            $quotationSentActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize(), 'mail' => $mail]);
        }
    }

    /*  replaceShortcodes() method:
     *
     *  This is used to replace all shortcodes within a given message
     *  The method goes and checks for any shortcode that can be converted
     *  to actual information used in the quotation. E.g it would replace
     *  the shortcode [grand_total] with the actual grand total amount
     *  of the quotation.
     *
     */
    public function replaceShortcodes($quotation, $data)
    {
        $client = $quotation->customized_client_details;
        $company = $quotation->customized_company_details;
        $currency = $quotation->currency_type['currency']['symbol'] ?? '';
        $sub_total = $currency.number_format($quotation->sub_total_value, 2, ',', '.');
        $grand_total = $currency.number_format($quotation->grand_total_value, 2, ',', '.');

        //  Custom Quotation Variables - Shortcodes
        $customFields = [
            '[quotation_heading]' => $quotation->heading,
            '[quotation_reference_no]' => '#'.$quotation->reference_no_value,
            '[created_date]' => (new Carbon($quotation->created_date_value))->format('M d Y'),
            '[expiry_date]' => (new Carbon($quotation->expiry_date_value))->format('M d Y'),
            '[sub_total]' => $sub_total,
            '[grand_total]' => $grand_total,
            '[currency]' => $currency,
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
     *  This is used to skip sending an existing quotation (via email).
     *  It also works to store the skip send activity.
     *
     */
    public function initiateSkipSend($quotation_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***********************************************************
         *   CHECK IF USER HAS PERMISSION TO SKIP SEND QUOTATION     *
         **********************************************************/

        try {
            //  Get the quotation
            $quotation = $this->where('id', $quotation_id)->first();

            //  Check if we have an quotation
            if ($quotation) {
                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of quotation skipped sending
                $status = 'skipped sending';
                $quotationSkipSendActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $quotation];
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

    /*  initiateRecordPayment() method:
     *
     *  This is used to an existing quotation to an invoice.
     *  It also works to store the converted activity and broadcasting
     *  of notifications to users concerning the conversion of the quotation.
     *
     */
    public function initiateConvertToInvoice($quotation_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*********************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD QUOTATION CONVERSION     *
         ********************************************************************/

        try {
            //  Get the quotation
            $quotation = $this->where('id', $quotation_id)->first();

            //  Check if we have an quotation
            if ($quotation) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new QuotationConverted($quotation));

                /*******************************************************
                 *   CREATE A INVOICE USING THE QUOTATION             *
                 ******************************************************/

                //  Get the associated company
                $company = Company::where('id', $quotation->company_id)->first();

                //  If we have any company so far
                if ($company) {
                    //  Get the company settings
                    $settings = $company->settings['details'];
                    $invoiceTemplate = $settings['invoiceTemplate'];
                }

                $daysApart = (new Carbon($quotation->created_date_value) )->diffInDays((new Carbon($quotation->expiry_date_value) ));

                //  Create a template to hold the quotation details
                $template = [
                    'heading' => $invoiceTemplate['heading'],
                    'reference_no_title' => $invoiceTemplate['reference_no_title'],
                    //  'reference_no_value' => $quotation['reference_no_value'],
                    'created_date_title' => $invoiceTemplate['created_date_title'],
                    'created_date_value' => (Carbon::now())->format('Y-m-d'),
                    'expiry_date_title' => $invoiceTemplate['expiry_date_title'],
                    'expiry_date_value' => (Carbon::now())->addDays($daysApart - 1)->format('Y-m-d'),
                    'sub_total_title' => $invoiceTemplate['sub_total_title'],
                    'sub_total_value' => $quotation['sub_total_value'],
                    'grand_total_title' => $invoiceTemplate['grand_total_title'],
                    'grand_total_value' => $quotation['grand_total_value'],
                    'currency_type' => $quotation['currency_type'],
                    'calculated_taxes' => $quotation['calculated_taxes'],
                    'invoice_to_title' => $invoiceTemplate['invoice_to_title'],
                    'customized_company_details' => $quotation['customized_company_details'],
                    'customized_client_details' => $quotation['customized_client_details'],
                    'client_id' => $quotation['customized_client_details']['id'],
                    'table_columns' => $quotation['table_columns'],
                    'items' => $quotation['items'],
                    'notes' => $quotation['notes'],
                    'colors' => $quotation['colors'],
                    'footer' => $quotation['footer'],
                    'quotation_id' => $quotation->id,
                    'company_branch_id' => $quotation->company_branch_id,
                    'company_id' => $quotation->company_id,
                ];

                //  Invoice Instance
                $data = ( new Invoice() )->initiateCreate($template);
                $success = $data['success'];
                $response = $data['response'];

                //  If the invoice was created successfully
                if ($success) {
                    //  If this is a success then we have the invoice
                    $invoice = $response;
                } else {
                    //  If the data was not a success then return the response
                    return ['success' => false, 'response' => $response];
                }

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of quotation converted
                $status = 'converted';
                $quotationPaidActivity = oq_saveActivity($quotation, $auth_user, $status, ['quotation' => $quotation->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $invoice];
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
    /*  This method is used to get the overall statistics of the quotations,
     *  showing information of quotations in their respective states such as
     *  1) Name of status
     *  2) Total number of quotations in each respective status
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
                //  Get all the available quotations so far
                $quotations = $data['response'];

                //  From the list of quotations we will group them by their current activity status e.g) Converted, Sent, e.t.c
                //  After this we will map through each group (Converted, Sent, e.t.c) and get the status name, total sum of
                //  the grand totals as well as the total count of grouped quotations of that activity.
                /*
                *  Example of returned output:
                *
                    {
                        "Paid": {
                            "name": "Converted",
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

                $availableStats = collect($quotations)->groupBy('current_activity_status')->map(function ($quotationGroup, $key) {
                    return [
                        'name' => $key,  //  e.g) Converted, Expired, Sent, Approved, Draft
                        'grand_total' => collect($quotationGroup)->sum('grand_total_value'),  //  35020
                        'total_count' => collect($quotationGroup)->count(),                   //  12
                    ];
                });

                //  This is a list of all the statistics we want returned in their respective order
                $expectedStats = ['Draft', 'Approved', 'Sent', 'Expired', 'Converted'];

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
                            "name": "Expired",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Converted",
                            "grand_total": 44520,
                            "total_count": 5
                        }
                    ]
                *
                */

                $stats = collect($expectedStats)->map(function ($stat_name) use ($availableStats) {
                    if (collect($availableStats)->has($stat_name)) {
                        return $availableStats[$stat_name];
                    } else {
                        return [
                                    'name' => $stat_name,         //  e.g) Converted, Expired, Sent, Approved, Draft
                                    'grand_total' => 0,
                                    'total_count' => 0,
                                ];
                    }
                });

                //  Calculate the overall stats e.g) Total Converted & Total Outstanding
                $totalPaid = ['name' => 'Converted', 'grand_total' => 0, 'total_count' => 0];
                $totalOutstanding = ['name' => 'Unconverted', 'grand_total' => 0, 'total_count' => 0];

                foreach ($stats as $stat) {
                    if (in_array($stat['name'], ['Draft', 'Approved', 'Sent', 'Expired'])) {
                        $totalOutstanding['grand_total'] += $stat['grand_total'];
                        $totalOutstanding['total_count'] += $stat['total_count'];
                    } elseif (in_array($stat['name'], ['Converted'])) {
                        $totalPaid['grand_total'] += $stat['grand_total'];
                        $totalPaid['total_count'] += $stat['total_count'];
                    }
                }

                //  Remove the stats about Converted since we have the Total Converted
                foreach ($stats as $index => $stat) {
                    if ($stat['name'] == 'Converted') {
                        unset($stats[$index]);
                    }
                }

                //  Get the company base currency
                $baseCurrency = collect($auth_user->company->currency_type);

                //  Merge the overview stats, stats and base currency into one collection
                $data = [
                        'overview_stats' => [$totalOutstanding, $totalPaid],
                        'stats' => $stats,
                        'base_currency' => $baseCurrency, ];

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
