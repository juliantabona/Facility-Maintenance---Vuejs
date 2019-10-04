<?php

namespace App\Http\Controllers\Api;

use App\Quotation;
use App\Http\Controllers\Controller;

class QuotationController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getQuotations()
    {
        //  Check if the user is authourized to view all quotations
        if ($this->user->can('viewAll', Quotation::class)) {
        
            //  Get the quotations
            $quotations = Quotation::paginate();

            //  Check if the quotations exist
            if ($quotations) {

                //  Return an API Readable Format of the Quotation Instance
                return ( new \App\Quotation )->convertToApiFormat($quotations);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getQuotation( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::where('id', $quotation_id)->first() ?? null;

        //  Check if the quotation exists
        if ($quotation) {

            //  Check if the user is authourized to view the quotation
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

    /*********************************
     *  OWNER RELATED RESOURCES      *
    *********************************/

    public function getQuotationOwner( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation owner
        $owner = $quotation->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the quotation owner
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationMerchant( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation merchant
        $merchant = $quotation->merchant ?? null;

        //  Check if the merchant exists
        if ($merchant) {

            //  Check if the user is authourized to view the quotation merchant
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationCustomer( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation customer
        $customer = $quotation->customer ?? null;

        //  Check if the customer exists
        if ($customer) {

            //  Check if the user is authourized to view the quotation customer
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationReference( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation reference
        $reference = $quotation->reference ?? null;

        //  Check if the reference exists
        if ($reference) {

            //  Check if the user is authourized to view the quotation reference
            if ($this->user->can('view', $quotation)) {

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
     *  INVOICE RELATED RESOURCES    *
    *********************************/

    public function getQuotationInvoices( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation invoices
        $invoices = $quotation->invoices()->paginate() ?? null;

        //  Check if the invoices exist
        if ($invoices) {

            //  Check if the user is authourized to view the quotation invoices
            if ($this->user->can('view', $quotation)) {

                //  Return an API Readable Format of the Invoice Instance
                return ( new \App\Invoice )->convertToApiFormat($invoices);

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

    public function getQuotationDocuments( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation documents
        $documents = $quotation->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the quotation documents
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationDocument( $quotation_id, $document_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation document
        $document = $quotation->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the quotation document
            if ($this->user->can('view', $quotation)) {

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

    /*********************************
     *  TAX RELATED RESOURCES        *
    *********************************/

    public function getQuotationTaxes( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::find($quotation_id);

        //  Get the quotation taxes
        $taxes = $quotation->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the quotation taxes
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationTax( $quotation_id, $tax_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation tax
        $tax = $quotation->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the quotation tax
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationDiscounts( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the store discounts
        $discounts = $quotation->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the quotation discounts
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationDiscount( $quotation_id, $discount_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation discount
        $discount = $quotation->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the quotation discount
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationCoupons( $quotation_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation coupons
        $coupons = $quotation->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the quotation coupons
            if ($this->user->can('view', $quotation)) {

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

    public function getQuotationCoupon( $quotation_id, $coupon_id )
    {
        //  Get the quotation
        $quotation = Quotation::findOrFail($quotation_id);

        //  Get the quotation coupons
        $coupons = $quotation->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the quotation coupons
            if ($this->user->can('view', $quotation)) {

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

    */
}
