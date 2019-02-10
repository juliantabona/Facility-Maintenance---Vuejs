<?php

namespace App\Http\Controllers\Api;

use PDF;
use Mail;
use App\Invoice;
use App\Mail\InvoiceMail;
use App\Mail\InvoiceReceiptMail;
use App\Notifications\InvoiceCreated;
use App\Notifications\InvoiceUpdated;
use App\Notifications\InvoiceApproved;
use App\Notifications\InvoiceSent;
use App\Notifications\InvoicePaid;
use App\Notifications\InvoicePaymentCancelled;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no invoices
        $invoices = [];

        //  Query data
        $type = request('model', 'branch');      //  e.g) company, branch
        $model_id = request('modelId');          //  The id of the client/supplier for getting related invoices

        /*  First thing is first, we need to understand one of 9 scenerios, Either we want:
         *
         *  1) Only invoices for a related COMPANY of the authenticated user (NO STEPS)
         *  2) Only invoices for a related BRANCH of the authenticated user (NO STEPS)
         *  3) Only invoices for a related CLIENT of the authenticated user (NO STEPS)
         *  4) Only invoices for a related CONTRACTOR of the authenticated user (NO STEPS)
         *  5) Only invoices in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given COMPANY of the authenticated user
         *  6) Only invoices in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given BRANCH of the authenticated user
         *  7) Only invoices in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given CLIENT of the authenticated user
         *  8) Only invoices in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given CONTRACTOR of the authenticated user
         *  9) All invoices in the system e.g) If SuperAdmin needs access to all data
         *
         *  Once we have those invoices we will determine whether we want any of the following
         *
         *  1) All invoices aswell as the trashed ones
         *  2) Only invoices that are trashed
         *  3) Only invoices that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific invoices
         *  If the user indicated that they want invoices related to their company,
         *  then get the invoices related to the authenticated users company.
         *  They must indicate using the query "model" set to "company".
         */
        if ($type == 'company') {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW COMPANY INVOICES    *
            /**************************************************************/

            $invoices = $user->companyBranch->company->invoices();

        /*  User Branch specific invoices
         *  If the user indicated that they want invoices related to their branch,
         *  then get the invoices related to the authenticated users branch.
         *  They must indicate using the query "model" set to "branch".
         */
        } elseif ($type == 'branch') {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW BRANCH INVOICES    *
            /**************************************************************/

            $invoices = $user->companyBranch->invoices();

        /*  Client specific invoices
         *  If the user indicated that they want invoices related to a specific client,
         *  then get the invoices related to that client. They must indicate using the
         *  query "model" set to "client" and "model_id" to the company unique id.
         */
        } elseif ($type == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL INVOICES    *
            /**********************************************************/

            /*  ALL INVOICES
            *  If the user wants all the invoices in the system, they must indicate
            *  using the query "all" set to "1". This is normaly used by authorized
            *  superadmins to access all invoice resources in the system.
            */

            /*   Create a new invoice instance that can be used to retrieve all invoices
             */
            $invoices = new Invoice();
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
                $invoices = $invoices->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $invoices = $invoices->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query
                $invoices = $invoices->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any invoices so far
            if (count($invoices)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $invoices->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($invoices, 200);
    }

    public function show($invoice_id)
    {
        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $invoice = Invoice::withTrashed()->where('id', $invoice_id)->first();
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $invoice = Invoice::onlyTrashed()->where('id', $invoice_id)->first();
            //  Get all except trashed
            } else {
                //  Run query
                $invoice = Invoice::where('id', $invoice_id)->first();
            }

            //  If we have any invoice so far
            if (count($invoice)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $invoice->load(oq_url_to_array(request('connections')));
                }

                return $invoice;
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function update(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $model_Type = request('model');                      //  Associated model e.g) invoice
        $modelId = request('modelId');                      //  The id of the associated model
        $invoice = request('invoice');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE INVOICE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        if (!empty($invoice)) {
            try {
                $template = [
                    'status' => $invoice['status'],
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
                    'currency_type' => json_encode($invoice['currency_type'], JSON_FORCE_OBJECT),
                    'calculated_taxes' => json_encode($invoice['calculated_taxes'], JSON_FORCE_OBJECT),
                    'invoice_to_title' => $invoice['invoice_to_title'],
                    'customized_company_details' => json_encode($invoice['customized_company_details'], JSON_FORCE_OBJECT),
                    'customized_client_details' => json_encode($invoice['customized_client_details'], JSON_FORCE_OBJECT),
                    'client_id' => $invoice['customized_client_details']['id'],
                    'table_columns' => json_encode($invoice['table_columns'], JSON_FORCE_OBJECT),
                    'items' => json_encode($invoice['items'], JSON_FORCE_OBJECT),
                    'notes' => json_encode($invoice['notes'], JSON_FORCE_OBJECT),
                    'colors' => json_encode($invoice['colors'], JSON_FORCE_OBJECT),
                    'footer' => $invoice['footer'],
                    'trackable_type' => $model_Type,
                    'trackable_id' => $modelId,
                    'company_branch_id' => $user->companyBranch->id,
                    'company_id' => $user->companyBranch->company->id,
                ];

                //  Update the invoice
                $invoice = Invoice::where('id', $invoice_id)->update($template);

                //  If the invoice was created/updated successfully
                if ($invoice) {
                    //  refetch the updated invoice
                    $invoice = Invoice::find($invoice_id);

                    /*****************************
                     *   SEND NOTIFICATIONS      *
                     *****************************/

                    $user->notify(new InvoiceUpdated($invoice));

                    //  Record activity of a invoice created
                    $status = 'updated';
                    $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);

                    $invoice = $invoice->fresh();
                }

                //  If the invoice was updated successfully
                if ($invoice) {
                    //  Action was executed successfully
                    return oq_api_notify($invoice, 200);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }

    public function store(Request $request)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $model_Type = request('model');                      //  Associated model e.g) invoice
        $modelId = request('modelId');                      //  The id of the associated model
        $invoice = request('invoice');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE INVOICE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        $template = [
            'status' => $invoice['status'],
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
            'trackable_type' => $model_Type,
            'trackable_id' => $modelId,
            'company_branch_id' => $user->companyBranch->id,
            'company_id' => $user->companyBranch->company->id,
        ];

        //  Create the invoice
        $invoice = Invoice::create($template);

        //  If the invoice was created/updated successfully
        if ($invoice) {
            //  Update the reference no
            $invoiceNumber = str_pad($invoice->id, 3, '0', STR_PAD_LEFT);
            $invoice->update(['reference_no_value' => $invoiceNumber]);

            //  re-retrieve the instance to get all of the fields in the table.
            $invoice = $invoice->fresh();

            /*****************************
             *   SEND NOTIFICATIONS      *
             *****************************/

            $user->notify(new InvoiceCreated($invoice));

            //  Record activity of a invoice created
            $status = 'created';
            $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);
        }

        //  return created invoice
        return oq_api_notify($invoice, 201);
    }

    public function approve(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /********************************************************
         *   CHECK IF USER HAS PERMISSION TO APPROVE INVOICE    *
         *******************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we an invoice
            if (count($invoice)) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $user->notify(new InvoiceApproved($invoice));

                //  Set status to "approved"
                $status = 'approved';

                //  Record activity of a invoice approved
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);

                //  Re-fresh invoice to get the latest approved status from our recent activties
                $invoice = $invoice->fresh();

                //  If the invoice was approved successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function skipSend(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*****************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND INVOICE    *
         *****************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we an invoice
            if (count($invoice)) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $user->notify(new InvoiceSent($invoice));

                /*****************************
                 *   SEND INVOICE VIA EMAIL  *
                 *****************************/

                //  Set status to "sent"
                $status = 'sent';

                //  Record activity of a invoice sent
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);

                //  Re-fresh invoice to get the latest sent status from our recent activties
                $invoice = $invoice->fresh();

                //  If the invoice was sent successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function send(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*****************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND INVOICE    *
         *****************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if (count($invoice)) {
                /*****************************
                 *   SEND INVOICE VIA EMAIL  *
                 *****************************/

                $email = request('email');
                $subject = request('subject');
                $message = request('message');

                $invoicePDF = PDF::loadView('pdf.invoice', array('invoice' => $invoice));

                if (!empty($invoice->details['heading']) && !empty($invoice['reference_no_value'])) {
                    $pdfName = $invoice->details['heading'].' - '.
                               $invoice->details['reference_no_value'].' - '.
                               \Carbon\Carbon::parse($invoice['created_date_value'])->format('M d Y').
                               '.pdf';
                } else {
                    $pdfName = 'Invoice - '.$invoice->id.'.pdf';
                }

                Mail::to($user->email)->send(new InvoiceMail($subject, $message, $invoicePDF, $pdfName));

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $user->notify(new InvoiceSent($invoice));

                //  Set status to "sent"
                $status = 'sent';

                //  Structure mail template
                $mail = ['email' => $email, 'subject' => $subject, 'message' => $message];

                //  Record activity of a invoice sent and mail template sent
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize(), 'mail' => $mail]);

                //  Re-fresh invoice to get the latest sent status from our recent activties
                $invoice = $invoice->fresh();

                //  If the invoice was sent successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function sendReceipt(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*****************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND RECEIPT    *
         *****************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we have an invoice
            if (count($invoice)) {
                /*************************************
                 *   SEND INVOICE RECEIPT VIA EMAIL  *
                 *************************************/

                $email = request('email');
                $subject = request('subject');
                $message = request('message');

                $receiptPDF = PDF::loadView('emails.send_invoice_receipt', array('invoice' => $invoice));

                if (!empty($invoice->details['heading']) && !empty($invoice['reference_no_value'])) {
                    $pdfName = 'Receipt - '.
                               $invoice->details['reference_no_value'].' - '.
                               \Carbon\Carbon::parse($invoice['created_date_value'])->format('M d Y').
                               '.pdf';
                } else {
                    $pdfName = 'Receipt - '.$invoice->id.'.pdf';
                }

                Mail::to($user->email)->send(new InvoiceReceiptMail($subject, $message, $receiptPDF, $pdfName));

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $user->notify(new InvoiceSent($invoice));

                //  Set status to "sent receipt"
                $status = 'sent receipt';

                //  Structure mail template
                $mail = ['email' => $email, 'subject' => $subject, 'message' => $message];

                //  Record activity of a receipt sent and mail template sent
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize(), 'mail' => $mail]);

                //  Re-fresh invoice to get the latest sent status from our recent activties
                $invoice = $invoice->fresh();

                //  If the invoice was sent successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function recordPayment(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*****************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD PAYMENT  *
         *****************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we an invoice
            if (count($invoice)) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $user->notify(new InvoicePaid($invoice));

                //  Set status to "paid"
                $status = 'paid';

                //  Record activity of a invoice sent
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);

                //  Re-fresh invoice to get the latest sent status from our recent activties
                $invoice = $invoice->fresh();

                //  If the invoice was sent successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function cancelPayment(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*****************************************************
         *   CHECK IF USER HAS PERMISSION TO RECORD PAYMENT  *
         *****************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we an invoice
            if (count($invoice)) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                $user->notify(new InvoicePaymentCancelled($invoice));

                //  Set status to "payment cancelled"
                $status = 'payment cancelled';

                //  Record activity of a invoice sent
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);

                //  Re-fresh invoice to get the latest status from our recent activties
                $invoice = $invoice->fresh();

                //  If the invoice was sent successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function updateReminders(Request $request, $invoice_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /************************************************************
         *   CHECK IF USER HAS PERMISSION TO ADD INVOICE REMINDERS  *
         ***********************************************************/

        try {
            //  Get the invoice
            $invoice = Invoice::where('id', $invoice_id)->first();

            //  Check if we an invoice
            if (count($invoice)) {
                $reminders = [];

                foreach (request('reminders')['days'] as $key => $reminder) {
                    $can_email = 0;
                    $can_sms = 0;

                    foreach (request('reminders')['method'] as $method) {
                        if ($method == 'email') {
                            $can_email = 1;
                        }

                        if ($method == 'sms') {
                            $can_sms = 1;
                        }
                    }

                    $reminders[$key] = [
                        'days_after' => request('reminders')['days'][$key],
                        'type' => 'payment',
                        'can_sms' => $can_sms,
                        'can_email' => $can_email,
                        'email' => $invoice->customized_company_details['email'],
                        'phone' => $invoice->customized_company_details['phone'],
                        'company_branch_id' => $user->company_branch_id,
                        'company_id' => $user->company_id,
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
                $invoice = Invoice::where('id', $invoice_id)->with('reminders')->first();

                //  Set status to "reminder"
                $status = 'payment reminder';

                //  Record activity of a invoice sent
                $invoiceCreatedActivity = oq_saveActivity($invoice, $user, $status, ['invoice' => $invoice->summarize()]);

                //  If the invoice was sent successfully, return back
                return oq_api_notify($invoice, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }
}
