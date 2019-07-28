<?php

namespace App\Traits;

use DB;
use App\Store;
use App\MyCart;
use App\Company;
use App\CompanyBranch;
use App\Invoice;
use App\Lifecycle;
//  Mails
use Mail;
use App\Mail\OrderMail;
//  Notifications
use App\Notifications\OrderCreated;
use App\Notifications\OrderUpdated;
use Illuminate\Support\Facades\Hash;
//  Other
use PDF;
use Carbon\Carbon;

trait OrderTraits
{
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
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE ORDER      *
         ******************************************************/

        /*********************************************
         *   VALIDATE ORDER INFORMATION              *
         ********************************************/

        $store = Store::find(request('store_id'));

        if (!$store) {
            //  Store does not exist
            return ['success' => false, 'response' => oq_api_notify_error(null, ['general' => ['The store does not exist']], 404)];
        }

        //  MyCart Instance
        $cartInstance = ( new MyCart() );
        $cartRequest = $cartInstance->initiateGetCartDetails();

        if( $cartRequest['success'] && count($cartRequest['response']) ){
            $cartDetails = $cartRequest['response'];
        }else{
            //  Cart does not exist
            return ['success' => false, 'response' => $cartRequest];
        }

        //  Create a template to hold the order details
        $template = $template ?? [
            //  General details
            'parent_id' => request('parent_id') ?? null,
            'number' => request('number') ?? null,
            'order_key' => request('order_key') ?? null,
            'status' => request('status') ?? null,
            'currency_type' => request('currency_type') ?? null,
            //'cart_hash' => Hash::make(request('line_items')) ?? null,
            'meta_data' => request('meta_data') ?? null,
            'date_completed' => request('date_completed') ?? null,

            //  Item Info
            'line_items' => $cartDetails['cart_items'] ?? (request('line_items') ?? null),

            //  Shipping Info
            'shipping_lines' => request('shipping_lines') ?? null,

            //  Grand Total, Subtotal, Shipping Total, Discount Total
            'cart_total' => $cartDetails['sub_total'] ?? (request('cart_total') ?? 0),
            'shipping_total' => request('shipping_total') ?? 0,
            'discount_total' => request('discount_total') ?? 0,
            'grand_total' => $cartDetails['grand_total'] ?? (request('grand_total') ?? 0),

            //  Tax Info
            'cart_tax' => $cartDetails['cart_tax_total'] ?? (request('cart_tax') ?? 0),
            'shipping_tax' => request('shipping_tax') ?? 0,
            'discount_tax' => request('discount_tax') ?? 0,
            'grand_total_tax' => request('grand_total_tax') ?? 0,
            'prices_include_tax' => request('prices_include_tax') ?? null,
            'tax_lines' => request('tax_lines') ?? null,

            //  Customer Info
            'client_id' => request('client_id') ?? null,
            'client_type' => request('client_type') ?? null,
            'customer_ip_address' => request('customer_ip_address') ?? null,
            'customer_user_agent' => request('customer_user_agent') ?? null,
            'customer_note' => request('customer_note') ?? null,
            'billing_info' => request('billing_info') ?? null,
            'shipping_info' => request('shipping_info') ?? null,

            //  Payment Info
            'payment_method' => request('payment_method') ?? null,
            'payment_method_title' => request('payment_method_title') ?? null,
            'transaction_id' => request('transaction_id') ?? null,
            'date_paid' => request('date_paid') ?? null,

            //  Store, Company & Branch Info
            'store_id' => request('store_id') ?? null,
            'company_branch_id' => $store->company_branch_id ?? null,
            'company_id' => $store->company_id ?? null,
        ];

        try {
            //  Create the order
            $order = $this->create($template)->fresh();

            //  If the order was created successfully
            if ($order) {

                //  Check and generate the order lifecycle
                $lifecyle = $this->checkAndCreateLifecycle($order);

                //  Check and generate the order invoice
                $invoiceRequest = $this->checkAndCreateOrderInvoice($store, $order);

                //  If the invoice was created successfully
                if($invoiceRequest && $invoiceRequest['success']){

                    //  Send the order invoice
                    $sentOrderMail = $this->checkAndSendOrderInvoice($invoiceRequest['response'], $order, $store);
                    return $sentOrderMail;
                    
                }

                //  refetch the updated order
                $order = $order->fresh();

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

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing order. It also works
     *  to order the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the order.
     *
     */
    public function initiateUpdate($order_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE ORDER    *
         ******************************************************/

        /*********************************************
         *   VALIDATE ORDER INFORMATION            *
         ********************************************/

        //  Create a template to hold the order details
        $template = $template ?? [
            //  General details
            'title' => request('title'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,

            //  Pricing details
            'cost_per_item' => request('cost_per_item') ?? 0,
            'unit_price' => request('unit_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'quantity' => request('quantity') ?? null,
            'allow_inventory' => request('allow_inventory'),
            'auto_track_inventory' => request('auto_track_inventory'),

            //  Variant details
            'variants' => request('variants') ?? null,
            'variant_attributes' => request('variant_attributes') ?? null,
            'allow_variants' => request('allow_variants'),

            //  Download Details
            'allow_downloads' => request('allow_downloads'),

            //  Order Details
            'show_on_order' => request('show_on_order'),

            //  Ownership Details
            'company_branch_id' => $auth_user->company_branch_id ?? null,
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Update the order
            $order = $this->where('id', $order_id)->first()->update($template);

            //  If the order was updated successfully
            if ($order) {
                //  re-retrieve the instance to get all of the fields in the table.
                $order = $this->where('id', $order_id)->first();

                //  refetch the updated order
                $order = $order->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new OrderUpdated($order));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of order updated
                $status = 'updated';
                $orderUpdatedActivity = oq_saveActivity($order, $auth_user, $status, ['order' => $order->summarize()]);

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

    public function checkAndCreateLifecycle($order)
    {
        
        $auth_user = auth('api')->user();

        /*  $defaultLifecycle:
         *  The default lifecycle represents the lifecycle process followed
         *  by orders of a particular company.
         */
        $defaultLifecycle = Lifecycle::where('type', 'order')->where('company_id', $order->company_id)->first();

        if (!empty($defaultLifecycle)) {

            //  Delete any previous lifecycles to the order
            DB::table('lifecycle_allocations')
                ->where('trackable_id', $defaultLifecycle->id)
                ->where('trackable_type', 'order')
                ->delete();

            //  Add the default lifecycle to the order
            DB::table('lifecycle_allocations')->insert([
                'lifecycle_id' => $defaultLifecycle->id, 
                'trackable_id' => $order->id,                       
                'trackable_type' => 'order',                       
                'created_at' => DB::raw('now()'),                       
                'updated_at' => DB::raw('now()')
            ]);

        }
    }

    public function checkAndCreateOrderInvoice($store, $order)
    {
        $auth_user = auth('api')->user();

        //  Check if the store has any settings
        $settings = Store::find($store->id)->settings;

        if (!$settings) {
            //  Create new settings for the store
            $settings = ( new Store )->checkAndCreateSettings($store);
        }

        $hasInvoices = $order->invoices->count();

        //  If we don't have an invoice create one
        if ( !$hasInvoices ) {
            
            //  Get the settings details
            $settings = $settings['details'];

            //  Get the current date and time
            $nowDateTime = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());

            //  Create the invoice template
            $template = [
                'heading' => $settings['invoiceTemplate']['heading'],
                'reference_no_title' => $settings['invoiceTemplate']['reference_no_title'],
                'reference_no_value' => null,                                                   //  Autogenerated
                'created_date_title' => $settings['invoiceTemplate']['created_date_title'],
                'created_date_value' => $nowDateTime,  
                'expiry_date_title' => $settings['invoiceTemplate']['expiry_date_title'],
                'expiry_date_value' => $nowDateTime->addDays( $settings['invoiceTemplate']['expire_after_no_of_days'] ),
                'sub_total_title' => $settings['invoiceTemplate']['sub_total_title'],
                'sub_total_value' => $order->cart_total,
                'grand_total_title' => $settings['invoiceTemplate']['grand_total_title'],
                'grand_total_value' => $order->grand_total,
                'currency_type' => $settings['general']['currency_type'],
                //'calculated_taxes' => $order->tax_lines,
                'invoice_to_title' => $settings['invoiceTemplate']['invoice_to_title'],
                'customized_company_details' => $order->company->getBasicDetails() ?? null,
                'customized_client_details' => $order->client->getBasicDetails() ?? null,
                'client_id' => $order->client_id,
                'client_type' => $order->client_type,
                'table_columns' => $settings['invoiceTemplate']['table_columns'],
                'items' => $order['line_items'],
                'notes' => $settings['invoiceTemplate']['notes'],
                'colors' => $settings['invoiceTemplate']['colors'],
                'footer' => $settings['invoiceTemplate']['footer'],
                'company_branch_id' => $order->company_branch_id,
                'company_id' => $order->company_id,
            ];

            //  Create a new invoice
            return ( new Invoice() )->initiateCreate($template);
            
        }

        return false;
    }

    public function checkAndSendOrderInvoice($invoice, $order, $store){

        $auth_user = auth('api')->user();
        $deliveryMethods = request('deliveryMethods');

        /******************************
         * Send invoice via Email/Sms *
         ******************************/

        //  If specified to send invoice via mail
        if ( isset($deliveryMethods) && !empty($deliveryMethods) ) {
            
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
                                'subject' => $subject,  'message' => $message
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
            'primaryEmails' => null, 'ccEmails' => null, 'bccEmails' => null, 'subject' => null,  'message' => null
        );

        //  Replace defaults with any provided options
        $mailDetails = array_merge($defaultMailDetails, $mailDetails);

        //  Default settings
        $defaultMailConfig = array( 'attach_order_pdf' => true, 'attach_invoice_pdf' => true, 'attach_bank_details_pdf' => true);

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
            $invoicePDF = ( new Invoice )->getInvoiceAsPDF($invoice);

            //  Store Bank Account PDF
            $bankDetailsPDF = ( new Store )->getStoreBankAccountDetailsAsPDF($order->store);

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
    
    public function getOrderAsPDF($order){

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
        $client = $order->customized_client_details;
        $company = $order->customized_company_details;
        $currency = $order->currency_type['currency']['symbol'] ?? '';
        $sub_total = $currency.number_format($order->sub_total_value, 2, ',', '.');
        $grand_total = $currency.number_format($order->grand_total_value, 2, ',', '.');

        //  Custom Order Variables - Shortcodes
        $customFields = [
            '[order_heading]' => $order->heading,
            '[order_reference_no]' => '#'.$order->reference_no_value,
            '[created_date]' => (new Carbon($order->created_date_value))->format('M d Y'),
            '[expiry_date]' => (new Carbon($order->expiry_date_value))->format('M d Y'),
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
