<?php

namespace App\Traits;

//  Mails
use Mail;
use App\Mail\InvoiceMail;
use App\Mail\InvoiceReceiptMail;
//  Notifications
use App\Notifications\InvoiceCreated;
use App\Notifications\InvoiceUpdated;
use App\Notifications\InvoiceApproved;
use App\Notifications\InvoiceSent;
use App\Notifications\InvoiceReceiptSent;
use App\Notifications\InvoicePaid;
use App\Notifications\InvoicePaymentCancelled;
//  Other
use PDF;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

trait InvoiceTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of invoice results.
     *
     */
    public function initiateGetAll()
    {
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
        $allocation = request('allocation');

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL INVOICES         *
            /**********************************************************/

            //  Get the current invoice instance
            $invoices = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH INVOICES    *
            /*************************************************************/

            // Only get invoices associated to the company branch
            $invoices = $auth_user->companyBranch->invoices();
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY INVOICES    *
            /**************************************************************/

            //  Only get invoices associated to the company
            $invoices = $auth_user->company->invoices();
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

        $order_join = 'invoices';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $invoices = $invoices->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $invoices = $invoices->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get all except trashed
            } else {
                //  Run query
                $invoices = $invoices->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            }

            //  Filter by status if specified
            if (request('status')) {
                //  Run query
                $stat_name = ucwords(request('status'));

                $invoices = $invoices->get();

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $invoices->load(oq_url_to_array(request('connections')));
                }

                //  If we have a special case to display outstanding invoices, we need to
                //  Select invoices with statuses of 'Draft', 'Approved', 'Sent', 'Expired'
                if ($stat_name == 'Outstanding') {
                    //  List of statuses considered as outstanding
                    $outstanding = ['Draft', 'Approved', 'Sent', 'Expired'];
                    $invoices = collect($invoices)->whereIn('current_activity_status', $outstanding);
                } else {
                    $invoices = collect($invoices)->where('current_activity_status', $stat_name);
                }

                $page = request('page', 1);         //  The page number from the pagination list
                $perPage = request('limit', 10);    //  Pagination limit
                $invoices = new LengthAwarePaginator(
                                    collect($invoices->forPage($page, $perPage))->values(),
                                    $invoices->count(),
                                    $perPage,
                                    $page,
                                    ['path' => url('api/invoices')]
                                );
            } else {
                $invoices = $invoices->advancedFilter(['order_join' => $order_join, 'paginate' => true]);

                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $invoices->load(oq_url_to_array(request('connections')));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $invoices];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateShow() method:
     *
     *  This is used to return only one specific invoice.
     *
     */
    public function initiateShow($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed invoice
            if (request('withtrashed') == 1) {
                //  Run query
                $invoice = $this->withTrashed()->where('id', $invoice_id)->first();

            //  Get the non-trashed invoice
            } else {
                //  Run query
                $invoice = $this->where('id', $invoice_id)->first();
            }

            //  If we have any invoice so far
            if (count($invoice)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $invoice->load(oq_url_to_array(request('connections')));
                }

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

    /*  initiateCreate() method:
     *
     *  This is used to create a new invoice. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the invoice.
     *
     */
    public function initiateCreate()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $invoice is a collection of the invoice to be stored.
         */
        $invoice = request('invoice');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE INVOICE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        //  Create a template to hold the invoice details
        $template = [
            'heading' => $invoice['heading'],
            'reference_no_title' => $invoice['reference_no_title'],
            'reference_no_value' => $invoice['reference_no_value'],
            'created_date_title' => $invoice['created_date_title'],
            'created_date_value' => $invoice['created_date_value'],
            'expiry_date_title' => $invoice['expiry_date_title'],
            'expiry_date_value' => $invoice['expiry_date_value'],
            'sub_total_title' => $invoice['sub_total_title'],
            'sub_total_value' => $invoice['sub_total_value'],
            'grand_total_title' => $invoice['grand_total_title'],
            'grand_total_value' => $invoice['grand_total_value'],
            'currency_type' => $invoice['currency_type'],
            'calculated_taxes' => $invoice['calculated_taxes'],
            'invoice_to_title' => $invoice['invoice_to_title'],
            'customized_company_details' => $invoice['customized_company_details'],
            'customized_client_details' => $invoice['customized_client_details'],
            'client_id' => $invoice['customized_client_details']['id'],
            'table_columns' => $invoice['table_columns'],
            'items' => $invoice['items'],
            'notes' => $invoice['notes'],
            'colors' => $invoice['colors'],
            'footer' => $invoice['footer'],
            'company_branch_id' => $auth_user->company_branch_id,
            'company_id' => $auth_user->company_id,
        ];

        try {
            //  Create the invoice
            $invoice = $this->create($template);

            //  If the invoice was created successfully
            if ($invoice) {
                //  Generate a reference number
                $invoiceNumber = str_pad($invoice->id, 3, '0', STR_PAD_LEFT);

                //  Update the reference number
                $invoice->update(['reference_no_value' => $invoiceNumber]);

                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $invoice->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceCreated($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice created
                $status = 'created';
                $invoiceCreatedActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing invoice. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the invoice.
     *
     */
    public function initiateUpdate($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $invoice is a collection of the invoice to be stored.
         */
        $invoice = request('invoice');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE INVOICE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        //  Create a template to hold the invoice details
        $template = [
            'heading' => $invoice['heading'],
            'reference_no_title' => $invoice['reference_no_title'],
            'reference_no_value' => $invoice['reference_no_value'],
            'created_date_title' => $invoice['created_date_title'],
            'created_date_value' => $invoice['created_date_value'],
            'expiry_date_title' => $invoice['expiry_date_title'],
            'expiry_date_value' => $invoice['expiry_date_value'],
            'sub_total_title' => $invoice['sub_total_title'],
            'sub_total_value' => $invoice['sub_total_value'],
            'grand_total_title' => $invoice['grand_total_title'],
            'grand_total_value' => $invoice['grand_total_value'],
            'currency_type' => $invoice['currency_type'],
            'calculated_taxes' => $invoice['calculated_taxes'],
            'invoice_to_title' => $invoice['invoice_to_title'],
            'customized_company_details' => $invoice['customized_company_details'],
            'customized_client_details' => $invoice['customized_client_details'],
            'client_id' => $invoice['customized_client_details']['id'],
            'table_columns' => $invoice['table_columns'],
            'items' => $invoice['items'],
            'notes' => $invoice['notes'],
            'colors' => $invoice['colors'],
            'footer' => $invoice['footer'],
            'isRecurring' => $invoice['isRecurring'],
            'recurringSettings' => $invoice['recurringSettings'],
            'trackable_type' => 'invoice',
            'trackable_id' => $invoice_id,
            'company_branch_id' => $auth_user->company_branch_id,
            'company_id' => $auth_user->company_id,
        ];

        try {
            //  Update the invoice
            $invoice = $this->where('id', $invoice_id)->first()->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceUpdated($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice updated
                $status = 'updated';
                $invoiceUpdatedActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateApprove() method:
     *
     *  This is used to approve an existing invoice. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the approval of
     *  the invoice.
     *
     */
    public function initiateApprove($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO APPROVE INVOICE   *
         ******************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceApproved($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice approved
                $status = 'approved';
                $invoiceApprovedActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateSend() method:
     *
     *  This is used to send an existing invoice (via email).
     *  It also works to store the sent activity and broadcasting
     *  of notifications to users concerning the sending of
     *  the invoice.
     *
     */
    public function initiateSend($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND INVOICE      *
         ******************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
                /***************************************************************
                 *   SEND INVOICE VIA EMAIL - RECORD NOTIFICATIONS & ACTIVITY  *
                 ***************************************************************/

                //  Email details

                $email = request('email');
                $subject = request('subject');
                $message = request('message');

                //  Send invoice via mail
                $this->sendInvoiceAsMail($email, $subject, $message, $invoice, 'sent');

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

    public function sendInvoiceAsMail($email, $subject, $message, $invoice, $status = 'sent')
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        if ($auth_user) {
            $auth_user->notify(new InvoiceSent($invoice));
        }

        //  Invoice PDF
        $invoicePDF = PDF::loadView('pdf.invoice', array('invoice' => $invoice));

        //  Get invoice name
        if (!empty($invoice->details['heading']) && !empty($invoice['reference_no_value'])) {
            //  Get the invoice name from heading, reference and date
            $pdfName = $invoice->details['heading'].' - '.
                       $invoice->details['reference_no_value'].' - '.
                       Carbon::parse($invoice['created_date_value'])->format('M d Y').
                       '.pdf';
        } else {
            //  Otherwise get invoice name from the invoice id
            $pdfName = 'Invoice - '.$invoice->id.'.pdf';
        }

        /******************************
         *   SEND INVOICE VIA MAIL    *
         *****************************/
        $client = $invoice->customized_client_details;
        $my_company = $invoice->customized_company_details;
        $currency = $invoice->currency_type['currency']['symbol'] ?? '';
        $sub_total = $currency.number_format($invoice->sub_total_value, 2, ',', '.');
        $grand_total = $currency.number_format($invoice->grand_total_value, 2, ',', '.');

        //  Custom Variables
        $customFields = [
            '[invoice_heading]' => $invoice->heading,
            '[invoice_reference_no]' => '#'.$invoice->reference_no_value,
            '[created_date]' => (new Carbon($invoice->created_date_value))->format('M d Y'),
            '[expiry_date]' => (new Carbon($invoice->expiry_date_value))->format('M d Y'),
            '[sub_total]' => $invoice->sub_total,
            '[grand_total]' => $grand_total,
            '[currency]' => $currency,
            '[client_company_name]' => $client['name'] ?? '',
            '[client_first_name]' => $client['first_name'] ?? '',
            '[client_last_name]' => $client['last_name'] ?? '',
            '[client_full_name]' => $client['full_name'] ?? '',
            '[client_email]' => $client['email'],
            '[my_company_name]' => $my_company['name'],
            '[my_company_email]' => $my_company['email'],
        ];

        $search = array_keys($customFields);
        $replace = array_values($customFields);

        $message = str_replace($search, $replace, $message);
        $subject = str_replace($search, $replace, $subject);

        //  Send email
        Mail::to($email)->send(new InvoiceMail($subject, $message, $invoicePDF, $pdfName));

        /*****************************
         *   RECORD ACTIVITY         *
         *****************************/

        //  Structure mail template
        $mail = ['email' => $email, 'subject' => $subject, 'message' => $message];

        //  Record activity of invoice sent receipt
        $invoiceSentActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize(), 'mail' => $mail]);
    }

    /*  initiateSkipSend() method:
     *
     *  This is used to skip sending an existing invoice (via email).
     *  It also works to store the skip send activity.
     *
     */
    public function initiateSkipSend($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***********************************************************
         *   CHECK IF USER HAS PERMISSION TO SKIP SEND INVOICE     *
         **********************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice skipped sending
                $status = 'skip send';
                $invoiceSkipSendActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateSendReceipt() method:
     *
     *  This is used to send an existing invoice receipt (via email).
     *  It also works to store the sent receipt activity and broadcasting
     *  of notifications to users concerning the sending of the invoice.
     *
     */
    public function initiateSendReceipt($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***********************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND INVOICE RECEIPT  *
         **********************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceReceiptSent($invoice));

                /*****************************
                 *   SEND INVOICE VIA EMAIL  *
                 *****************************/

                //  Email details
                $email = request('email');
                $subject = request('subject');
                $message = request('message');

                //  Invoice PDF
                $receiptPDF = PDF::loadView('emails.send_invoice_receipt', array('invoice' => $invoice, 'msg' => null));

                //  Get invoice receipt name
                if (!empty($invoice->details['heading']) && !empty($invoice['reference_no_value'])) {
                    //  Get the invoice receipt name from heading, reference and date
                    $pdfName = 'Receipt - '.
                               $invoice->details['reference_no_value'].' - '.
                               Carbon::parse($invoice['created_date_value'])->format('M d Y').
                               '.pdf';
                } else {
                    //  Otherwise get invoice name from the invoice id
                    $pdfName = 'Receipt - '.$invoice->id.'.pdf';
                }

                //  Send email
                Mail::to($email)->send(new InvoiceReceiptMail($subject, $message, $invoice, $receiptPDF, $pdfName));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Structure mail template
                $mail = ['email' => $email, 'subject' => $subject, 'message' => $message];

                //  Record activity of invoice sent receipt
                $status = 'sent receipt';
                $invoiceSentReceiptActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize(), 'mail' => $mail]);

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

    public function initiateUpdateRecurringSettingsSchedulePlan($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $invoice is a collection of the invoice to be stored.
         */
        $invoice = request('invoice');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         *************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        //  Create a template to hold the invoice details
        $invoice['recurringSettings']['editing']['schedulePlan'] = 'false';

        $template = [
            'isRecurring' => 1,
            'recurringSettings' => $invoice['recurringSettings'],
        ];

        try {
            //  Update the invoice
            $invoice = $this->where('id', $invoice_id)->first()->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceUpdated($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring schedule plan updated
                $status = 'updated recurring schedule';
                $invoiceUpdatedActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    public function initiateUpdateRecurringSettingsDeliveryPlan($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $invoice is a collection of the invoice to be stored.
         */
        $invoice = request('invoice');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         *************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        //  Create a template to hold the invoice details
        $invoice['recurringSettings']['editing']['deliveryPlan'] = false;

        $template = [
            'isRecurring' => 1,
            'recurringSettings' => $invoice['recurringSettings'],
        ];

        try {
            //  Update the invoice
            $invoice = $this->where('id', $invoice_id)->first()->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceUpdated($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring delivery plan updated
                $status = 'updated recurring delivery';
                $invoiceUpdatedActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    public function initiateUpdateRecurringSettingsPaymentPlan($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $invoice is a collection of the invoice to be stored.
         */
        $invoice = request('invoice');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         *************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        //  Create a template to hold the invoice details
        $invoice['recurringSettings']['editing']['paymentPlan'] = false;

        $template = [
            'isRecurring' => 1,
            'recurringSettings' => $invoice['recurringSettings'],
        ];

        try {
            //  Update the invoice
            $invoice = $this->where('id', $invoice_id)->first()->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoiceUpdated($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring schedule timing updated
                $status = 'updated recurring payment';
                $invoiceUpdatedActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateRecordPayment() method:
     *
     *  This is used to record payment of an existing invoice.
     *  It also works to store the paid activity and broadcasting
     *  of notifications to users concerning the payment of the invoice.
     *
     */
    public function initiateRecordPayment($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /****************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD INVOICE PAYMENT     *
         ****************************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoicePaid($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice paid
                $status = 'paid';
                $invoicePaidActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateCancelPayment() method:
     *
     *  This is used to cancel a recorded payment of an existing invoice.
     *  It also works to store the cancelled payment activity and broadcasting
     *  of notifications to users concerning the payment of the invoice.
     *
     */
    public function initiateCancelPayment($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /****************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD INVOICE PAYMENT     *
         ****************************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $auth_user->notify(new InvoicePaymentCancelled($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice payment cancelled
                $status = 'payment cancelled';
                $invoicePaymentCancelledActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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

    /*  initiateCancelPayment() method:
     *
     *  This is used to cancel a recorded payment of an existing invoice.
     *  It also works to store the cancelled payment activity and broadcasting
     *  of notifications to users concerning the payment of the invoice.
     *
     */
    public function initiateUpdateReminders($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /****************************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD INVOICE PAYMENT     *
         ****************************************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if ($invoice) {
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
                        'type' => 'payment',
                        'can_sms' => $can_sms,
                        'can_email' => $can_email,
                        'email' => $invoice->customized_company_details['email'],
                        'phone' => $invoice->customized_company_details['phone'],
                        'company_branch_id' => $auth_user->company_branch_id,
                        'company_id' => $auth_user->company_id,
                        'trackable_id' => $invoice->id,
                        'trackable_type' => 'invoice',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }

                //  Delete old reminders
                $deleted = $invoice->reminders()->delete();

                //  Insert new reminders
                $invoice = $invoice->reminders()->insert($reminders);

                //  Re-fresh invoice to get the latest sent status from our recent activties
                $invoice = $this->where('id', $invoice_id)->with('reminders')->first();

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice updated payment reminders
                $status = 'payment reminder';
                $invoicePaymentCancelledActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize()]);

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
    /*  This method is used to get the overall statistics of the invoices,
     *  showing information of invoices in their respective states such as
     *  1) Name of status
     *  2) Total number of invoices in each respective status
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
        $allocation = request('allocation');

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL INVOICES         *
            /**********************************************************/

            //  Get the current invoice instance
            $invoices = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH INVOICES    *
            /*************************************************************/

            // Only get invoices associated to the company branch
            $invoices = $auth_user->companyBranch->invoices();
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY INVOICES    *
            /**************************************************************/

            //  Only get invoices associated to the company
            $invoices = $auth_user->company->invoices();
        }

        try {
            //  Get all the available invoices so far
            $invoices = $invoices->get();

            //  From the list of invoices we will group them by their current activity status e.g) Paid, Sent, e.t.c
            //  After this we will map through each group (Paid, Sent, e.t.c) and get the status name, total sum of
            //  the grand totals as well as the total count of grouped invoices of that activity.
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

            $availableStats = collect($invoices)->groupBy('current_activity_status')->map(function ($invoiceGroup, $key) {
                return [
                    'name' => $key,  //  e.g) Paid, Expired, Cancelled, Sent, Approved, Draft
                    'grand_total' => collect($invoiceGroup)->sum('grand_total_value'),  //  35020
                    'total_count' => collect($invoiceGroup)->count(),                   //  12
                ];
            });

            //  This is a list of all the statistics we want returned in their respective order
            $expectedStats = ['Draft', 'Approved', 'Sent', 'Cancelled', 'Expired', 'Paid'];

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
                if (collect($availableStats)->has($stat_name)) {
                    return $availableStats[$stat_name];
                } else {
                    return [
                                'name' => $stat_name,         //  e.g) Paid, Expired, Cancelled, Sent, Approved, Draft
                                'grand_total' => 0,
                                'total_count' => 0,
                            ];
                }
            });

            //  Calculate the overall stats e.g) Total Paid & Total Outstanding
            $totalPaid = ['name' => 'Paid', 'grand_total' => 0, 'total_count' => 0];
            $totalOutstanding = ['name' => 'Outstanding', 'grand_total' => 0, 'total_count' => 0];

            foreach ($stats as $stat) {
                if (in_array($stat['name'], ['Draft', 'Approved', 'Sent', 'Expired'])) {
                    $totalOutstanding['grand_total'] += $stat['grand_total'];
                    $totalOutstanding['total_count'] += $stat['total_count'];
                } elseif (in_array($stat['name'], ['Paid'])) {
                    $totalPaid['grand_total'] += $stat['grand_total'];
                    $totalPaid['total_count'] += $stat['total_count'];
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
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of an invoice to very specific
     *  columns that can then be used for storage e.g) in the instance of
     *  adding a recent activity. We may only want to summarize the invoice
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
                'heading', 'reference_no_title', 'reference_no_value', 'created_date_title', 'created_date_value',
                'expiry_date_title', 'expiry_date_value', 'sub_total_title', 'sub_total_value', 'grand_total_title', 'grand_total_value',
                'currency_type', 'calculated_taxes', 'invoice_to_title', 'customized_company_details', 'customized_client_details', 'client_id',
                'table_columns', 'items', 'notes', 'colors', 'footer', 'quotation_id'
            )->first())
            //  Remove all custom attributes since the are all based on recent activities
            ->forget(['last_approved_activity', 'last_sent_activity', 'last_paid_activity', 'last_payment_cancelled_activity',
                    'has_paid', 'has_expired', 'has_cancelled', 'has_sent', 'has_approved', 'current_activity_status', 'recent_activities',
            ]);
    }
}
