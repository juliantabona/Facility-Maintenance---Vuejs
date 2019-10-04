<?php

namespace App\Http\Controllers\Api;

use App\Invoice;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getInvoices()
    {
        //  Check if the user is authourized to view all invoices
        if ($this->user->can('viewAll', Invoice::class)) {
        
            //  Get the invoices
            $invoices = Invoice::paginate();

            //  Check if the invoices exist
            if ($invoices) {

                //  Return an API Readable Format of the Invoice Instance
                return ( new \App\Invoice )->convertToApiFormat($invoices);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getInvoice( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::where('id', $invoice_id)->first() ?? null;

        //  Check if the invoice exists
        if ($invoice) {

            //  Check if the user is authourized to view the invoice
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Invoice Instance
                return $invoice->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  OWNER RELATED RESOURCES      *
    *********************************/

    public function getInvoiceOwner( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice owner
        $owner = $invoice->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the invoice owner
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Model Instance
                return $owner->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  MERCHANT RELATED RESOURCES   *
    *********************************/

    public function getInvoiceMerchant( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice merchant
        $merchant = $invoice->merchant ?? null;

        //  Check if the merchant exists
        if ($merchant) {

            //  Check if the user is authourized to view the invoice merchant
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Model Instance
                return $merchant->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  CUSTOMER RELATED RESOURCES   *
    *********************************/

    public function getInvoiceCustomer( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice customer
        $customer = $invoice->customer ?? null;

        //  Check if the customer exists
        if ($customer) {

            //  Check if the user is authourized to view the invoice customer
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Model Instance
                return $customer->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  REFERENCE RELATED RESOURCES  *
    *********************************/

    public function getInvoiceReference( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice reference
        $reference = $invoice->reference ?? null;

        //  Check if the reference exists
        if ($reference) {

            //  Check if the user is authourized to view the invoice reference
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Model Instance
                return $reference->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  QUOTATION RELATED RESOURCES  *
    *********************************/

    public function getInvoiceQuotation( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice quotation
        $quotation = $invoice->quotation ?? null;

        //  Check if the quotation exists
        if ($quotation) {

            //  Check if the user is authourized to view the invoice quotation
            if ($this->user->can('view', $quotation)) {

                //  Return an API Readable Format of the Quotation Instance
                return $quotation->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*****************************************
     *  CHILD INVOICE(S) RELATED RESOURCES   *
    *****************************************/

    public function getChildInvoices( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the child invoices
        $child_invoices = $invoice->childInvoices()->paginate() ?? null;

        //  Check if the child invoices exist
        if ($child_invoices) {

            //  Check if the user is authourized to view the invoice child invoices
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Invoice )->convertToApiFormat($child_invoices);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*****************************************
     *  PARENT INVOICE RELATED RESOURCES     *
    *****************************************/

    public function getParentInvoice( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the parent invoice
        $parent_invoice = $invoice->parentInvoice ?? null;

        //  Check if the parent invoice exists
        if ($parent_invoice) {

            //  Check if the user is authourized to view the invoice parent invoice
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Document Instance
                return $parent_invoice->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DOCUMENT RELATED RESOURCES   *
    *********************************/

    public function getInvoiceProofOfPayment( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice proof of payments
        $proof_of_payment = $invoice->proofOfPayment()->paginate() ?? null;

        //  Check if the proof of payments exist
        if ($proof_of_payment) {

            //  Check if the user is authourized to view the invoice proof of payment
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document )->convertToApiFormat($proof_of_payment);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceDocuments( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice documents
        $documents = $invoice->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the invoice documents
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document )->convertToApiFormat($documents);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceDocument( $invoice_id, $document_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice document
        $document = $invoice->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the invoice document
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Document Instance
                return $document->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /************************************
     *  TRANSACTION RELATED RESOURCES   *
    ************************************/

    public function getInvoiceTransactions( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::find($invoice_id);

        //  Get the invoice transactions
        $transactions = $invoice->transactions()->paginate() ?? null;

        //  Check if the transactions exist
        if ($transactions) {

            //  Check if the user is authourized to view the invoice transactions
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Transaction Instance
                return ( new \App\Transaction )->convertToApiFormat($transactions);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceTransaction( $invoice_id, $transaction_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice transaction
        $transaction = $invoice->transactions()->where('transactions.id', $transaction_id)->first() ?? null;

        //  Check if the transaction exists
        if ($transaction) {

            //  Check if the user is authourized to view the invoice transaction
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Transaction Instance
                return $transaction->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  PAYMENT RELATED RESOURCES    *
    *********************************/

    public function getInvoicePayments( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::find($invoice_id);

        //  Get the invoice payments
        $payments = $invoice->payments()->paginate() ?? null;

        //  Check if the payments exist
        if ($payments) {

            //  Check if the user is authourized to view the invoice payments
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Transaction Instance
                return ( new \App\Transaction )->convertToApiFormat($payments);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoicePayment( $invoice_id, $payment_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice payment
        $payment = $invoice->payments()->where('payments.id', $payment_id)->first() ?? null;

        //  Check if the payment exists
        if ($payment) {

            //  Check if the user is authourized to view the invoice payment
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Payment Instance
                return $payment->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  REFUND RELATED RESOURCES     *
    *********************************/

    public function getInvoiceRefunds( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::find($invoice_id);

        //  Get the invoice refunds
        $refunds = $invoice->refunds()->paginate() ?? null;

        //  Check if the refunds exist
        if ($refunds) {

            //  Check if the user is authourized to view the invoice refunds
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Transaction Instance
                return ( new \App\Transaction )->convertToApiFormat($refunds);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceRefund( $invoice_id, $refund_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice refund
        $refund = $invoice->refunds()->where('refunds.id', $refund_id)->first() ?? null;

        //  Check if the refund exists
        if ($refund) {

            //  Check if the user is authourized to view the invoice refund
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Refund Instance
                return $refund->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  TAX RELATED RESOURCES        *
    *********************************/

    public function getInvoiceTaxes( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::find($invoice_id);

        //  Get the invoice taxes
        $taxes = $invoice->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the invoice taxes
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Tax Instance
                return ( new \App\Tax )->convertToApiFormat($taxes);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceTax( $invoice_id, $tax_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice tax
        $tax = $invoice->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the invoice tax
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Tax Instance
                return $tax->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DISCOUNT RELATED RESOURCES   *
    *********************************/

    public function getInvoiceDiscounts( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the store discounts
        $discounts = $invoice->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the invoice discounts
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Discount Instance
                return ( new \App\Discount )->convertToApiFormat($discounts);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceDiscount( $invoice_id, $discount_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice discount
        $discount = $invoice->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the invoice discount
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Discount Instance
                return $discount->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  COUPONS RELATED RESOURCES    *
    *********************************/

    public function getInvoiceCoupons( $invoice_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice coupons
        $coupons = $invoice->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the invoice coupons
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Coupon Instance
                return ( new \App\Coupon )->convertToApiFormat($coupons);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getInvoiceCoupon( $invoice_id, $coupon_id )
    {
        //  Get the invoice
        $invoice = Invoice::findOrFail($invoice_id);

        //  Get the invoice coupons
        $coupons = $invoice->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the invoice coupons
            if ($this->user->can('view', $invoice)) {

                //  Return an API Readable Format of the Coupon Instance
                return $coupons->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

























    /*
    public function index()
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoices were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of invoices
            $invoices = $response;

            //  Action was executed successfully
            return oq_api_notify($invoices, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateShow($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was found successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store()
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was created successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateUpdate($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was updated successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function approve($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateApprove($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was approved successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function send($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateSendInvoice($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was sent successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function skipSend($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateSkipSend($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice sending was skipped successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function sendReceipt($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateSendInvoiceReceipt($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice receipt was sent successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    //  updateRecurringSettingsSchedulePlan()
    //  Updates the schedule plan (date, time and frequency) of how the invoices
    //  will be sent over a time period
     
    public function updateRecurringSettingsSchedulePlan($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateUpdateRecurringSettingsSchedulePlan($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice schedule  plan was updated successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function updateRecurringSettingsDeliveryPlan($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateUpdateRecurringSettingsDeliveryPlan($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice schedule sending plan was updated successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function updateRecurringSettingsPaymentPlan($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateUpdateRecurringSettingsPaymentPlan($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice schedule payment plan was updated successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function approveRecurringSettings($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateApproveRecurringSettings($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was approved successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function recordPayment($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateRecordPayment($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice was paid successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function cancelPayment($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateCancelPayment($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice payment was cancelled successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function updateReminders($invoice_id)
    {
        //  Invoice Instance
        $data = ( new Invoice() )->initiateUpdateReminders($invoice_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice reminders were updated successfully
        if ($success) {
            //  If this is a success then we have the invoice
            $invoice = $response;

            //  Action was executed successfully
            return oq_api_notify($invoice, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getEstimatedStats()
    {
        //  Start creating the invoice
        $data = ( new Invoice() )->getStatistics();
        $success = $data['success'];
        $response = $data['response'];

        //  If the invoice statistics were found successfully
        if ($success) {
            //  If this is a success then we have the statistics returned
            $stats = $response;

            //  Action was executed successfully
            return oq_api_notify($stats, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }
    */
}
