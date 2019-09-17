<?php

namespace App\Traits;

use DB;
use App\Store;
use App\MyCart;
use App\Company;
use App\CompanyBranch;
use App\RecentActivity;
use App\Invoice;
use App\Lifecycle;
//  Mails
use Mail;
use App\Mail\OrderMail;
//  Notifications
use App\Notifications\OrderCreated;
use App\Notifications\OrderUpdated;
//  Resources
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Orders as OrdersResource;
//  Other
use PDF;
use Carbon\Carbon;

trait OrderTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($orders = null)
    {

        try {

            if( $orders ){

                //  Transform the orders
                return new OrdersResource($orders);

            }else{

                //  Transform the order
                return new OrderResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
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

                //  Check and generate the order lifecycle
                $lifecyle = $this->checkAndCreateLifecycle($order, $store);

                //  Set the lifecycle to pending payment status
                $pendingPaymentActivity = $this->setLifecycleToPendingPaymentStatus($order);

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
        
    }

    public function checkAndCreateLifecycle($order, $store)
    {

        /*  $defaultLifecycle:
         *  The default lifecycle represents the lifecycle process followed
         *  by orders of a particular company.
         */
        $defaultLifecycle = $store->availableLifecycles->where('type', 'order')->where('default', 1)->first();

        if (empty($defaultLifecycle)) {
            //  Add the default lifecycle to the order
            DB::table('lifecycle_allocations')->insert([
                'lifecycle_id' => $defaultLifecycle->id,
                'trackable_id' => $order->id,
                'trackable_type' => 'order',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ]);
        }
    }

    public function setLifecycleToPendingPaymentStatus($order)
    {
        //  Set the current lifecycle to pending payment
        return (new Lifecycle())->setToPendingPayment($order->id, 'order');
    }

    public function setLifecycleToVerifyPaymentStatus()
    {
        try {
            $payment_amount = request('payment_amount') ?? null;
            $payment_method = request('payment_method') ?? null;

            $orderPendingPaymentActivity = $this->current_lifecycle_stage;
            $orderPendingPaymentActivityArray = collect($orderPendingPaymentActivity)->toArray();

            //  If the current lifecycle activity is set to pending payment then we can update the lifecycle
            if ($orderPendingPaymentActivityArray['activity']['type'] == 'payment' && $orderPendingPaymentActivityArray['activity']['pending_status'] == 'true') {
                //  Set the id to null to be autogenerated
                $orderPendingPaymentActivityArray['id'] = null;

                //  Set the created by to the users id instead of the whole user data
                $orderPendingPaymentActivityArray['created_by'] = $orderPendingPaymentActivityArray['created_by']['id'] ?? null;

                //  Set pending payment to false to turn it off
                $orderPendingPaymentActivityArray['activity']['pending_status'] = false;
                //  Set manual verification to true to turn it on
                $orderPendingPaymentActivityArray['activity']['manual_verification_status'] = true;

                //  Update the payment amount if needed, otherwise use the old value
                $orderPendingPaymentActivityArray['activity']['meta']['payment_amount'] = $payment_amount ?? $orderPendingPaymentActivityArray['activity']['meta']['payment_amount'];
                //  Update the payment method if needed, otherwise use the old value
                $orderPendingPaymentActivityArray['activity']['meta']['payment_method'] = $payment_method ?? $orderPendingPaymentActivityArray['activity']['meta']['payment_method'];

                //  Keep only important fields and remove unnecessary fields from the data set
                $importantFields = (new RecentActivity())->getFillable();

                foreach ($orderPendingPaymentActivityArray as $key => $value) {
                    //  If this is not an important field
                    if (!in_array($key, $importantFields)) {
                        //  Remove the field
                        unset($orderPendingPaymentActivityArray[$key]);
                    }
                }

                //  Create a new activity with the modified fields and values
                $verifyPaymentActivity = new RecentActivity($orderPendingPaymentActivityArray);

                $savedActivity = $this->recentActivities()->save($verifyPaymentActivity);

                //  refetch the order
                $order = $this->fresh();

                //  Action was executed successfully
                return ['success' => true, 'response' => $order];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }

        return ['success' => false, 'response' => 'Proof of payment was not updated. The current lifecycle is not pending payment'];
    }

    public function convertToInvoice($order, $store)
    {
        $auth_user = auth('api')->user();

        //  Check if the store has any settings
        $storeSettings = $store->settings['details'];

        $hasInvoices = $order->invoices->count();

        //  If we don't have an invoice create one
        if (!$hasInvoices) {

            //  Get the current date and time
            $orderCreatedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', ($order->created_date ?? $order->created_at));

            //  Create the invoice template
            $template = [
                'number' => $order['number'],
                'currency_type' => $order['currency_type'],
                'items' => $order['items'],
                'taxes' => $order['taxes'],
                'discounts' => $order['discounts'],
                'coupons' => $order['coupons'],
                'sub_total' => $order['sub_total'],
                'item_tax_total' => $order['item_tax_total'],
                'global_tax_total' => $order['global_tax_total'],
                'grand_tax_total' => $order['grand_tax_total'],
                'item_discount_total' => $order['item_discount_total'],
                'global_discount_total' => $order['global_discount_total'],
                'grand_discount_total' => $order['grand_discount_total'],
                'shipping_total' => $order['shipping_total'],
                'grand_total' => $order['grand_total'],
                'billing_info' => $order['billing_info'],
                'shipping_info' => $order['shipping_info'],
                'company_info' => $order['company_info'],
                'created_date' => $orderCreatedDateTime,
                'expiry_date' => $orderCreatedDateTime->addDays($storeSettings['invoiceTemplate']['expire_after_no_of_days']),
                'isRecurring' => 0,
                'recurring_settings' => null,
                'invoice_parent_id' => null,
                'invoiceable_id' => $order['id'],
                'invoiceable_type' => 'order',
                'meta' => [
                    'heading_title' => $storeSettings['invoiceTemplate']['heading_title'],
                    'reference_no_title' => $storeSettings['invoiceTemplate']['reference_no_title'],                                             //  Autogenerated
                    'created_date_title' => $storeSettings['invoiceTemplate']['created_date_title'],
                    'expiry_date_title' => $storeSettings['invoiceTemplate']['expiry_date_title'],
                    'sub_total_title' => $storeSettings['invoiceTemplate']['sub_total_title'],
                    'grand_total_title' => $storeSettings['invoiceTemplate']['grand_total_title'],
                    'recipient_title' => $storeSettings['invoiceTemplate']['recipient_title'],
                    'table_columns' => $storeSettings['invoiceTemplate']['table_columns'],
                    'notes' => $storeSettings['invoiceTemplate']['notes'],
                    'colors' => $storeSettings['invoiceTemplate']['colors'],
                    'footer_notes' => $storeSettings['invoiceTemplate']['footer_notes']
                ]
            ];

            //  Create a new invoice
            return ( new Invoice() )->initiateCreate($template);
        }

        return false;
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
        $client = $order->customized_client_details;
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
