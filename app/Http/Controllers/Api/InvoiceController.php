<?php

namespace App\Http\Controllers\Api;

use App\Invoice;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
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

    /*  updateRecurringSettingsSchedulePlan()
     *  Updates the schedule plan (date, time and frequency) of how the invoices
     *  will be sent over a time period
     */
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
}
