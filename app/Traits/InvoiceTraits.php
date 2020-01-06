<?php

namespace App\Traits;

use PDF;
use App\Store;
use Carbon\Carbon;
use Twilio as Twilio;

//  Mails
use Mail;
use App\Mail\InvoiceMail;
use App\Mail\InvoiceReceiptMail;

//  Notifications
use App\Notifications\InvoicePaid;
use App\Notifications\InvoiceSmsSent;
use App\Notifications\InvoiceCreated;
use App\Notifications\InvoiceUpdated;
use App\Notifications\InvoiceApproved;
use App\Notifications\InvoiceEmailSent;
use App\Notifications\InvoiceTestSmsSent;
use App\Notifications\InvoiceTestEmailSent;
use App\Notifications\InvoiceReceiptSmsSent;
use App\Notifications\InvoiceReceiptEmailSent;
use App\Notifications\InvoicePaymentCancelled;
use App\Notifications\InvoiceReceiptTestSmsSent;
use App\Notifications\InvoiceReceiptTestEmailSent;
use App\Notifications\InvoiceRecurringSettingsApproved;
use App\Notifications\InvoiceRecurringSettingsPaymentPlanUpdated;
use App\Notifications\InvoiceRecurringSettingsDeliveryPlanUpdated;

//  Resources
use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Resources\Invoices as InvoicesResource;

use Illuminate\Pagination\LengthAwarePaginator;

trait InvoiceTraits
{
    private $invoiceInstance;
    private $merchant;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($invoices = null)
    {

        try {

            if( $invoices ){

                //  Transform the invoices
                return new InvoicesResource($invoices);

            }else{

                //  Transform the invoice
                return new InvoiceResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  getTemplateSettings() method:
     *
     *  Get the settings required to create an invoice template
     *
     */
    public function getTemplateSettings()
    {
        return [
            'heading_title' => 'INVOICE',
            'reference_no_title' => 'Invoice Number',
            'created_date_title' => 'Invoice Date',
            'expiry_date_title' => 'Due Date',
            'expire_after_no_of_days' => '7',
            'sub_total_title' => 'Total',
            'grand_total_title' => 'Grand Total',
            'recipient_title' => 'INVOICE TO',
            'table_columns' => [
               'Services',
               'Quantity',
               'Unit Price',
               'Amount'
            ],
            'notes' => [
               'title' => 'Payment Information',
               'details' => '<p><b>Note that all payments must be made before the invoice expiry date since invoice prices are subject to change. Payments can be made in cash, cheque or via bank transfer'
            ],
            'colors' => [
               '#017BB8',
               '#EEF4FF'
            ],
            'footer_notes' => 'Terms & Conditions Apply'

        ];
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new interface.
     *  The $invoiceInfo variable represents the  
     *  invoice dataset provided
     */
    public function initiateCreate( $invoiceInfo = [] )
    {
        //  Incase we have passed an Object always convert it to an Array
        $invoiceInfo = collect( $invoiceInfo )->toArray();

        /*
         *  The $invoiceInfo variable represents accepted structure of the USSD 
         *  Interface data required to create a new resource.
         */

        /*  If we have the merchant id  */
        if ($invoiceInfo['merchant_id']) {

            /*  Retrieve the merchant details using the merchant id  */
            $this->merchant = Store::find($invoiceInfo['merchant_id']);

        }

        /*  If we have the customer contact id  */
        if( isset($invoiceInfo['customer_id']) && !empty($invoiceInfo['customer_id']) ){

            /*  Retrieve the customer contact details from the existing merchant contacts  */
            $customer = $this->merchant->findContactById( $invoiceInfo['customer_id'] );

        /*  If we have the customer contact information  */
        }elseif( isset($invoiceInfo['customer_info']) && !empty($invoiceInfo['customer_info']) ){

            /*  Create a new customer contact  */
            $customer = $this->merchant->createContact( $invoiceInfo['customer_info'] );
        }

        /*  If we have the reference contact id  */
        if( isset($invoiceInfo['reference_id']) && !empty($invoiceInfo['reference_id']) ){

            /*  Retrieve the reference contact details from the existing merchant contacts  */
            $reference = $this->merchant->findContactById( $invoiceInfo['reference_id'] );

        /*  If we have the reference contact information  */
        }elseif( isset($invoiceInfo['reference_info']) && !empty($invoiceInfo['reference_info']) ){

            /*  Create a new reference contact  */
            $reference = $this->merchant->createContact( $invoiceInfo['reference_info'] );

        /*  If we do not have any reference related information  */
        }else{

            /*  Use the customer contact as the reference contact  */
            $reference = $customer;

        }

        /*  If we have the cart items  */
        if( isset($invoiceInfo['items']) && !empty($invoiceInfo['items']) ){

            /*  Retrieve the cart details from the items provided  */
            $cart = ( new \App\MyCart() )->getCartDetails( $invoiceInfo['items'], $this->merchant->taxes, $this->merchant->discounts );

        }

        /*
         *  The $template variable represents structure of the invoice.
         *  If no template is provided, we create one using the 
         *  request data.
         */
        $template = [

            /*  Basic Info  */
            'number' => null,
            'currency' => $this->merchant->currency ?? null,
            'created_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'expiry_date' =>  Carbon::now()->format('Y-m-d H:i:s'),

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
            'customer_note' => $invoiceInfo['customer_note'] ?? null,

            /*  Merchant Info  */
            'merchant_id' => $this->merchant->id ?? null,
            'merchant_type' => $this->merchant->resource_type ?? null,
            'merchant_info' => $this->merchant->getBasicInfo() ?? null,

            /*  Meta Data  */
            'metadata' => isset($this->merchant) ? $this->getMetadataInfo() : null

        ];

        /*
         *  Replace the default template with any custom data
         */
        $template = array_merge($template, $invoiceInfo);

        try {

            /*
             *  Create new a invoice, then retrieve a fresh instance
             */
            $this->invoiceInstance = $this->create($template)->fresh();

            /*  If the invoice was created successfully  */
            if( $this->invoiceInstance ){

                /*  Set the invoice number  */
                $this->invoiceInstance->setInvoiceNumber();

                /*  Record the activity of the the invoice creation  */
                $activity = $this->invoiceInstance->recordActivity('created');
  
                /*  Return a fresh instance of the invoice  */
                return $this->invoiceInstance->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

    /*  getMetadataInfo()
     *
     *  This method returns the metadata template that contains invoice
     *  design and custom information defined by the invoices owning
     *  merchant
     */
    public function getMetadataInfo()
    {
        /*  Get the merchants invoice settings  */
        $invoiceSettings = $this->merchant->settings['details']['invoiceTemplate'] ?? null;

        /*  If the merchants invoice settings were found  */
        if( $invoiceSettings ){
         
            $template = [

                'heading_title' =>  $invoiceSettings['heading_title'],
                'reference_no_title' =>  $invoiceSettings['reference_no_title'],
                'created_date_title' =>  $invoiceSettings['created_date_title'],
                'sub_total_title' =>  $invoiceSettings['sub_total_title'],
                'grand_total_title' =>  $invoiceSettings['grand_total_title'],
                'recipient_title' =>  $invoiceSettings['recipient_title'],
                'table_columns' =>  $invoiceSettings['table_columns'],
                'notes' =>  $invoiceSettings['notes'],
                'colors' =>  $invoiceSettings['colors'],
                'footer_notes' =>  $invoiceSettings['footer_notes']

            ];

            return $template;

        }
    }


    /*  setInvoiceNumber()
     *
     *  This method creates a unique invoice number using the invoice id.
     *  It does this by padding the unique invoice id with leading zero's
     *  "0" so that the invoice number is always atleast 5 digits long
     */
    public function setInvoiceNumber()
    {
        /*  Generate a unique invoice number. 
         *  Get the invoice id, and Pad the left side with leading "0"
         *  e.g 123 = 00123, 1234 = 01234, 12345 = 12345
         */
        $invoice_number = str_pad($this->id, 5, 0, STR_PAD_LEFT);

        /*  Set the unique invoice number  */
        $this->update(['number' => $invoice_number]);
    }

    public function smsInvoiceToCustomer()
    {
        //  Get the order customer
        $customer = $this->customer()->first();

        //  If we have the order customer
        if( $customer ){
           
            //  Get the customers default mobile number or the first available mobile number
            $mobile = $customer->getAvailalbleMobilePhone();

            //  If we have the customers mobile phone
            if( $mobile ){
                    
                try{

                    //  Send invoice sms
                    return Twilio::message('+'.$mobile['calling_code'].$mobile['number'], $this->createInvoiceSms());

                } catch (\Exception $e) {

                    //  Return the error
                    return oq_api_notify_error('Query Error', $e->getMessage(), 404);

                }

            }

        }
    }

    public function createInvoiceSms()
    {
        //  Set the character limit
        $character_limit = 160;

        //  Get the invoice number
        $invoice_number = $this->number;

        //  Get the invoice grand total
        $grand_total = number_format($this->grand_total, 2, '.', ',');

        //  Get the currency symbol or currency code
        $currency = $this->currency['symbol'] ?? $this->currency['code'];

        //  Get the merchant
        $merchant = $this->merchant()->first();

        //  Get the invoice expiry date
        $expiry_date = (new Carbon($this->expiry_date))->format('M d Y');

        //  Get the reference/customer available mobile number
        $mobile = $this->reference->getAvailalbleMobilePhone() ?? $this->customer->getAvailalbleMobilePhone();
        $mobile_number = $mobile ? $mobile['calling_code'].$mobile['number'] : null;

        //  Get the cart items (inline) e.g 1x(Product 1), 2x(Product 2)
        $items_inline = ( new \App\MyCart() )->getItemsSummarizedInline($this->item_lines);

        //  Craft the sms message
        $invoice_number = 'Invoice #'.$invoice_number.', ';
        $items = 'for '.$items_inline;
        $amount = 'Amount: '.$currency.$grand_total.' ';
        $due_date = 'due '.$expiry_date.'.';
        $dial = 'Dial '.$merchant->customer_access_code.' to pay';

        $characters_left = ($character_limit - strlen($invoice_number.$amount.$due_date.$dial));
        $summarized_items = $this->truncateWithDots($items.(strlen($items) < $characters_left ? '.' : ''), $characters_left);
        $sms = $invoice_number.$summarized_items.$amount.$due_date.$dial;

        //  Return the sms message
        return $sms;
    }

    public function smsInvoiceReceiptToCustomer()
    {
        /*  Get the customer's default mobile number or the first available mobile number  */
        $mobile = $this->customer->getAvailalbleMobilePhone();

        /*  If we have a mobile phone  */
        if( $mobile ){

            /*  Structure the phone number  */
            $phone_number = '+'.$mobile['calling_code'].$mobile['number'];

            /*  Send invoice sms  */
            return Twilio::message($phone_number, $this->createInvoiceReceiptSms());

        }
    }

    public function createInvoiceReceiptSms()
    {
        //  Set the character limit
        $character_limit = 160;

        //  Get the invoice number
        $invoice_number = $this->number;

        //  Get the order number (if linked to order)
        $order_number = $this->owner->resource_type == 'order' ? $this->owner->number : null;

        //  Get the invoice grand total
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
        $invoice_number = 'Payment confirmation: Invoice #'.$invoice_number;
        $order_number = ($order_number) ? ', Order #'.$order_number.' ' : ' ';
        $items = 'for '.$items_inline;
        $amount = 'Amount: '.$currency.$grand_total.'.';
        $dial = 'Dial '.$merchant->customer_access_code.' to view order';

        $characters_left = ($character_limit - strlen($invoice_number.$order_number.$amount.$dial));
        $summarized_items = $this->truncateWithDots($items.(strlen($items) < $characters_left ? '.' : ''), $characters_left);
        $sms = $invoice_number.$order_number.$summarized_items.$amount.$dial;

        /*  Return the sms message  */
        return $sms;
    }

    public function truncateWithDots($string, $limit)
    {
        return (strlen($string) > $limit) ? substr($string, $limit - 3).'...' : $string;
    }

    public function recordAutomaticPayment($transaction = [])
    {
        if( !empty($transaction) ){

            $template = [
                'type' => 'payment',
                'automatic' => true,
                'status' => $transaction['status'] ?? 'success',
                'payment_type' => $transaction['payment_type'] ?? null,
                'payment_amount' => $transaction['payment_amount'] ?? 0,
            ];

            return $this->recordTransaction($template);
        }

        return false;
    }

    public function recordManualPayment($transaction = [])
    {
        if( !empty($transaction) ){

            $template = [
                'type' => 'payment',
                'automatic' => false,
                'status' => $transaction['status'] ?? 'success',
                'payment_type' => $transaction['payment_type'] ?? null,
                'payment_amount' => $transaction['payment_amount'] ?? 0,
            ];

            return $this->recordTransaction($template);
        }

        return false;

    }

    public function recordTransaction($template = [])
    {
        $transaction = ( new \App\Transaction() )->initiateCreate($template);

        //  If the transaction was created successfully
        if( $transaction ){

            //  Assign the new transaction to the invoice
            $transaction->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type
            ]);

            //  Record the activity of the the invoice payment
            $activity = $this->recordActivity('paid');

            //  If the invoice belongs to an Order
            if( $this->owner->resource_type == 'order'){
                
                //  If the template status is "success"
                if( $template['status'] == 'success' ){
                    
                    //  Update the current order payment status
                    $this->owner->updatePaymentStatus();

                }elseif( $template['status'] == 'failed' ){

                    //  Set the order status to "Failed Payment"
                    $this->owner->setStatusToFailedPayment();

                }

            }

        }

        return $transaction;
    }


    /*  recordActivity()
     *
     *  This method saves the activity of the invoice with a specified status
     *  as well as activity data and the authenticated user responsible 
     *  for the activity.
     */
    public function recordActivity($status, $data = null)
    {   
        return ( new \App\RecentActivity() )->saveActivity($this, $status, $data);
    }


















































    public function sampleTemplate()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        //  Invoice Sample Template
        return [
            'heading' => 'INVOICE',
            'reference_no_title' => 'Invoice Number',
            'created_date_title' => 'Invoice Date',
            'expiry_date_title' => 'Due Date',
            'sub_total_title' => 'Total',
            'grand_total_title' => 'Grand Total',
            'invoice_to_title' => 'INVOICE TO',
            'table_columns' => ['Services', 'Quantity', 'Unit Price', 'Amount'],
            'notes' => [
                'title' => 'Payment Information',
                'details' => '<p><b>Note that all payments must be made before the invoice expiry date since invoice prices are subject to change. Payments can be made in cash, cheque or via bank transfer'
            ],
            'colors' => ['#017BB8', '#EEF4FF'],
            'footer' => 'Terms & Conditions Apply',
        ];
    }

    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of invoice results.
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
         *  $modelId = 1, 2, 3, e.t.c
         *
         *  The $modelId variable only get data specifically related to
         *  the specified model id. It is useful for scenerios where we
         *  want only invoices of that custom model id. This works with
         *  only when the $modelType variable is also provided
         */
        $modelId = request('modelId');

        /*
         *  $modelType = 1, 2, 3, e.t.c
         *
         *  The $modelType variable only get data specifically related to
         *  the specified model type. It is useful for scenerios where we
         *  want only invoices of that custom model type. This works with
         *  only when the $modelId variable is also provided
         */
        $modelType = request('modelType');

        /*
         *  $storeId = 1, 2, 3, e.t.c
         *
         *  The $storeId variable only get data specifically related to
         *  the specified store id. It is useful for scenerios where we
         *  want only invoices of that store only
         */
        $orderId = request('orderId');

        /*
         *  $storeId = 1, 2, 3, e.t.c
        /*
         *  The $storeId variable only get data specifically related to
         *  the specified store id. It is useful for scenerios where we
         *  want only invoices of that store only
         */
        $orderId = request('orderId');

        /*
         *  $orderId = 1, 2, 3, e.t.c
        /*
         *  The $orderId variable only get data specifically related to
         *  the specified order id. It is useful for scenerios where we
         *  want only invoices of that order only
         */
        $storeId = request('storeId');

        /*
         *  $clientId = 1, 2, 3, e.t.c
        /*
         *  The $clientId variable only get data specifically related to
         *  the specified clientId id. It is useful for scenerios where we
         *  want only invoices with the client id set to the provided clientId
         */
        $clientId = request('clientId');

        /*
         *  $companyBranchId = 1, 2, 3, e.t.c
        /*
         *  The $companyBranchId variable only get data specifically related to
         *  the specified company branch id. It is useful for scenerios where we
         *  want only invoices of that branch only
         */
        $companyBranchId = request('companyBranchId');

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only invoices of that company only
         */
        $companyId = request('companyId');
        
        if( isset($modelId) && !empty($modelType) ){

            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED STORE INVOICES    *
            /********************************************************************/

            //  Create the dynamic model
            $dynamicModel = oq_generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Find the associated record by model id
                try {
                    $model = $dynamicModel::find($modelId);                    
                } catch (\Exception $e) {
                    //  Log the error
                    $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                    //  Return the error response
                    return ['success' => false, 'response' => $response];
                }
            } else {
                //  Model class does not exist - Log the error
                $response = oq_api_notify_error('Invalid Model Class - e.g) must be jobcard/order', null, 404);
            }

        }else if( isset($orderId) && !empty($orderId) ){

            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED STORE INVOICES    *
            /********************************************************************/

            $model = Order::find($orderId);

        }else if( isset($storeId) && !empty($storeId) ){

            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED STORE INVOICES    *
            /********************************************************************/

            $model = Store::find($storeId);

        }else if( isset($companyBranchId) && !empty($companyBranchId) ){

            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED BRANCH INVOICES   *
            /********************************************************************/

            $model = CompanyBranch::find($companyBranchId);

        }else if( isset($clientId) && !empty($clientId) ){

            /**********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY INVOICES    *
            /**********************************************************************/

            $model = Company::find($clientId);

        }else if( isset($companyId) && !empty($companyId) ){

            /**********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY INVOICES    *
            /**********************************************************************/

            $model = Company::find($companyId);

        }else{

            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL INVOICES         *
                /**********************************************************/

                //  Get the current invoice instance
                $model = $this;

            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH INVOICES    *
                /*************************************************************/

                // Only get invoices associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY INVOICES    *
                /**************************************************************/

                //  Only get invoices associated to the company
                $model = $auth_user->company;
            }

        }

        if(isset($clientId) && !empty( $clientId )){

            //  Invoices where the provided model is the client
            $invoices = $model->incomingInvoices();

        } else {
            //  Invoices where the model is not the client
            $invoices = $model->invoices();
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
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $invoices = $invoices->get();
                } else {
                    $invoices = $invoices->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                }

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
            if ($invoice) {
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

    /*  initiateCreate2() method:
     *
     *  This is used to create a new invoice. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the invoice.
     *
     */
    public function initiateCreate2($template = null)
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

        if(!$template){
            //  Get the auth user company settings to make the invoice
            $settings = $auth_user->company->settings['details'];
        }

        //  Create a template to hold the invoice details
        $template = $template ?? [

            'number' => $invoice['number'],
            'currency_type' => $invoice['currency_type'],
            'items' => $invoice['items'],
            'taxes' => $invoice['taxes'],
            'discounts' => $invoice['discounts'],
            'coupons' => $invoice['coupons'],
            'sub_total' => $invoice['sub_total'],
            'item_tax_total' => $invoice['item_tax_total'],
            'global_tax_total' => $invoice['global_tax_total'],
            'grand_tax_total' => $invoice['grand_tax_total'],
            'item_discount_total' => $invoice['item_discount_total'],
            'global_discount_total' => $invoice['global_discount_total'],
            'grand_discount_total' => $invoice['grand_discount_total'],
            'shipping_total' => $invoice['shipping_total'],
            'grand_total' => $invoice['grand_total'],
            'billing_info' => $invoice['billing_info'],
            'shipping_info' => $invoice['shipping_info'],
            'company_info' => $invoice['company_info'],
            'created_date' => $invoice['created_date'],
            'expiry_date' => $invoice['expiry_date'],
            'is_recurring' => $invoice['is_recurring'],
            'recurring_settings' => $invoice['recurring_settings'],
            'invoice_parent_id' => $invoice['invoice_parent_id'],
            'meta' => [
                'heading_title' => $settings['invoiceTemplate']['heading_title'],
                'reference_no_title' => $settings['invoiceTemplate']['reference_no_title'],                                             //  Autogenerated
                'created_date_title' => $settings['invoiceTemplate']['created_date_title'],
                'expiry_date_title' => $settings['invoiceTemplate']['expiry_date_title'],
                'sub_total_title' => $settings['invoiceTemplate']['sub_total_title'],
                'grand_total_title' => $settings['invoiceTemplate']['grand_total_title'],
                'recipient_title' => $settings['invoiceTemplate']['recipient_title'],
                'table_columns' => $settings['invoiceTemplate']['table_columns'],
                'notes' => $settings['invoiceTemplate']['notes'],
                'colors' => $settings['invoiceTemplate']['colors'],
                'footer_notes' => $settings['invoiceTemplate']['footer_notes']
            ]
        ];

        try {
            //  Create the invoice
            $invoice = $this->create($template)->fresh();

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

                // $auth_user->notify(new InvoiceCreated($invoice));

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

        if(!$template){
            //  Get the auth user company settings to make the invoice
            $settings = $auth_user->company->settings['details'];
        }

        //  Create a template to hold the invoice details
        $template = $template ?? [

            'number' => $invoice['number'],
            'currency_type' => $invoice['currency_type'],
            'items' => $invoice['items'],
            'taxes' => $invoice['taxes'],
            'discounts' => $invoice['discounts'],
            'coupons' => $invoice['coupons'],
            'sub_total' => $invoice['sub_total'],
            'item_tax_total' => $invoice['item_tax_total'],
            'global_tax_total' => $invoice['global_tax_total'],
            'grand_tax_total' => $invoice['grand_tax_total'],
            'item_discount_total' => $invoice['item_discount_total'],
            'global_discount_total' => $invoice['global_discount_total'],
            'grand_discount_total' => $invoice['grand_discount_total'],
            'shipping_total' => $invoice['shipping_total'],
            'grand_total' => $invoice['grand_total'],
            'billing_info' => $invoice['billing_info'],
            'shipping_info' => $invoice['shipping_info'],
            'company_info' => $invoice['company_info'],
            'created_date' => $invoice['created_date'],
            'expiry_date' => $invoice['expiry_date'],
            'is_recurring' => $invoice['is_recurring'],
            'recurring_settings' => $invoice['recurring_settings'],
            'invoice_parent_id' => $invoice['invoice_parent_id'],
            'meta' => [
                'heading_title' => $settings['invoiceTemplate']['heading_title'] ?? null,
                'reference_no_title' => $settings['invoiceTemplate']['reference_no_title'] ?? null,                                             //  Autogenerated
                'created_date_title' => $settings['invoiceTemplate']['created_date_title'] ?? null,
                'expiry_date_title' => $settings['invoiceTemplate']['expiry_date_title'] ?? null,
                'sub_total_title' => $settings['invoiceTemplate']['sub_total_title'] ?? null,
                'grand_total_title' => $settings['invoiceTemplate']['grand_total_title'] ?? null,
                'recipient_title' => $settings['invoiceTemplate']['recipient_title'] ?? null,
                'table_columns' => $settings['invoiceTemplate']['table_columns'] ?? null,
                'notes' => $settings['invoiceTemplate']['notes'] ?? null,
                'colors' => $settings['invoiceTemplate']['colors'] ?? null,
                'footer_notes' => $settings['invoiceTemplate']['footer_notes'] ?? null,
            ]
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

                // $auth_user->notify(new InvoiceUpdated($invoice));

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

                // $auth_user->notify(new InvoiceApproved($invoice));

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
    public function initiateSendInvoice($invoice_id)
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
                /***********************************
                 *   SEND INVOICE VIA EMAIL/SMS    *
                 ***********************************/

                //  Accepted Delivery Methods
                $deliveryMethods = request('deliveryMethods');

                //  If specified to send invoice via sms
                if (in_array('Sms', $deliveryMethods)) {
                    //  Send via sms
                    $this->sendInvoiceAsSMS($invoice);
                }

                //  If specified to send invoice via mail
                if (in_array('Email', $deliveryMethods)) {
                    //  send via email
                    $this->sendInvoiceAsMail($invoice);
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

    /*  initiateSendReceipt() method:
     *
     *  This is used to send an existing invoice receipt (via email).
     *  It also works to store the sent receipt activity and broadcasting
     *  of notifications to users concerning the sending of the invoice.
     *
     */
    public function initiateSendInvoiceReceipt($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***************************************************************
         *   CHECK IF USER HAS PERMISSION TO SEND INVOICE RECEIPT      *
         ***************************************************************/

        try {
            //  Get the invoice receipt
            $invoice = $this->where('id', $invoice_id)->first();

            //  Check if we have an invoice receipt
            if ($invoice) {
                /***********************************
                 *   SEND INVOICE VIA EMAIL/SMS    *
                 ***********************************/

                //  Accepted Delivery Methods
                $deliveryMethods = request('deliveryMethods');

                //  If specified to send invoice receipt via sms

                if (in_array('Sms', $deliveryMethods)) {
                    //  Send via sms
                    $this->sendInvoiceReceiptAsSMS($invoice);
                }

                //  If specified to send invoice receipt via mail
                if (in_array('Email', $deliveryMethods)) {
                    //  send via email receipt
                    $this->sendInvoiceReceiptAsMail($invoice);
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

    /*  sendInvoiceAsSMS() method:
     *
     *  This is used to send the invoice via SMS only.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every sms sent will be
     *  recorded as a recent activity with the phone and sms saved.
     *
     */
    public function sendInvoiceAsSMS($invoice, $phones = null, $smsMessage = null, $user = null, $sampleSms = false)
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /***************************
         *   GET SMS DETAILS       *
         ***************************/
        $phones = $phones ?? request('sms')['phones'];
        $smsMessage = $this->getCompiledSmsMessage($invoice);

        //  Filter and only get the phones set to active

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        if (request('test') == 1) {
            $status = 'sent test sms';
        // $auth_user->notify(new InvoiceTestSmsSent($invoice));
        } else {
            $status = 'sent sms';
            // $auth_user->notify(new InvoiceSmsSent($invoice));
        }

        //  If we have phones and a message to send
        if (!empty($phones) && !empty($smsMessage)) {
            //  Foreach phone number provided
            foreach ($phones as $phone) {
                //  If $phone['show'] = true it is an active phone number or is the show index is not set
                //  If $phone['show'] = false it is not an active phone number
                //  We only send messages to phone numbers set to active
                if (!isset($phone['show']) || $phone['show'] == true) {
                    //  Get the calling code
                    $callingCode = '+'.$phone['calling_code'];

                    //  Get the phone number
                    $phoneNumber = $phone['number'];

                    //  Send the sms message to the given number
                    Twilio::message($callingCode.$phoneNumber, $smsMessage);

                    /*****************************
                     *   RECORD ACTIVITY         *
                     *****************************/

                    //  Structure mail template
                    $sms = ['phone' => $phone, 'message' => $smsMessage];

                    if(!$sampleSms){
                        //  Record activity of invoice sent receipt
                        $invoiceSentActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize(), 'sms' => $sms]);
                    }
                }
            }

            return ['success' => true, 'response' => ['phones'=>$phones, 'sms_message'=> $smsMessage]];
        }
    }



    /*  sendInvoiceAsMail() method:
     *
     *  This is used to send the invoice via Email only. It takes the
     *  actual invoice to build a pdf to send to the receipient. It
     *  will also add any CC or BCC within the mail if provided.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every email sent will be
     *  recorded as a recent activity with the mail details saved.
     *
     */

    public function sendInvoiceAsMail($invoice, $primaryEmails = null, $ccEmails = null, $bccEmails = null, $subject = null, $message = null, $user = null)
    {
        //  Provided User Or Current authenticated user
        $auth_user = $user ?? auth('api')->user();

        /*****************************
         *   GET EMAIL DETAILS       *
         *****************************/

        $primaryEmails = $primaryEmails ?? request('mail')['primaryEmails'];
        $ccEmails = $ccEmails ?? request('mail')['ccEmails'];
        $bccEmails = $bccEmails ?? request('mail')['bccEmails'];
        $subject = $subject ?? request('mail')['subject'];
        $message = $message ?? request('mail')['message'];

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        //  If this is a test email
        if (request('test') == 1) {
            $status = 'sent test email';
        // $auth_user->notify(new InvoiceTestEmailSent($invoice));

        //  Otherwise if this is not a test email
        } else {
            $status = 'sent email';
            // $auth_user->notify(new InvoiceEmailSent($invoice));
        }

        //  Invoice PDF
        $invoicePDF = $this->getInvoiceAsPDF($invoice);

        /***********************************************
         *   REPLACE SHORTCODES WITH ACTUAL CONTENT    *
         ***********************************************/

        $message = $this->replaceShortcodes($invoice, $message);
        $subject = $this->replaceShortcodes($invoice, $subject);

        //  Foreach email
        foreach ($primaryEmails as $primaryEmail) {
            /******************************
             *   SEND INVOICE VIA MAIL    *
             ******************************/
            $pdf = $invoicePDF[0];
            $pdf_name = $invoicePDF[1];
            Mail::to($primaryEmail)->send(new InvoiceMail($subject, $message, $pdf, $pdf_name));

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $mail = ['email' => $primaryEmail, 'subject' => $subject, 'message' => $message];

            //  Record activity of invoice sent receipt
            $invoiceSentActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize(), 'mail' => $mail]);
        
            //  Action was executed successfully
            return ['success' => true, 'response' => $mail];
        }
    }

    public function getInvoiceAsPDF($invoice){

        return PDF::loadView('pdf.invoice', array('invoice' => $invoice));
        
    }

    public function getInvoiceReceiptAsPDF($invoice){

        return PDF::loadView('emails.send_invoice_receipt', array('invoice' => $invoice, 'msg' => null));

    }

    /*  sendInvoiceReceiptAsSMS() method:
     *
     *  This is used to send the invoice receipt via SMS only.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every sms sent will be
     *  recorded as a recent activity with the phone and sms saved.
     *
     */
    public function sendInvoiceReceiptAsSMS($invoice)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /***************************
         *   GET SMS DETAILS       *
         ***************************/
        $phones = request('sms')['phones'];
        $smsMessage = request('sms')['message'];

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        if (request('test') == 1) {
            $status = 'sent receipt test sms';
        // $auth_user->notify(new InvoiceReceiptTestSmsSent($invoice));
        } else {
            $status = 'sent receipt sms';
            // $auth_user->notify(new InvoiceReceiptSmsSent($invoice));
        }

        //  Foreach phone number provided
        foreach ($phones as $phone) {
            //  Get the calling code
            $callingCode = '+'.$phone['calling_code'];

            //  Get the phone number
            $phoneNumber = $phone['number'];

            //  Send the sms message to the given number
            Twilio::message($callingCode.$phoneNumber, $smsMessage);

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $sms = ['phone' => $phone, 'message' => $smsMessage];

            //  Record activity of invoice sent receipt
            $INVOICESentActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize(), 'sms' => $sms]);
        }
    }

    /*  sendInvoiceReceiptAsMail() method:
     *
     *  This is used to send the invoice receipt via Email only. It
     *  takes the actual invoice to build a pdf to send to the receipient.
     *  It will also add any CC or BCC within the mail if provided.
     *  Notifications will also be used to alert any user that
     *  needs to be notified on the event. Every email sent will be
     *  recorded as a recent activity with the mail details saved.
     *
     */
    public function sendInvoiceReceiptAsMail($invoice)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*****************************
         *   SEND NOTIFICATIONS      *
         *****************************/

        //  If this is a test email
        if (request('test') == 1) {
            $status = 'sent receipt test email';
        // $auth_user->notify(new InvoiceReceiptTestEmailSent($invoice));

        //  Otherwise if this is not a test email
        } else {
            $status = 'sent receipt email';
            // $auth_user->notify(new InvoiceReceiptEmailSent($invoice));
        }

        /*****************************
         *   GET EMAIL DETAILS       *
         *****************************/

        $primaryEmails = request('mail')['primaryEmails'];
        $ccEmails = request('mail')['ccEmails'];
        $bccEmails = request('mail')['bccEmails'];
        $subject = request('mail')['subject'];
        $message = request('mail')['message'];

        //  Get Receipt PDF
        $receiptPDF = $this->getInvoiceReceiptAsPDF($invoice);

        /***********************************************
         *   REPLACE SHORTCODES WITH ACTUAL CONTENT    *
         ***********************************************/

        $message = $this->replaceShortcodes($invoice, $message);
        $subject = $this->replaceShortcodes($invoice, $subject);

        //  Foreach email
        foreach ($primaryEmails as $primaryEmail) {
            /******************************
             *   SEND INVOICE VIA MAIL    *
             ******************************/

            Mail::to($primaryEmail)->send(new InvoiceReceiptMail($subject, $message, $invoice, $receiptPDF));

            /*****************************
             *   RECORD ACTIVITY         *
             *****************************/

            //  Structure mail template
            $mail = ['email' => $primaryEmail, 'subject' => $subject, 'message' => $message];

            //  Record activity of invoice sent receipt
            $invoiceSentActivity = oq_saveActivity($invoice, $auth_user, $status, ['invoice' => $invoice->summarize(), 'mail' => $mail]);
        }
    }

    /*  replaceShortcodes() method:
     *
     *  This is used to replace all shortcodes within a given message
     *  The method goes and checks for any shortcode that can be converted
     *  to actual information used in the invoice. E.g it would replace
     *  the shortcode [grand_total] with the actual grand total amount
     *  of the invoice.
     *
     */
    public function replaceShortcodes($invoice, $data)
    {
        $client = $invoice->customized_customer_details;
        $company = $invoice->customized_company_details;
        $currency = $invoice->currency_type['currency']['symbol'] ?? '';
        $sub_total = $currency.number_format($invoice->sub_total_value, 2, ',', '.');
        $grand_total = $currency.number_format($invoice->grand_total, 2, ',', '.');

        //  Custom Invoice Variables - Shortcodes
        $customFields = [
            '[invoice_heading]' => $invoice->heading,
            '[invoice_reference_no]' => '#'.$invoice->reference_no_value,
            '[created_date]' => (new Carbon($invoice->created_date))->format('M d Y'),
            '[expiry_date]' => (new Carbon($invoice->expiry_date))->format('M d Y'),
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
                $status = 'skipped sending';
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

    /*  initiateUpdateRecurringSettingsSchedulePlan() method:
     *
     *  This is used to update the schedule plan of an existing invoice.
     *  The schedule plan is the (date, time and frequency) of how the invoice
     *  are sent to receipients over a time period. It also works to store the update
     *  activity and broadcasting of notifications to users concerning the updating of
     *  the invoice schedule.
     *
     */
    public function initiateUpdateRecurringSettingsSchedulePlan($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         **************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         *********************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            $settingsData['editing']['schedulePlan'] = false;

            //  Mark the next stage with a status of editting
            if (!$invoice->has_set_recurring_payment_plan) {
                $settingsData['editing']['paymentPlan'] = true;
            }

            //  Create a template to hold the setting details
            $template = [
                'is_recurring' => 1,
                'recurring_settings' => $settingsData,
            ];

            //  Update the invoice
            $invoice = $invoice->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new InvoiceRecurringSettingsSchedulePlanUpdated($invoice));

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

    public function initiateUpdateRecurringSettingsPaymentPlan($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         **************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         *********************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            $settingsData['editing']['schedulePlan'] = false;
            $settingsData['editing']['paymentPlan'] = false;

            //  Mark the next stage with a status of editting
            if (!$invoice->has_set_recurring_payment_plan) {
                $settingsData['editing']['deliveryPlan'] = true;
            }

            //  Create a template to hold the setting details
            $template = [
                'is_recurring' => 1,
                'recurring_settings' => $settingsData,
            ];

            //  Update the invoice
            $invoice = $invoice->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new InvoiceRecurringSettingsPaymentPlanUpdated($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring payment plan updated
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

    public function initiateUpdateRecurringSettingsDeliveryPlan($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         **************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         *********************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            $settingsData['editing']['schedulePlan'] = false;
            $settingsData['editing']['paymentPlan'] = false;
            $settingsData['editing']['deliveryPlan'] = false;

            //  Create a template to hold the setting details
            $template = [
                'is_recurring' => 1,
                'recurring_settings' => $settingsData,
            ];

            //  Update the invoice
            $invoice = $invoice->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new InvoiceRecurringSettingsDeliveryPlanUpdated($invoice));

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

    public function initiateApproveRecurringSettings($invoice_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $settings is a collection of the recurring settings to be stored.
         */
        $settingsData = request('settings');

        /**************************************************************************
         *   CHECK IF USER HAS PERMISSION TO SAVE RECURRING INVOICE SCHEDULES     *
         **************************************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         *********************************************/

        try {
            //  Get the invoice
            $invoice = $this->where('id', $invoice_id)->first();

            $settingsData['editing']['schedulePlan'] = false;
            $settingsData['editing']['paymentPlan'] = false;
            $settingsData['editing']['deliveryPlan'] = false;

            //  Create a template to hold the setting details
            $template = [
                'is_recurring' => 1,
                'recurring_settings' => $settingsData,
            ];

            //  Update the invoice
            $invoice = $invoice->update($template);

            //  If the invoice was updated successfully
            if ($invoice) {
                //  re-retrieve the instance to get all of the fields in the table.
                $invoice = $this->where('id', $invoice_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new InvoiceRecurringSettingsApproved($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of recurring delivery plan updated
                $status = 'approved recurring settings';
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

                // $auth_user->notify(new InvoicePaid($invoice));

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

                // $auth_user->notify(new InvoicePaymentCancelled($invoice));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of invoice cancelled payment
                $status = 'cancelled payment';
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
                $status = 'updated payment reminders';
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

        //  Start getting the companies
        $data = $this->initiateGetAll(['paginate' => false]);
        $success = $data['success'];
        $response = $data['response'];

        if ($success) {
            try {
                //  Get all the available invoices so far
                $invoices = $data['response'];

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
                        'grand_total' => collect($invoiceGroup)->sum('grand_total'),  //  35020
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
        } else {
            return ['success' => false, 'response' => $response];
        }
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific
     *  columns that can then be used for storage. We may only want to summarize
     *  the data to very important information, rather than storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want (we access the fillable columns of the model), while also
     *  removing any custom attributes we do not want to store
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
