<?php

namespace App\Traits;

use DB;
use PDF;
use Mail;
use App\Store;
use App\MyCart;
use App\Company;
use App\Invoice;
use Carbon\Carbon;
use Twilio as Twilio;
use App\CompanyBranch;
use App\RecentActivity;
use App\Mail\OrderMail;
use App\Notifications\OrderCreated;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Orders as OrdersResource;

trait OrderTraits
{
    private $order;
    private $merchant;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($orders = null)
    {
        try {
            if ($orders) {
                //  Transform the orders
                return new OrdersResource($orders);
            } else {
                //  Transform the order
                return new OrderResource($this);
            }
        } catch (\Exception $e) {
            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

    /*  getTemplateSettings() method:
     *
     *  Get the settings required to create an quotation template
     *
     */
    public function getTemplateSettings()
    {
        return [
            'heading_title' => 'ORDER',
            'reference_no_title' => 'Order Number',
            'created_date_title' => 'Created Date',
            'sub_total_title' => 'Sub Total',
            'grand_total_title' => 'Grand Total',
            'recipient_title' => 'ORDER TO',
            'table_columns' => [
               'Items',
               'Qty',
               'Unit Price',
               'Amount'
            ],
            'notes' => [
               'title' => 'Payment Information',
               'details' => 'Note that orders may take 24-48 hrs to process. Payments can be made in cash, cheque or via bank transfer. For any queries regarding your order please contact (+267) 75993221'
            ],
            'colors' => [
               '#017BB8',
               '#EEF4FF'
            ],
            'intro_note' => [
               'title' => 'Thank you for your order',
               'description' => 'Your order has been received and is currently being processed. The order details are show below for your reference.'
            ],
            'how_to_pay' => [
               'title' => 'How To Pay?',
               'description' => 'We have attached our bank account details for your reference. Payment can be done via bank deposit, bank transfer or cheque. Make sure to take a picture or to download your receipt. Use this link below to attach your receipt or proof of payment so that your order is completed. Contact (+267) 75993221 for any assistance you need. Thank you.'
            ],
            'bank_details' => [
               'account_name' => 'Optimum Quality',
               'branch_name' => 'Corporate Branch',
               'branch_code' => '282267',
               'swift_code' => 'FIRNBWGX',
               'account_number' => '57131113369'
            ],
            'footer_notes' => 'Terms & Conditions Apply'
        ];
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new interface.
     *  The $orderInfo variable represents the  
     *  order dataset provided
     */
    public function initiateCreate( $orderInfo = [] )
    {
        /*
         *  The $orderInfo variable represents accepted structure of the USSD 
         *  Interface data required to create a new resource.
         */

        /*  If we have the merchant id  */
        if ($orderInfo['merchant_id']) {

            /*  Retrieve the merchant details using the merchant id  */
            $this->merchant = Store::find($orderInfo['merchant_id']);

        }

        /*  If we have the customer contact id  */
        if (isset($orderInfo['customer_id']) && !empty($orderInfo['customer_id'])) {

            /*  Retrieve the customer contact details from the existing merchant contacts  */
            $customer = $this->merchant->findContactById($orderInfo['customer_id']);


        /*  If we have the customer contact information  */
        } elseif (isset($orderInfo['customer_info']) && !empty($orderInfo['customer_info'])) {

            /*  Create a new customer contact  */
            $customer = $this->merchant->createContact($orderInfo['customer_info']);

        }

        /*  If we have the reference contact id  */
        if (isset($orderInfo['reference_id']) && !empty($orderInfo['reference_id'])) {

            /*  Retrieve the reference contact details from the existing merchant contacts  */
            $reference = $this->merchant->findContactById($orderInfo['reference_id']);

        /*  If we have the reference contact information  */
        } elseif (isset($orderInfo['reference_info']) && !empty($orderInfo['reference_info'])) {
            
            /*  Create a new reference contact  */
            $reference = $this->merchant->createContact($orderInfo['reference_info']);

        /*  If we do not have any reference related information  */
        } else {
            /*  Use the customer contact as the reference contact  */
            $reference = $customer;
        }

        /*  If we have the cart items  */
        if (isset($orderInfo['items']) && !empty($orderInfo['items'])) {

            /*  Retrieve the cart details from the items provided  */
            $cart = ( new \App\MyCart() )->getCartDetails( $orderInfo['items'], $this->merchant->taxes, $this->merchant->discounts );
        
        }

        /*
         *  The $template variable represents structure of the order.
         *  If no template is provided, we create one using the
         *  request data.
         */
        $template = [
            /*  Basic Info  */
            'number' => null,
            'currency' => $this->merchant->currency,
            'created_date' => Carbon::now()->format('Y-m-d H:i:s'),

            /*  Item Info  */
            'item_lines' => $cart['items'] ?? null,

            /*  Taxes, Discounts & Coupons Info  */
            'tax_lines' => $this->merchant->taxes ?? null,
            'discount_lines' => $this->merchant->discounts ?? null,
            'coupon_lines' => $this->merchant->coupons ?? null,

            /*  Grand Total, Sub Total, Tax Total, Discount Total, Shipping Total  */
            'sub_total' => $cart['sub_total'] ?? 0,
            'item_tax_total' => $cart['item_tax_total'] ?? 0,
            'global_tax_total' => $cart['global_tax_total'] ?? 0,
            'grand_tax_total' => $cart['grand_tax_total'] ?? 0,
            'item_discount_total' => $cart['item_discount_total'] ?? 0,
            'global_discount_total' => $cart['global_discount_total'] ?? 0,
            'grand_discount_total' => $cart['grand_discount_total'] ?? 0,
            'shipping_total' => $cart['shipping_total'] ?? 0,
            'grand_total' => $cart['grand_total'] ?? 0,

            /*  Reference Info  */
            'reference_id' => $reference->id ?? null,
            'reference_ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'reference_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,

            /*  Customer Info  */
            'customer_id' => $customer->id ?? null,
            'billing_info' => $customer->getBillingInfo() ?? null,
            'shipping_info' => $customer->getShippingInfo() ?? null,
            'customer_note' => $orderInfo['customer_note'] ?? null,

            /*  Merchant Info  */
            'merchant_id' => $this->merchant->id ?? null,
            'merchant_type' => $this->merchant->resource_type ?? null,
            'merchant_info' => $this->merchant->getBasicInfo() ?? null,

            /*  Meta Data  */
            'metadata' => isset($this->merchant) ? $this->getMetadataInfo() : null,
        ];

        /*
         *  Replace the default template with any custom data provided 
         *  by the orderInfo
         */
        $template = array_merge($template, $orderInfo);

        try {
            /*
             *  Create new a order, then retrieve a fresh instance
             */
            $this->order = $this->create($template)->fresh();

            /*  If the order was created successfully  */
            if ($this->order) {
                /*  Set the order number  */
                $this->order->setOrderNumber();

                /*  Record the activity of the the order creation  */
                $activity = $this->order->recordActivity('created');

                /*  Convert the order into a payable invoice  */
                $invoice = $this->order->convertToInvoice();

                /*  Convert the order into a payable invoice  */
                $status = $this->order->setStatusToPendingPayment();

                /*  Return a fresh instance of the order  */
                return $this->order->fresh();
            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            
        }
    }

    /*  initiateUpdate() method
     *
     *  This method is used to update an existing
     *  order. The $orderInfo variable represents 
     *  the order dataset provided
     */
    public function initiateUpdate( $orderInfo = null )
    {
        //  The $template variable represents structure of the order.
        $template = $orderInfo;

        try {
            
            /*
             *  Update the current order instance
             */
            $updated = $this->update($template);

            /*  If the order was updated successfully  */
            if ($updated) {

                /*  Return a fresh instance of the order  */
                return $this->fresh();

            }
        } catch (\Exception $e) {
            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

    /*  initiateFulfillment() method
     *
     *  This method is used to create order fulfillment
     *  of the current order items. The $orderInfo holds
     *  additional order fulfillment details if any.
     */
    public function initiateFulfillment( $orderInfo = null )
    {
        try {

            $unfulfilled_item_lines = [];

            //  Foreach unfulfilled order item line
            foreach( $this->unfulfilled_item_lines as $unfulfilled_item_line ){

                //  If we have item lines already provided then this means we want to fulfill specific item lines
                if( isset($orderInfo['item_lines']) && !empty($orderInfo['item_lines']) ){

                    //  Foreach specified item line
                    foreach($orderInfo['item_lines'] as $specified_item_line){

                        //  Lets check if the current unfulfilled item line item line matches the current specified item line
                        if( $unfulfilled_item_line['id'] == $specified_item_line['id'] ){

                            if( intval($specified_item_line['quantity']) != 0){

                                $hasValidQuantity = intval($specified_item_line['quantity']) <= intval($unfulfilled_item_line['quantity']);
    
                                $quantity =  $hasValidQuantity ? intval($specified_item_line['quantity']) : intval($unfulfilled_item_line['quantity']);
    
                                $unfulfilled_item_line['quantity'] = $quantity;
                                
                                array_push($unfulfilled_item_lines, $unfulfilled_item_line);

                            }

                        }

                    }

                }else{
                            
                    array_push($unfulfilled_item_lines, $unfulfilled_item_line);

                }

            }

            if( !empty($unfulfilled_item_lines) ){

                /*  Create new Fulfillment using the initiateCreate() method from the Fulfillment Model  */
                $fulfillment = ( new \App\Fulfillment() )->initiateCreate( $fulfillmentInfo = [

                    //  Fulfillment notes 
                    'notes' => $orderInfo['notes'] ?? null,
    
                    //  Fulfillment item lines
                    'item_lines' => $unfulfilled_item_lines,
                    
                    //  Recipient name 
                    'recipient_name' => $orderInfo['recipient_name'] ?? null,
                    
                    //  Recipient contact e.g Phone / Email 
                    'recipient_contact' => $orderInfo['recipient_contact'] ?? null
    
                ]);

                /*  If the fulfillment was created successfully  */
                if ($fulfillment) {

                    /*  Assign the new fulfillment to the order  */
                    $orderUpdateStatus = $fulfillment->update([
                        'owner_id' => $this->id,
                        'owner_type' => $this->resource_type,
                    ]);

                    //  Update fulfilment status
                    $this->updateFulfilmentStatus();

                }

                if($fulfillment && $orderUpdateStatus){

                    return true;

                }else{

                    return false;

                }

            }

        } catch (\Exception $e) {
            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

    public function updateFulfilmentStatus()
    {
      
        try {

            $orderInstance = $this->fresh();

            //  If the quantity of fulfilled item lines is zero (0)
            if( $orderInstance->quantity_of_fulfilled_item_lines == 0 ){

                //  Mark as unfulfilled
                $status = 'unfulfilled';

            //  If the quantity of unfulfilled item lines is zero (0)
            }elseif( $orderInstance->quantity_of_unfulfilled_item_lines == 0 ){

                //  Mark as fully fulfilled
                $status = 'fulfilled';

            //  Otherwise
            }else{

                //  Mark as partially fulfilled
                $status = 'partially fulfilled';

            }

            /*  Update the fulfillment status  */
            $orderUpdateStatus = $orderInstance->update([

                'fulfillment_status' => $status

            ]);

            if($orderUpdateStatus){

                /*  Record the activity of the new order status  */
                $orderInstance->recordActivity( $status );

                return true;

            }else{

                return false;

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            
        }  
    }

    /*  initiatePayment() method
     *
     *  This method is used to create order invoice and payment transation
     *  of the current order items. The $orderInfo holds additional order 
     *  payment details if any.
     */
    public function initiatePayment( $orderInfo = null )
    {
        try {

            $unpaid_item_lines = [];

            //  Foreach unpaid order item line
            foreach( $this->unpaid_item_lines as $unpaid_item_line ){

                //  If we have item lines already provided then this means we want to pay specific item lines
                if( isset($orderInfo['item_lines']) && !empty($orderInfo['item_lines']) ){

                    //  Foreach specified item line
                    foreach($orderInfo['item_lines'] as $specified_item_line){

                        //  Lets check if the current unpaid item line item line matches the current specified item line
                        if( $unpaid_item_line['id'] == $specified_item_line['id'] ){

                            if( intval($specified_item_line['quantity']) != 0){

                                $hasValidQuantity = intval($specified_item_line['quantity']) <= intval($unpaid_item_line['quantity']);
    
                                $quantity =  $hasValidQuantity ? intval($specified_item_line['quantity']) : intval($unpaid_item_line['quantity']);
    
                                $unpaid_item_line['quantity'] = $quantity;
                                
                                array_push($unpaid_item_lines, $unpaid_item_line);

                            }

                        }

                    }

                }else{
                            
                    array_push($unpaid_item_lines, $unpaid_item_line);

                }

            }

            if( !empty($unpaid_item_lines) ){

                //  Re-calculate the cart details using the provided items and order taxes and discounts
                $cartDetails = ( new \App\MyCart() )->getCartDetails( $unpaid_item_lines, $this->tax_lines, $this->discount_lines );

                
                $invoiceInfo = array_merge( $this->only( $this->fillable ), 

                    //  Replace current default order cart details with the updated cart details
                    $cartDetails, 

                    //  Add the order ownership deails (This will assign the invoice to this order)
                    ['owner_id' => $this->id, 'owner_type' => $this->resource_type],

                    //  Replace the item lines with those that are currently being paid for
                    ['item_lines' => $unpaid_item_lines]
                
                );

                //  Create new Invoice using the initiateCreate() method from the Invoice Model
                $invoice = ( new \App\Invoice() )->initiateCreate( $invoiceInfo );

                /*  If the invoice was created successfully  */
                if ($invoice) {

                    //  Mark the order invoice as manually paid
                    $transaction = $invoice->recordManualPayment( $transaction = [
                        'payment_type' => $orderInfo['payment_type'],
                        'payment_amount' => $invoice['grand_total']
                    ]);
                    return $transaction;

                }

            }
                
            return true;

        } catch (\Exception $e) {
            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

    public function setStatusToPendingPayment()
    {
        /*  Set order status to "Pending Payment"  */
        $this->updatePaymentStatus( $status = 'pending payment' );
    }

    public function setStatusToFailedPayment()
    {
        /*  Set order status to "Failed Payment"  */
        $this->updatePaymentStatus( $status = 'failed payment' );
    }

    public function setStatusToPaid()
    {
        /*  Set order status to "Paid"  */
        $this->updatePaymentStatus( $status = 'paid' );
    }

    public function updatePaymentStatus( $status = null )
    {
        try {

            if( $status == null ){

                //  Get a fresh instance of this order
                $orderInstance = $this->fresh();

                //  If the quantity of paid item lines is zero (0)
                if( $orderInstance->quantity_of_paid_item_lines == 0 ){

                    //  Mark as unpaid
                    $status = 'unpaid';

                //  If the quantity of unpaid item lines is zero (0)
                }elseif( $orderInstance->quantity_of_unpaid_item_lines == 0 ){

                    //  Mark as fully paid
                    $status = 'paid';

                //  Otherwise
                }else{

                    //  Mark as partially paid
                    $status = 'partially paid';

                }

            }else{

                //  Get the current instance of the order
                $orderInstance = $this;

                
            }

            //  Update the payment status
            $orderUpdateStatus = $orderInstance->update([

                'payment_status' => $status

            ]);

            //  If the payment status was updated successfully
            if( $orderUpdateStatus ){

                /*  Record the activity of the new order status  */
                $orderInstance->recordActivity( $status );

                return true;

            }else{

                return false;

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            
        }  
    }
    
    /*  setOrderNumber()
     *
     *  This method creates a unique order number using the order id.
     *  It does this by padding the unique order id with leading zero's
     *  "0" so that the order number is always atleast 5 digits long
     */
    public function setOrderNumber()
    {
        /*  Generate a unique order number.
         *  Get the order id, and Pad the left side with leading "0"
         *  e.g 123 = 00123, 1234 = 01234, 12345 = 12345
         */
        $order_number = str_pad($this->id, 5, 0, STR_PAD_LEFT);

        /*  Set the unique order number  */
        $this->update(['number' => $order_number]);
    }

    /*  getMetadataInfo()
     *
     *  This method returns the metadata template that contains order
     *  design and custom information defined by the order's owning
     *  merchant
     */
    public function getMetadataInfo()
    {
        /*  Get the merchants order settings  */
        $orderSettings = $this->merchant->settings['details']['orderTemplate'] ?? null;

        /*  If the merchants order settings were found  */
        if ($orderSettings) {
            $template = [
                'heading_title' => $orderSettings['heading_title'],
                'reference_no_title' => $orderSettings['reference_no_title'],
                'created_date_title' => $orderSettings['created_date_title'],
                'sub_total_title' => $orderSettings['sub_total_title'],
                'grand_total_title' => $orderSettings['grand_total_title'],
                'recipient_title' => $orderSettings['recipient_title'],
                'table_columns' => $orderSettings['table_columns'],
                'notes' => $orderSettings['notes'],
                'colors' => $orderSettings['colors'],
                'footer_notes' => $orderSettings['footer_notes'],
            ];

            return $template;
        }
    }

    public function convertToInvoice()
    {
        //  Create a new invoice using this order details
        $invoice = ( new Invoice() )->initiateCreate($invoiceInfo = $this);

        /*  If the invoice was created successfully  */
        if ($invoice) {
            /*  Assign the new invoice to the order  */
            $invoice->update([
                'owner_id' => $this->id,
                'owner_type' => $this->resource_type,
            ]);
        }

        //  Return the created invoice
        return $invoice;
    }

    public function smsOrderToMerchant()
    {
        //  Get the order merchant
        $merchant = $this->merchant()->first();

        //  If we have the order merchant
        if( $merchant ){
           
            //  Get the order merchants default mobile number or the first available mobile number
            $mobile = $merchant->getAvailalbleMobilePhone();

            //  If we have the order merchant mobile phone
            if( $mobile ){
                    
                try{

                    //  Send order sms
                    return Twilio::message('+'.$mobile['calling_code'].$mobile['number'], $this->createOrderSms());

                } catch (\Exception $e) {

                    //  Return the error
                    return oq_api_notify_error('Query Error', $e->getMessage(), 404);

                }

            }

        }
    }

    public function createOrderSms()
    {
        //  Set the character limit
        $character_limit = 160;

        //  Get the order number
        $order_number = $this->number;

        //  Get the order grand total
        $grand_total = number_format($this->grand_total, 2, '.', ',');

        //  Get the currency symbol or currency code
        $currency = $this->currency['symbol'] ?? $this->currency['code'];

        //  Get the merchant
        $merchant = $this->merchant()->first();

        //  Get the reference/customer available mobile number
        $mobile = $this->reference->getAvailalbleMobilePhone() ?? $this->customer->getAvailalbleMobilePhone();
        $mobile_number = $mobile ? $mobile['calling_code'].$mobile['number'] : null;

        //  Get the cart items (inline)  e.g 1x(Product 1), 2x(Product 3)
        $items_inline = ( new \App\MyCart() )->getItemsSummarizedInline($this->item_lines);

        //  Craft the sms message
        $order_number = 'Order #'.$order_number.', ';
        $items = 'for '.$items_inline;
        $amount = 'Amount: '.$currency.$grand_total.'.';
        $dial = 'Dial '.$merchant->team_access_code.' to view order. Customer: '.$mobile_number;

        $characters_left = ($character_limit - strlen($order_number.$amount.$dial));
        $summarized_items = $this->truncateWithDots($items.(strlen($items) < $characters_left ? '.' : ''), $characters_left);
        $sms = $order_number.$summarized_items.$amount.$dial;

        //  Return the sms message
        return $sms;
    }

    public function truncateWithDots($string, $limit)
    {
        return (strlen($string) > $limit) ? substr($string, $limit - 3).'...' : $string;
    }

    /*  recordActivity()
     *
     *  This method saves the activity of the order with a specified status
     *  as well as activity data and the authenticated user responsible
     *  for the activity.
     */
    public function recordActivity($status, $data = null)
    {
        return ( new \App\RecentActivity() )->saveActivity($this, $status, $data);
    }

    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of order results.
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
         *     authenticated user. This means we can access all possible records
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = request('allocation');

        /*
         *  $orderStatus =
        /*
         *  The $orderStatus variable is used to determine which status of orders to pull.
         *  It represents orders with a particular status. The user may request orders
         *  with a status of:
         *  1) pending: Order received but unpaid
         *  2) processing: Order received and stock reduced (order awaiting fulfilment)
         *  3) on-hold: Order awaiting payment and confirmation
         *  4) completed: Order fulfiled (No further actions required)
         *  5) cancelled: Order was cancelled by admin/staff
         *  6) refunded: Order refunded by admin/staff
         *  7) failed : Order payment failed (via payment gateway)
         */
        $orderStatus = strtolower(request('orderStatus'));

        /*
         *  $storeId = 1, 2, 3, e.t.c
        /*
         *  The $storeId variable only get data specifically related to
         *  the specified store id. It is useful for scenerios where we
         *  want only orders of that store only
         */
        $storeId = request('storeId');

        /*
         *  $companyBranchId = 1, 2, 3, e.t.c
        /*
         *  The $companyBranchId variable only get data specifically related to
         *  the specified company branch id. It is useful for scenerios where we
         *  want only orders of that branch only
         */
        $companyBranchId = request('companyBranchId');

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only orders of that company only
         */
        $companyId = request('companyId');

        if (isset($storeId) && !empty($storeId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED STORE ORDERS    *
            /********************************************************************/

            $model = Store::find($storeId);
        } elseif (isset($companyBranchId) && !empty($companyBranchId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED BRANCH ORDERS   *
            /********************************************************************/

            $model = CompanyBranch::find($companyBranchId);
        } elseif (isset($companyId) && !empty($companyId)) {
            /**********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY ORDERS    *
            /**********************************************************************/

            $model = Company::find($companyId);
        } else {
            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL ORDERS         *
                /**********************************************************/

                //  Get the current order instance
                $model = $this;
            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH ORDERS    *
                /*************************************************************/

                // Only get orders associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY ORDERS    *
                /**************************************************************/

                //  Only get orders associated to the company
                $model = $auth_user->company;
            }
        }

        if (isset($orderStatus) && !empty($orderStatus)) {
            //  If the $orderStatus is a list e.g) pending,processing,on-hold ... e.t.c
            $orderStatus = explode(',', $orderStatus);

            //  If we have atleast one status
            if (count($orderStatus)) {
                //  Get orders only with the specified status
                $orders = $model->orders()->whereIn('status', $orderStatus);
            }
        } else {
            //  Otherwise get all orders
            $orders = $model->orders();
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

        $order_join = 'orders';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $orders = $orders->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $orders = $orders->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $orders = $orders->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if (request('count') == 1) {
                //  If the orders are paginated
                if ($config['paginate']) {
                    $orders = $orders->total() ?? 0;
                //  If the orders are not paginated
                } else {
                    $orders = $orders->count() ?? 0;
                }
            } else {
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $orders = $orders->get();
                }

                //  If we have any orders so far
                if ($orders) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $orders->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $orders];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateShow() method:
     *
     *  This is used to return only one specific order.
     *
     */
    public function initiateShow($order_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed order
            if (request('withtrashed') == 1) {
                //  Run query
                $order = $this->withTrashed()->where('id', $order_id)->first();

            //  Get the non-trashed order
            } else {
                //  Run query
                $order = $this->where('id', $order_id)->first();
            }

            //  If we have any order so far
            if ($order) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $order->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $order];
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
     *  This is used to create a new order. It also works
     *  to order the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the order.
     *
     */
    public function initiateCreate2($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE ORDER      *
         ******************************************************/

        /*********************************************
         *   VALIDATE ORDER INFORMATION              *
         ********************************************/

        //  Get the associated store
        $store = Store::find(request('store_id'));

        //  Check if the store has any settings
        $storeSettings = $store->settings;

        //  If the store does not exist
        if (!$store) {
            //  Notify the user that the store was not found
            return ['success' => false, 'response' => oq_api_notify_error(null, ['general' => ['The store does not exist']], 404)];
        }

        //  MyCart Instance
        $cart = request('cart');
        $cartInstance = ( new MyCart() );
        $cartRequest = $cartInstance->initiateGetCartDetails($cart, $store->id);

        if ($cartRequest['success'] && count($cartRequest['response'])) {
            $cartDetails = $cartRequest['response'];
        } else {
            //  Notify the user that the cart does not exist
            return ['success' => false, 'response' => $cartRequest];
        }

        //  Create a template to hold the order details
        $template = $template ?? [
            /*  Basic Info  */
            'number' => request('number') ?? null,
            'currency_type' => $storeSettings['general']['currency_type'] ?? (request('currency_type') ?? null),
            'created_date' => request('created_date') ?? null,

            /*  Item Info  */
            'items' => $cartDetails['items'] ?? (request('items') ?? null),

            /*  Store Info  */
            'taxes' => $store->taxes ?? (request('taxes') ?? null),
            'discounts' => $store->discounts ?? (request('discounts') ?? null),
            'coupons' => $store->coupons ?? (request('coupons') ?? null),

            /*  Grand Total, Sub Total, Tax Total, Discount Total, Shipping Total  */
            'sub_total' => $cartDetails['sub_total'] ?? (request('sub_total') ?? 0),
            'item_tax_total' => $cartDetails['item_tax_total'] ?? (request('item_tax_total') ?? 0),
            'global_tax_total' => $cartDetails['global_tax_total'] ?? (request('global_tax_total') ?? 0),
            'grand_tax_total' => $cartDetails['grand_tax_total'] ?? (request('grand_tax_total') ?? 0),
            'item_discount_total' => $cartDetails['item_discount_total'] ?? (request('item_discount_total') ?? 0),
            'global_discount_total' => $cartDetails['global_discount_total'] ?? (request('global_discount_total') ?? 0),
            'grand_discount_total' => $cartDetails['grand_discount_total'] ?? (request('grand_discount_total') ?? 0),
            'shipping_total' => $cartDetails['shipping_total'] ?? (request('shipping_total') ?? 0),
            'grand_total' => $cartDetails['grand_total'] ?? (request('grand_total') ?? 0),

            /*  Customer Info  */
            'reference_id' => request('reference_id') ?? null,
            'reference_ip_address' => request('reference_ip_address') ?? null,
            'reference_user_agent' => request('reference_user_agent') ?? null,
            'customer_note' => request('customer_note') ?? null,
            'billing_info' => request('billing_info') ?? null,
            'shipping_info' => request('shipping_info') ?? null,

            /*  Company Info  */
            'company_info' => request('company_info') ?? null,

            /*  Allocation Details  */
            'orderable_id' => $store->id ?? null,
            'orderable_type' => 'store',

            /*  Meta Data  */
            'meta' => request('meta') ?? null,
        ];

        try {
            //  Create the order
            $order = $this->create($template)->fresh();

            //  If the order was created successfully
            if ($order) {
                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new OrderCreated($order));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of order created
                $status = 'created';
                $orderCreatedActivity = oq_saveActivity($order, $auth_user, $status, ['order' => $order->summarize()]);

                //  Check and generate the order invoice
                $invoiceRequest = $this->convertToInvoice($order, $store);

                return ['success' => true, 'response' => $invoiceRequest];

                //  If the invoice was created successfully
                if ($invoiceRequest && $invoiceRequest['success']) {
                    //  Get the invoice created
                    $invoice = $invoiceRequest['response'];

                    //  Send the order invoice
                    $sentOrderMail = $this->checkAndSendOrderInvoice($invoice, $order, $store);
                }

                //  refetch the updated order
                $order = $order->fresh();

                //  Action was executed successfully
                return ['success' => true, 'response' => $order];
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

    /*  initiateUpdate2() method:
     *
     *  This is used to update an existing order. It also works
     *  to order the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the order.
     *
     */
    public function initiateUpdate2($order_id)
    {
    }

    public function checkAndSendOrderInvoice($invoice, $order, $store)
    {
        $auth_user = auth('api')->user();
        $deliveryMethods = request('deliveryMethods');

        /******************************
         * Send invoice via Email/Sms *
         ******************************/

        //  If specified to send invoice via mail
        if (isset($deliveryMethods) && !empty($deliveryMethods)) {
            $mailDetails = request('mail');

            //  If specified to send invoice via sms
            if (in_array('Sms', $deliveryMethods)) {
                //  Send via sms
                ( new Invoice() )->sendInvoiceAsSMS($invoice);
            }

            //  If specified to send invoice via mail and we have the mail details
            if (in_array('Email', $deliveryMethods) && isset($mailDetails) && !empty($mailDetails)) {
                //  Email details
                $primaryEmails = $mailDetails['primaryEmails'];
                $ccEmails = $mailDetails['ccEmails'];
                $bccEmails = $mailDetails['bccEmails'];
                $subject = /* $mailDetails['subject']; */ 'Thank you for your order!';
                $message = /* $mailDetails['message']; */ 'Your order was received thank you.';

                //  Send via email
                return $this->sendOrderAsMail(
                            $order, $invoice,
                            $mailDetails = [
                                'primaryEmails' => $primaryEmails, 'ccEmails' => $ccEmails, 'bccEmails' => $bccEmails,
                                'subject' => $subject,  'message' => $message,
                            ],
                            $auth_user,
                            $config = [
                                'attach_order_pdf' => request('attach_order_pdf') || true,
                                'attach_invoice_pdf' => request('attach_order_pdf') || true,
                                'attach_bank_details_pdf' => request('attach_order_pdf') || true,
                            ]
                        );
            }
        }

        return false;
    }

    public function sendOrderAsMail($order, $invoice, $mailDetails = [], $user = null, $mailConfig = [])
    {
        //  Default settings for the mailDetails
        $defaultMailDetails = array(
            'primaryEmails' => null, 'ccEmails' => null, 'bccEmails' => null, 'subject' => null,  'message' => null,
        );

        //  Replace defaults with any provided options
        $mailDetails = array_merge($defaultMailDetails, $mailDetails);

        //  Default settings
        $defaultMailConfig = array('attach_order_pdf' => true, 'attach_invoice_pdf' => true, 'attach_bank_details_pdf' => true);

        //  Replace defaults with any provided options
        $mailConfig = array_merge($defaultMailConfig, $mailConfig);

        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /*****************************
         *   GET EMAIL DETAILS       *
         *****************************/

        $primaryEmails = $mailDetails['primaryEmails'] ?? request('mail')['primaryEmails'];
        $ccEmails = $mailDetails['ccEmails'] ?? request('mail')['ccEmails'];
        $bccEmails = $mailDetails['bccEmails'] ?? request('mail')['bccEmails'];
        $subject = $mailDetails['subject'] ?? request('mail')['subject'];
        $message = $mailDetails['message'] ?? request('mail')['message'];

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        //  If this is a test email
        if (request('test') == 1) {
            $status = 'sent test email';
        // $auth_user->notify(new OrderTestEmailSent($order));

        //  Otherwise if this is not a test email
        } else {
            $status = 'sent email';
            // $auth_user->notify(new OrderEmailSent($order));
        }

        /***********************************************
         *   REPLACE SHORTCODES WITH ACTUAL CONTENT    *
         ***********************************************/

        $message = $this->replaceShortcodes($order, $message);
        $subject = $this->replaceShortcodes($order, $subject);

        //  Foreach email
        foreach ($primaryEmails as $primaryEmail) {
            /******************************
             *   SEND ORDER VIA MAIL      *
             ******************************/

            //  Order PDF
            $orderPDF = $this->getOrderAsPDF($order);

            //  Invoice PDF
            $invoicePDF = ( new Invoice() )->getInvoiceAsPDF($invoice);

            //  Store Bank Account PDF
            $bankDetailsPDF = ( new Store() )->getStoreBankAccountDetailsAsPDF($order->store);

            $mailData = [$subject, $message];

            Mail::to($primaryEmail)->send(new OrderMail(
                $subject, $message,
                //  Order and Invoice
                $order, $invoice,
                //  Order PDF Details
                $orderPDF,
                //  Invoice PDF Details
                $invoicePDF,
                //  Bank Account PDF Details
                $bankDetailsPDF,
                //  Config to help us know which PDF's to attach.
                $mailConfig
            ));

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $mail = ['email' => $primaryEmail, 'subject' => $subject, 'message' => $message];

            //  Record activity of order sent receipt
            $orderSentActivity = oq_saveActivity($order, $auth_user, $status, ['order' => $order->summarize(), 'mail' => $mail]);

            //  Action was executed successfully
            return ['success' => true, 'response' => $mail];
        }
    }

    public function getOrderAsPDF($order)
    {
        return PDF::loadView('pdf.order', array('order' => $order));
    }

    /*  replaceShortcodes() method:
     *
     *  This is used to replace all shortcodes within a given message
     *  The method goes and checks for any shortcode that can be converted
     *  to actual information used in the order. E.g it would replace
     *  the shortcode [grand_total] with the actual grand total amount
     *  of the order.
     *
     */
    public function replaceShortcodes($order, $data)
    {
        $client = $order->customized_customer_details;
        $company = $order->customized_company_details;
        $currency = $order->currency_type['currency']['symbol'] ?? '';
        $sub_total = $currency.number_format($order->sub_total_value, 2, ',', '.');
        $grand_total = $currency.number_format($order->grand_total, 2, ',', '.');

        //  Custom Order Variables - Shortcodes
        $customFields = [
            '[order_heading]' => $order->heading,
            '[order_reference_no]' => '#'.$order->reference_no_value,
            '[created_date]' => (new Carbon($order->created_date))->format('M d Y'),
            '[expiry_date]' => (new Carbon($order->expiry_date))->format('M d Y'),
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

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific
     *  columns that can then be used for storage. We may only want to summarize
     *  the data to very important information, rather than storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want (we access the fillable columns of the model), while also
     *  removing any custom attributes we do not want to order
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
