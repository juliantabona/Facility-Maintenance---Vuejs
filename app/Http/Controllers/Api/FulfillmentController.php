<?php

namespace App\Http\Controllers\Api;

use App\Fulfillment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FulfillmentController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getFulfillments(Request $request)
    {
        //  Check if the user is authourized to view all fulfillments
        if ($this->user->can('viewAll', Fulfillment::class)) {
        
            if( $request->get('status') ){

                //  Get statues and separate into an array by comma separator
                $statuses = explode(',', $request->get('status'));
    
                //  Foreach status
                $statuses = collect($statuses)->map(function($status){

                    //  Trim the status text and lowercase every word
                    return strtolower( trim($status) );
                    
                })->toArray();
                
                //  Get the fulfillments filtered by status
                $fulfillments = Fulfillment::whereIn('status', $statuses)
                                ->orWhereIn('payment_status', $statuses)
                                ->orWhereIn('fulfillment_status', $statuses)->paginate();
            
            }else{
    
                //  Get the fulfillments
                $fulfillments = Fulfillment::paginate();
    
            }

            //  Check if the fulfillments exist
            if ($fulfillments) {

                //  Return an API Readable Format of the Fulfillment Instance
                return ( new \App\Fulfillment )->convertToApiFormat($fulfillments);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getFulfillment( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::where('id', $fulfillment_id)->first() ?? null;

        //  Check if the fulfillment exists
        if ($fulfillment) {

            //  Check if the user is authourized to view the fulfillment
            if ($this->user->can('view', $fulfillment)) {

                //  Return an API Readable Format of the Fulfillment Instance
                return $fulfillment->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function updateFulfillment( Request $request, $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::where('id', $fulfillment_id)->first() ?? null;

        //  Check if the fulfillment exists
        if ($fulfillment) {

            //  Check if the user is authourized to update the fulfillment
            if ($this->user->can('update', $fulfillment)) {

                //  Update the fulfillment
                $updatedFulfillment = $fulfillment->initiateUpdate($fulfillmentInfo = $request->all());

                //  Return an API Readable Format of the fulfillment Instance
                return $updatedFulfillment->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function cancelFulfillment( Request $request, $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::where('id', $fulfillment_id)->first() ?? null;

        //  Check if the fulfillment exists
        if ($fulfillment) {

            //  Check if the user is authourized to delete the fulfillment
            if ($this->user->can('delete', $fulfillment)) {

                $owner = $fulfillment->owner;

                //  Delete the fulfillment
                $deletedFulfillment = $fulfillment->delete();

                //  If the fulfillment belonged to an order
                if($owner->resource_type == 'order'){

                    //  Update the order fulfillment status
                    $owner->updateFulfilmentStatus();

                }

                return oq_api_notify(null, 204);

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

    public function getFulfillmentMerchant( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment merchant
        $merchant = $fulfillment->merchant ?? null;

        //  Check if the merchant exists
        if ($merchant) {

            //  Check if the user is authourized to view the fulfillment merchant
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentCustomer( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment customer
        $customer = $fulfillment->customer ?? null;

        //  Check if the customer exists
        if ($customer) {

            //  Check if the user is authourized to view the fulfillment customer
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentReference( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment reference
        $reference = $fulfillment->reference ?? null;

        //  Check if the reference exists
        if ($reference) {

            //  Check if the user is authourized to view the fulfillment reference
            if ($this->user->can('view', $fulfillment)) {

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
     *  DOCUMENT RELATED RESOURCES   *
    *********************************/

    public function getFulfillmentDocuments( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment documents
        $documents = $fulfillment->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the fulfillment documents
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentDocument( $fulfillment_id, $document_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment document
        $document = $fulfillment->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the fulfillment document
            if ($this->user->can('view', $fulfillment)) {

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
     *  INVOICE RELATED RESOURCES   *
    *********************************/

    public function getFulfillmentInvoices( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment invoices
        $invoices = $fulfillment->invoices()->paginate() ?? null;

        //  Check if the invoices exist
        if ($invoices) {

            //  Check if the user is authourized to view the fulfillment invoices
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentInvoice( $fulfillment_id, $invoice_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment invoice
        $invoice = $fulfillment->invoices()->where('id', $invoice_id)->first() ?? null;

        //  Check if the invoice exists
        if ($invoice) {

            //  Check if the user is authourized to view the fulfillment invoice
            if ($this->user->can('view', $fulfillment)) {

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
     *  TAX RELATED RESOURCES        *
    *********************************/

    public function getFulfillmentTaxes( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::find($fulfillment_id);

        //  Get the fulfillment taxes
        $taxes = $fulfillment->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the fulfillment taxes
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentTax( $fulfillment_id, $tax_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment tax
        $tax = $fulfillment->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the fulfillment tax
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentDiscounts( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the store discounts
        $discounts = $fulfillment->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the fulfillment discounts
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentDiscount( $fulfillment_id, $discount_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment discount
        $discount = $fulfillment->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the fulfillment discount
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentCoupons( $fulfillment_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment coupons
        $coupons = $fulfillment->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the fulfillment coupons
            if ($this->user->can('view', $fulfillment)) {

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

    public function getFulfillmentCoupon( $fulfillment_id, $coupon_id )
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment coupons
        $coupons = $fulfillment->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the fulfillment coupons
            if ($this->user->can('view', $fulfillment)) {

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

    /*********************************
     *  MESSAGE RELATED RESOURCES    *
    *********************************/

    public function getFulfillmentMessages($fulfillment_id)
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment messages
        $messages = $fulfillment->messages()->paginate() ?? null;

        //  Check if the messages exist
        if ($messages) {

            //  Check if the user is authourized to view the fulfillment messages
            if ($this->user->can('view', $fulfillment)) {

                //  Return an API Readable Format of the Message Instance
                return ( new \App\Message() )->convertToApiFormat($messages);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getFulfillmentMessage($fulfillment_id, $message_id)
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment message
        $message = $fulfillment->messages()->where('messages.id', $message_id)->first() ?? null;

        //  Check if the message exists
        if ($message) {

            //  Check if the user is authourized to view the fulfillment message
            if ($this->user->can('view', $fulfillment)) {

                //  Return an API Readable Format of the Message Instance
                return $message->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();
            
        }
    }

    /*********************************
     *  REVIEW RELATED RESOURCES     *
    *********************************/

    public function getFulfillmentReviews($fulfillment_id)
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment reviews
        $reviews = $fulfillment->reviews()->paginate() ?? null;

        //  Check if the reviews exist
        if ($reviews) {

            //  Check if the user is authourized to view the fulfillment reviews
            if ($this->user->can('view', $fulfillment)) {

                //  Return an API Readable Format of the Review Instance
                return ( new \App\Review() )->convertToApiFormat($reviews);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getFulfillmentReview($fulfillment_id, $review_id)
    {
        //  Get the fulfillment
        $fulfillment = Fulfillment::findOrFail($fulfillment_id);

        //  Get the fulfillment review
        $review = $fulfillment->reviews()->where('reviews.id', $review_id)->first() ?? null;

        //  Check if the review exists
        if ($review) {
            
            //  Check if the user is authourized to view the fulfillment review
            if ($this->user->can('view', $fulfillment)) {

                //  Return an API Readable Format of the Reviews Instance
                return $review->convertToApiFormat();
                
            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }














/*
    public function index()
    {
        //  Fulfillment Instance
        $data = ( new Fulfillment() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the fulfillments were found successfully
        if ($success) {
            //  If this is a success then we have the fulfillments
            $fulfillments = $response;

            //  Action was executed successfully
            return oq_api_notify($fulfillments, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($fulfillment_id)
    {
        //  Fulfillment Instance
        $data = ( new Fulfillment() )->initiateShow($fulfillment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the fulfillment was found successfully
        if ($success) {
            //  If this is a success then we have the fulfillment
            $fulfillment = $response;

            //  Action was executed successfully
            return oq_api_notify($fulfillment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the fulfillment
        $data = ( new Fulfillment() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the fulfillment was created successfully
        if ($success) {
            //  If this is a success then we have a fulfillment returned
            $fulfillment = $response;

            //  Action was executed successfully
            return oq_api_notify($fulfillment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($fulfillment_id)
    {
        //  Fulfillment Instance
        $data = ( new Fulfillment() )->initiateUpdate($fulfillment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the fulfillment was updated successfully
        if ($success) {
            //  If this is a success then we have a fulfillment returned
            $fulfillment = $response;

            //  Action was executed successfully
            return oq_api_notify($fulfillment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function saveProofOfPayment(Fulfillment $fulfillment)
    {
        //  Fulfillment Instance
        $data = $fulfillment->setLifecycleToVerifyPaymentStatus();
        $success = $data['success'];
        $response = $data['response'];

        //  If the fulfillment proof of payment was updated successfully
        if ($success) {
            //  If this is a success then we have a fulfillment returned
            $fulfillment = $response;

            //  Action was executed successfully
            return oq_api_notify($fulfillment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getDocuments(Request $request, $fulfillment_id)
    {
        try {
            
            $type = request('type');      // e.g) payment proof

            //  Get the associated company
            $fulfillmentDocuments = Fulfillment::findOrFail($fulfillment_id)->documents();

            if( isset($type) && !empty($type) ){
               //$fulfillmentDocuments = $fulfillmentDocuments->where('type', $type);
            }

            $fulfillmentDocuments = $fulfillmentDocuments->get();

            //  Action was executed successfully
            return oq_api_notify($fulfillmentDocuments, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }
*/

}
