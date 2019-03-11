<?php

namespace App\Http\Controllers\Api;

use App\Quotation;
use App\Http\Controllers\Controller;

class QuotationController extends Controller
{
    public function index()
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotations were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of quotations
            $quotations = $response;

            //  Action was executed successfully
            return oq_api_notify($quotations, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($quotation_id)
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateShow($quotation_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation was found successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store()
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation was created successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($quotation_id)
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateUpdate($quotation_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation was updated successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function approve($quotation_id)
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateApprove($quotation_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation was approved successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function send($quotation_id)
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateSendQuotation($quotation_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation was sent successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function skipSend($quotation_id)
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateSkipSend($quotation_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation sending was skipped successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function convertToInvoice($quotation_id)
    {
        //  Quotation Instance
        $data = ( new Quotation() )->initiateConvertToInvoice($quotation_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation was paid successfully
        if ($success) {
            //  If this is a success then we have the quotation
            $quotation = $response;

            //  Action was executed successfully
            return oq_api_notify($quotation, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getEstimatedStats()
    {
        //  Start creating the quotation
        $data = ( new Quotation() )->getStatistics();
        $success = $data['success'];
        $response = $data['response'];

        //  If the quotation statistics were found successfully
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
