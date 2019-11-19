<?php

namespace App\Http\Controllers\Api;

use App\Phone;
use App\Company;
use App\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    public function sendSampleSms()
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        //  Get the type e.g) invoice, quotation, receipt, e.t.c otherwise default to invoice
        $type = request('type');

        //  Get the provided phones otherwise use the auth user phones
        $phones = request('phones') ?? $auth_user->phones;

        /********************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND SAMPLE SMS    *
         *******************************************************/

        $company = $auth_user->company;
        $companySettings = $company->settings->details;
        $sampleCustomer = Company::find(1);
        
        if(!$phones){
            //  Type not specified
            return oq_api_notify_error('Provide the phones to send the sample sms', null, 404);
         }

        if(!$company){
           //  Type not specified
           return oq_api_notify_error('The auth user does not have a company assigned', null, 404);
        }
        
        if (!$type) {
            //  Type not specified
            return oq_api_notify_error('Include the type of sample sms to send e.g) quotation, invoice, receipt e.t.c', null, 404);
        
        }elseif($type == 'invoice'){
            //  Invoice Sample Template
            $invoice = json_decode(json_encode([
                'heading' => $companySettings['invoiceTemplate']['heading'],
                'reference_no_title' => $companySettings['invoiceTemplate']['reference_no_title'],
                'reference_no_value' => '001',
                'created_date_title' => $companySettings['invoiceTemplate']['created_date_title'],
                'created_date' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->format('Y-m-d'),
                'expiry_date_title' => $companySettings['invoiceTemplate']['expiry_date_title'],
                'expiry_date' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->addWeek()->format('Y-m-d'),
                'sub_total_title' => $companySettings['invoiceTemplate']['sub_total_title'],
                'sub_total_value' => '1250.00',
                'grand_total_title' => $companySettings['invoiceTemplate']['grand_total_title'],
                'grand_total' => '1250.00',
                'currency_type' => $companySettings['invoiceTemplate']['currency_type'],
                'calculated_taxes' => [],
                'invoice_to_title' => $companySettings['invoiceTemplate']['invoice_to_title'],
                'customized_company_details' => $company,
                'customized_customer_details' => $sampleCustomer,
                'customer_id' => $sampleCustomer->id,
                'table_columns' => $companySettings['invoiceTemplate']['table_columns'],
                'items' => [['name'=>$company->industry,'description'=>'','quantity'=>1,'unit_regular_price'=>1250,'total_price'=>1250,'taxes'=>[]]],
                'notes' => $companySettings['invoiceTemplate']['notes'],
                'colors' => $companySettings['invoiceTemplate']['colors'],
                'footer' => $companySettings['invoiceTemplate']['footer'],
                'is_recurring' => 0,
                'recurring_settings' => [],
                'company_branch_id' => $auth_user->company_branch_id,
                'company_id' => $auth_user->company_id,
            ]));

            //  Create Invoice Instance and send sample sms
            $data = ( new Invoice() )->sendInvoiceAsSMS($invoice, $phones, $smsMessage = null, $user = null, $sampleSms = true);
        }

        $success = $data['success'];
        $response = $data['response'];

        //  If the sms was sent successfully
        if ($success) {
            //  If this is a success then we have the list of phones and sms message that was sent to them
            $smsData = $response;

            //  Action was executed successfully
            return oq_api_notify($smsData, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

}
