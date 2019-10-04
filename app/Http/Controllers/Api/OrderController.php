<?php

namespace App\Http\Controllers\Api;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getOrders()
    {
        //  Check if the user is authourized to view all orders
        if ($this->user->can('viewAll', Order::class)) {
        
            //  Get the orders
            $orders = Order::paginate();

            //  Check if the orders exist
            if ($orders) {

                //  Return an API Readable Format of the Order Instance
                return ( new \App\Order )->convertToApiFormat($orders);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getOrder( $order_id )
    {
        //  Get the order
        $order = Order::where('id', $order_id)->first() ?? null;

        //  Check if the order exists
        if ($order) {

            //  Check if the user is authourized to view the order
            if ($this->user->can('view', $order)) {

                //  Return an API Readable Format of the Order Instance
                return $order->convertToApiFormat();

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

    public function getOrderMerchant( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order merchant
        $merchant = $order->merchant ?? null;

        //  Check if the merchant exists
        if ($merchant) {

            //  Check if the user is authourized to view the order merchant
            if ($this->user->can('view', $order)) {

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

    public function getOrderCustomer( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order customer
        $customer = $order->customer ?? null;

        //  Check if the customer exists
        if ($customer) {

            //  Check if the user is authourized to view the order customer
            if ($this->user->can('view', $order)) {

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

    public function getOrderReference( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order reference
        $reference = $order->reference ?? null;

        //  Check if the reference exists
        if ($reference) {

            //  Check if the user is authourized to view the order reference
            if ($this->user->can('view', $order)) {

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

    public function getOrderDocuments( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order documents
        $documents = $order->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the order documents
            if ($this->user->can('view', $order)) {

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

    public function getOrderDocument( $order_id, $document_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order document
        $document = $order->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the order document
            if ($this->user->can('view', $order)) {

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

    public function getOrderInvoices( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order invoices
        $invoices = $order->invoices()->paginate() ?? null;

        //  Check if the invoices exist
        if ($invoices) {

            //  Check if the user is authourized to view the order invoices
            if ($this->user->can('view', $order)) {

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

    public function getOrderInvoice( $order_id, $invoice_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order invoice
        $invoice = $order->invoices()->where('id', $invoice_id)->first() ?? null;

        //  Check if the invoice exists
        if ($invoice) {

            //  Check if the user is authourized to view the order invoice
            if ($this->user->can('view', $order)) {

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

    public function getOrderTaxes( $order_id )
    {
        //  Get the order
        $order = Order::find($order_id);

        //  Get the order taxes
        $taxes = $order->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the order taxes
            if ($this->user->can('view', $order)) {

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

    public function getOrderTax( $order_id, $tax_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order tax
        $tax = $order->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the order tax
            if ($this->user->can('view', $order)) {

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

    public function getOrderDiscounts( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the store discounts
        $discounts = $order->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the order discounts
            if ($this->user->can('view', $order)) {

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

    public function getOrderDiscount( $order_id, $discount_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order discount
        $discount = $order->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the order discount
            if ($this->user->can('view', $order)) {

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

    public function getOrderCoupons( $order_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order coupons
        $coupons = $order->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the order coupons
            if ($this->user->can('view', $order)) {

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

    public function getOrderCoupon( $order_id, $coupon_id )
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order coupons
        $coupons = $order->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the order coupons
            if ($this->user->can('view', $order)) {

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

    public function getOrderMessages($order_id)
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order messages
        $messages = $order->messages()->paginate() ?? null;

        //  Check if the messages exist
        if ($messages) {

            //  Check if the user is authourized to view the order messages
            if ($this->user->can('view', $order)) {

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

    public function getOrderMessage($order_id, $message_id)
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order message
        $message = $order->messages()->where('messages.id', $message_id)->first() ?? null;

        //  Check if the message exists
        if ($message) {

            //  Check if the user is authourized to view the order message
            if ($this->user->can('view', $order)) {

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

    public function getOrderReviews($order_id)
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order reviews
        $reviews = $order->reviews()->paginate() ?? null;

        //  Check if the reviews exist
        if ($reviews) {

            //  Check if the user is authourized to view the order reviews
            if ($this->user->can('view', $order)) {

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

    public function getOrderReview($order_id, $review_id)
    {
        //  Get the order
        $order = Order::findOrFail($order_id);

        //  Get the order review
        $review = $order->reviews()->where('reviews.id', $review_id)->first() ?? null;

        //  Check if the review exists
        if ($review) {
            
            //  Check if the user is authourized to view the order review
            if ($this->user->can('view', $order)) {

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
        //  Order Instance
        $data = ( new Order() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the orders were found successfully
        if ($success) {
            //  If this is a success then we have the orders
            $orders = $response;

            //  Action was executed successfully
            return oq_api_notify($orders, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($order_id)
    {
        //  Order Instance
        $data = ( new Order() )->initiateShow($order_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the order was found successfully
        if ($success) {
            //  If this is a success then we have the order
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the order
        $data = ( new Order() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the order was created successfully
        if ($success) {
            //  If this is a success then we have a order returned
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($order_id)
    {
        //  Order Instance
        $data = ( new Order() )->initiateUpdate($order_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the order was updated successfully
        if ($success) {
            //  If this is a success then we have a order returned
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function saveProofOfPayment(Order $order)
    {
        //  Order Instance
        $data = $order->setLifecycleToVerifyPaymentStatus();
        $success = $data['success'];
        $response = $data['response'];

        //  If the order proof of payment was updated successfully
        if ($success) {
            //  If this is a success then we have a order returned
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getDocuments(Request $request, $order_id)
    {
        try {
            
            $type = request('type');      // e.g) payment proof

            //  Get the associated company
            $orderDocuments = Order::findOrFail($order_id)->documents();

            if( isset($type) && !empty($type) ){
               //$orderDocuments = $orderDocuments->where('type', $type);
            }

            $orderDocuments = $orderDocuments->get();

            //  Action was executed successfully
            return oq_api_notify($orderDocuments, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }
*/

}
