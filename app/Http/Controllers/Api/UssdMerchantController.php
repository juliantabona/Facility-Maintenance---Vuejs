<?php

namespace App\Http\Controllers\Api;

use App\UssdInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

class UssdMerchantController extends Controller
{
    private $user;
    private $text;
    private $cart;
    private $store;
    private $visit;
    private $stores;
    private $orders;
    private $offset;
    private $products;
    private $currency;
    private $ussd_products;
    private $ussd_interface;
    private $maximum_cart_items;
    private $maximum_item_quantity;

    public function __construct(Request $request)
    {
        /*  Get the Authenticated User (If available)
         *  Otherwise create a User Instance for Guest Users
         */
        $this->user = auth('api')->user() ?? (new \App\User());

        /*  Get the USSD TEXT value (User Response)  */
        $this->text = $request->get('TEXT');

        /*  Get the USSD MSISDN value (User Phone)  */
        $this->msisdn = $request->get('MSISDN');

        $this->user['phone'] = [
            'calling_code' => substr($this->msisdn, 0, 3),
            'number' => substr($this->msisdn, 3, 8),
        ];

        /*  Defines the maximum number of items  */
        $this->maximum_cart_items = 5;
        $this->maximum_item_quantity = 5;
    }

    /*********************************
     *  USSD DISPLAY MENU            *
    *********************************/

    /*  merchantHome()
     *  This is the first method we hit where all the USSD processes are
     *  sequencially handled as the user makes requests and receices
     *  responses.
     */
    public function merchantHome(Request $request)
    {
        /*  If we could not get the user's phone number  */
        if (!$this->hasProvidedPhoneDetails()) {

            return $this->displayCustomErrorPage('Sorry, please provide you mobile number.');

        }

        /*  If the user has not responded to the landing page  */
        if (!$this->hasResponded()) {

            /*  Display the landing page (The first page of the USSD)
             *  This is the page where the user must provide their
             *  email or mobile number matching their account
             */
            $response = $this->displayLandingPage();

        /*  If the user has already responded to the landing page  */
        } else {

            /*  Check if the email/mobile number provided matches any existing account  */
            if ($this->isValidUserAccount()) {

                 /*  Check if the user provided their account password  */
                if($this->hasProvidedLoginPassword()){

                    /*  If the account matching the provided email/mobile number and password exists  */
                    if( $this->isValidLoginDetails() ){

                        //  Make the the user's USSD stores available from here on
                        $this->stores = $this->user->ussdStores()->get();

                        /*  Check if the user has selected a store to visit  */
                        if($this->hasSelectedStoreToVisit()){

                            /*  Allow the user to visit the store (At the store specified)  */
                            $response = $this->visitStore();

                        }else{

                            $response = $this->displayStoreSeletionPage();

                        }

                    }else{

                        return $this->displayCustomErrorPage('Sorry, incorrect login details.');

                    }

                }else{

                    $response = $this->displayPasswordPage();

                }

            /*  If no store using the provided ussd code exists  */
            } else {

                return $this->displayCustomErrorPage('Sorry, account using the email/mobile number provided does not exist. Try again');

            }

        }

        /*  Return the response to the user  */
        return response($response."\n\n".'characters: '.strlen($response))->header('Content-Type', 'text/plain');
        //return response($response)->header('Content-Type', 'application/json');
    }

    /*  hasResponded()
     *  Returns true/false of whether the user has responded before
     *  The user must atleast have responded once for this to be true
     */
    public function hasResponded()
    {
        /*  Check if the user has responded. If the text returned
         *  is not empty then the user has responded
         */
        return (trim($this->text) != '') ? true : false;
    }

    public function hasProvidedPhoneDetails()
    {
        return !empty($this->msisdn);
    }

    /*  displayLandingPage()
     *  This is the first page displayed when accessing the USSD.
     *  In this page we ask the user to enter their account email
     *  or mobile number used for authentication
     */
    public function displayLandingPage()
    {
        $response = "CON Merchant Portal:\nPlease enter your account email/mobile number to login";

        return $response;
    }


    /*  displayPasswordPage()
     *  This is the page where the user must enter their account
     *  password for authentication purposes.
     */
    public function displayPasswordPage()
    {
        $response = 'CON Please enter your account password to login';

        return $response;
    }

    /*  displayStoreSeletionPage()
     *  This is the page where the user must select a specific store
     *  from all available stores that the user has been allocated
     */
    public function displayStoreSeletionPage()
    {
        if( !empty( $this->stores ) ){

            $response = "CON Select store to visit\n";

            foreach( $this->stores->load('ussdInterface') as $key => $store ){
                
                $number = (++$key);
                
                $response .= $number.". ". $store['name'] ." (".$store['ussdInterface']['code'].")\n";
    
            }

        }else{

            $response = $this->displayCustomErrorPage('Sorry, you do not have any stores.');

        }


        return $response;
    }

    /*  displaySearchOrderOrViewOrdersByStatusPage()
     *  This is the page where the user must select whether they want
     *  to search for an order or view orders categorized into groups
     *  according to their status
     */
    public function displaySearchOrderOrViewOrdersByStatusPage()
    {
        $response = "CON Select option:\n";
        $response .= "1. Search Order\n";
        $response .= "2. View Orders by status\n";

        return $response;
    }


    /*  displayEnterOrderNumberPage()
     *  This is the page where the user must enter the order number of
     *  the order they want to search
     */
    public function displayEnterOrderNumberPage()
    {
        $response = "CON Enter the order number:\n";

        return $response;
    }

    /*  displayOrderSummaryPage()
     *  This is the page where the order summary is displayed.
     *  It shows the order details and actions that can be 
     *  taken.
     */
    public function displayOrderSummaryPage()
    {
        $items_count = count($this->order->item_lines) ?? 0;

        $response = "CON Order #".$this->order->number.":\n";
        $response .= "Status: (".ucwords($this->order->manual_status).")\n";
        $response .= "1. Mark As Completed*\n";
        $response .= "2. More Actions\n";
        $response .= "---\n";
        $response .= "3. Items(".$items_count.")\n";
        $response .= "4. Customer\n";
        $response .= "5. Cost Breakdown\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrderItemsPage()
     *  This is the page where the order items are displayed.
     *  It shows all the items that were placed on the order
     */
    public function displayOrderItemsPage()
    {

        /*  Get the cart items (Array) e.g ["1x(Product 1)", "2x(Product 3)"]  */
        $order_items_array = $this->getOrderItemsInArray();

        $response = "CON Order #".$this->order->number." Items:\n";

        foreach( $order_items_array as $item ){

            $response .= $item."\n";

        }

        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrderCustomerPage()
     *  This is the page where the order customer details are displayed.
     *  It shows the customer contact number
     */
    public function displayOrderCustomerPage()
    {
        $response = "CON Name: ".$this->order->customer->name."\n";
        $response .= "CON Phone(s): ".$this->order->customer->phone_list."\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrderCostBreakdownPage()
     *  This is the page where the order cost breakdown is
     *  displayed. It shows the orders Sub-Total, Tax-Total,
     *  Discount-Total and Grand-Total
     */
    public function displayOrderCostBreakdownPage()
    {
        $response = "CON Order #".$this->order->number." Breakdown:\n";
        $response .= "Sub Total (".$this->convertToMoney($this->order->sub_total).")\n";
        $response .= "Tax Total (".$this->convertToMoney($this->order->grand_tax_total).")\n";
        $response .= "Discount Total (".$this->convertToMoney($this->order->grand_discount_total).")\n";
        $response .= "Grand Total (".$this->convertToMoney($this->order->grand_total).")\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrderCompletionConfirmationPage()
     *  This is the page where the user must confirm the order completion.
     *  When the user confirms the order status will be changed to closed
     *  to reflect completion
     */
    public function displayOrderCompletionConfirmationPage()
    {
        $response = "CON Mark Order #".$this->order->number." as Completed. Please Confirm:\n";
        $response .= "1. Confirm\n";
        $response .= "0. Go Back\n";

        return $response;
    }


    /*  displayMoreOrderActionsPage()
     *  This is the page where the user will view more actions they can
     *  execute on the order. This is were the user can also mark the 
     *  order with additional statuses available
     */
    public function displayMoreOrderActionsPage()
    {
        $response = "CON Order #".$this->order->number." (More Actions):\n";
        $response .= "Mark As:\n";
        $response .= "1. Pending Payment\n";
        $response .= "2. Paid\n";
        $response .= "3. Pending Delivery\n";
        $response .= "4. Delivered\n";
        $response .= "5. Cancelled\n";
        $response .= "6. Completed\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrderNotFoundPage()
     *  This is the page where we notify the user that the order.
     *  they were searching for was not found.
     */
    public function displayOrderNotFoundPage()
    {
        $response = "CON Order #".$this->getProvidedOrderNumber()." was not found.\n";
        $response .= "0. Go Back";

        return $response;
    }

    /*  displayOrderCategoryPage()
     *  This is the page where the user must select a specific order
     *  category from all available categories. An order category 
     *  in this case refers to orders of the same group having
     *  the same status e.g Open, Paid, Completed, e.t.c
     */
    public function displayOrderCategoryPage()
    {
        $response = "CON Select Category:\n";
        $response .= "1. Open (".$this->store->orders()->open()->count().")\n";
        $response .= "2. Pending Payment (".$this->store->orders()->pendingPayment()->count().")\n";
        $response .= "3. Paid (".$this->store->orders()->paid()->count().")\n";
        $response .= "4. Pending Delivery (".$this->store->orders()->pendingDelivery()->count().")\n";
        $response .= "5. Delivered (".$this->store->orders()->delivered()->count().")\n";
        $response .= "6. Cancelled (".$this->store->orders()->cancelled()->count().")\n";
        $response .= "7. Completed (".$this->store->orders()->completed()->count().")\n";
        $response .= "8. Search Order #";

        return $response;
    }

    /*  displayOrderListPage()
     *  This is the page where the user must select a specific order
     *  from all available orders listed.
     */
    public function displayOrderListPage( $orders )
    {
        if( count( $orders ) ){

            $response = "CON Select Order (Page 1):\n";

            foreach( $orders as $key => $order ){
                
                $number = (++$key);
                
                $response .= $number.". Order #". $order['number'] ."\n";
    
            }

            $response .= "Enter * For Next\n";
            $response .= "0. Go Back\n";

        }else{

            $response = "CON No orders found.\n";
            $response .= "0. Go Back\n";

        }


        return $response;
    }

    /*  displayStoreDoesNotExistPage()
     *  This is the page displayed when a store is not found.
     */
    public function displayStoreDoesNotExistPage()
    {
        return $this->displayCustomErrorPage("Store was not found.\nMake sure you are using the correct store code");
    }

    /*  displayIssueConnectingToStorePage()
     *  This is the page displayed when a store existed during the session but
     *  we cannot seem to access it again. Maybe the store got deleted while a
     *  user was shopping or issues where encontered during a query
     */
    public function displayIssueConnectingToStorePage()
    {
        return $this->displayCustomErrorPage('Sorry, we could not access/connect to the store. Please try again');
    }

    /*  displayCustomErrorPage()
     *  This is the page displayed when a problem was encountered and we want
     *  to end the session with a custom error message.
     */
    public function displayCustomErrorPage($error_message)
    {
        $response = 'END '.$error_message;

        return $response;
    }

    /*  displayStoreLandingPage()
     *  This is the first page displayed when accessing the any store.
     *  In this page we ask the user to select product to add to cart
     */
    public function displayStoreLandingPage()
    {
        $response = "CON ".$this->ussd_interface['name'].": Select an option\n";
        $response .= "1. Edit Store\n";
        $response .= "2. View Orders\n";
        $response .= "3. View Products (".$this->ussd_products->count().")\n";
        $response .= "0. Exit";

        return $response;
    }

    /*  displayProductQuantityPage()
     *  This is the page displayed when a user must select a product quantity
     */
    public function displayProductQuantityPage()
    {
        $response = 'CON Select your quantity (how many you want) for this item "'.$this->product['name']."\"\n";
        $response .= '1. Select between 1 and '.$this->maximum_item_quantity."\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayCartSummaryPage()
     *  This is the page displayed when a user to show them a summary of what they
     *  are about to purchase at that point in time.
     */
    public function displayCartSummaryPage()
    {
        $summary_text = $this->summarize('Total: '.$this->currency.$this->cart['grand_total'].' Items: '.$this->cart['items_summarized_inline'], 100);
        $response = 'CON '.$summary_text."\n";
        $response .= "1. Pay Now\n";
        $response .= "0. Go Back\n";

        /*  If the number of store visits does not exceed the maximum cart items we can have.
         *  In this case lets image the number of store visits is the number of items we
         *  have. If:
         *
         *  Visit=1 then  Items added=1  and Max Items=3 (Allow another item to be added)
         *  Visit=2 then  Items added=2  and Max Items=3 (Allow another item to be added)
         *  Visit=3 then  Items added=3  and Max Items=3 (Don't allow another item to be added)
         *  Therefore only allow addition of items if the visits are strickly less than the
         *  maximum cart items
         */
        $response .= $this->canAddMoreItems() ? "Enter * to add another item\n" : '';

        return $response;
    }

    /*  displayPaymentOptionsPage()
     *  This is the page displayed when a user must select a payment method
     */
    public function displayPaymentOptionsPage()
    {
        $summary_text = $this->summarize('You are paying '.$this->currency.$this->cart['grand_total'].' for '.$this->cart['items_summarized_inline'], 100);
        $response = 'CON '.$summary_text.". Select payment method\n";
        $response .= "1. Airtime\n";
        $response .= "2. Orange Money\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayAirtimePaymentConfirmationPage()
     *  This is the page displayed when a user must confirm payment using Airtime
     */
    public function displayAirtimePaymentConfirmationPage()
    {
        $summary_text = $this->summarize('You are paying '.$this->currency.$this->cart['grand_total'].' for '.$this->cart['items_summarized_inline'], 100);
        $response = 'CON '.$summary_text.' using Airtime. You will be charged ('.$this->currency.$this->getServiceFee().") as a service fee. Please confirm\n";
        $response .= "1. Confirm\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrangeMoneyPaymentConfirmationPage()
     *  This is the page displayed when a user must confirm payment using Orange Money
     */
    public function displayOrangeMoneyPaymentConfirmationPage()
    {
        $summary_text = $this->summarize('You are paying '.$this->currency.$this->cart['grand_total'].' for '.$this->cart['items_summarized_inline'], 100);
        $response = 'CON '.$summary_text.' using Orange Money. You will be charged ('.$this->currency.$this->getServiceFee().") as a service fee. Please confirm\n";
        $response .= "1. Enter pin to confirm\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayPaymentSuccessPage()
     *  This is the page displayed when a user made a payment and it was successful.
     *  We display a success message as well as the order reference number
     */
    public function displayPaymentSuccessPage($order = null)
    {
        $response = 'CON Payment completed successfully. You will receive your payment confirmation details via SMS. ';
        $response .= 'Refer to your Order Reference #'.$this->order->number." when receiving your items. Thank you :)\n";
        $response .= "1. Continue shopping\n";
        $response .= "2. Exit\n";

        return $response;
    }

    /*  displayPaymentFailedPage()
     *  This is the page displayed when a user made a payment and it failed.
     */
    public function displayPaymentFailedPage($error_message = '')
    {
        return $this->displayCustomErrorPage('Sorry, payment failed. '.$error_message.' Try again.');
    }

    public function getServiceFee()
    {
        $cartTotal = $this->cart['grand_total'];

        if ($cartTotal >= 30) {
            return 5.00;
        } elseif ($cartTotal > 15) {
            return 1.50;
        } else {
            return 0.60;
        }
    }

    public function getStoreLandingPageProducts()
    {
        $response = '';

        /*  If we have any products  */
        if (count($this->products)) {
            /*  List the products available  */
            foreach ($this->products as $key => $product) {
                $option = $key + 1;

                /*  Get the product name, currency symbol and price  */
                $product_id = trim($product['id']);
                $product_name = trim($product['name']);
                $product_price = $product['unit_price'];
                $this->currency = $product['currency']['symbol'] ?? $product['currency']['code'];

                /*  Check if the product is on sale  */
                $product_on_sale = !empty($product['unit_sale_price']) ? true : false;

                /*  Show this product with price only if:
                 *  1 - It has inventory and the quantity is greater than zero (otherwise it is out of stock)
                 *  2 - It does not have inventory (no stock taken)
                 */
                if (($product['stock_quantity'] > 0 && $product['has_inventory']) || !$product['has_inventory']) {
                    /*  Check if the product has been added to the cart already  */
                    if ($this->isProductAddedToCart($product_id)) {
                        /*  Show the product name, and indicate that the product is in the cart already  */
                        $response .= $option.'. '.$product_name." (added)\n";

                    /*  If the product hasn't been added to the cart already  */
                    } else {
                        /*  Show the product name, currency and price  */
                        $response .= $option.'. '.$product_name.' -'.$this->currency.$product_price;

                        /*  If the product is on sale then make an indication  */
                        $response .= ($product_on_sale ? ' (on sale)' : '')."\n";
                    }

                    //  Otherwise show this product as out of stock
                } else {
                    /*  Show the product name, and indicate that this product is out of stock  */
                    $response .= $option.'. '.$product_name." (out of stock)\n";
                }
            }
        } else {
            /*  If we don't have any products to list  */
            $response .= "\nSorry, no products today :)\n";
        }

        return $response;
    }

    /*  isValidUserAccount()
     *  Returns true/false if an account using the provided email/mobile number exists
     */
    public function isValidUserAccount()
    {
        /*  If the user already responded to the "Landing Page (Enter Email/Mobile Number)"
         *  (Level 1), then they have provided their email/mobile number. We can get the 
         *  provided information and find out if any account using the given email/phone
         *  exists.
         */
        $email_or_mobile_number = $this->getResponseFromLevel(1);

        if ($email_or_mobile_number) {

            //  Check if any account matches the given email/mobile number
            $user = (new \App\User)->findMatchingAccount([
                        'email' => $email_or_mobile_number,
                        'mobile_number' => $email_or_mobile_number
                    ]);

            /*  If a user was found */
            if ($user) {

                /*  Return true to indicate that the account exists */
                return true;

            }

        }

        /*  Return false to indicate that the account does not exist */
        return false;
    }

    /*  getUssdInterface()
     *  Returns the store object that matches the ussd code provided
     */
    public function getUssdInterface($ussd_code = null, $password = null)
    {
        /*  If a store code was provided  */
        if ($ussd_code) {

            if( $password ){

                /*  Get the USSD Interface that matches the ussd code and password provided  */
                return UssdInterface::where('code', $ussd_code)->where('password', $password)->first() ?? null;

            }else{

                /*  Get the USSD Interface that matches the ussd code provided  */
                return UssdInterface::where('code', $ussd_code)->where('password', $password)->first() ?? null;

            }

        }

        return false;
    }

    /*  hasProvidedLoginPassword()
     *  Returns true/false of whether the user already provided 
     *  their account login password
     */
    public function hasProvidedLoginPassword()
    {
        /*  If the user already responded to the Enter Account Password Page (Level 2)
         *  with a specific password then return true otherwise false.
         */
        return  $this->completedLevel(2);
    }

    /*  isValidLoginDetails()
     *  Returns true/false if the provided email/mobile number and  
     *  password are correct login details. It also updates the
     *  $this->user attribute with the authenticated user.
     */
    public function isValidLoginDetails()
    {
        /*  If the user already responded to the "Landing Page (Enter Email/Mobile Number)" (Level 1)
         *  then we have the "Email/Mobile Number" and if they responded to the "Enter Password Page" (level 2)
         *  the we have the "Account Password". We can use this information to login to the user account and
         *  return true/false for success/fail.
         */
        $email_or_mobile_number = $this->getResponseFromLevel(1);
        
        $identity = [
            'email' => $email_or_mobile_number,
            'mobile_number' => $email_or_mobile_number
        ];

        $password = $this->getResponseFromLevel(2);

        if ($email_or_mobile_number && $password) {

            $loginResponse = (new \App\User)->initiateUserLogin( $identity, $password );
            
            /*  If the login attempt was successful */
            if ($loginResponse) {

                /*  Get the logged in user */
                $this->user = $loginResponse;

                /*  Return true to indicate login success */
                return true;

            }

        }

        /*  Return true to indicate login failed */
        return false;
    }

    /*  hasSelectedStoreToVisit()
     *  Returns true/false of whether the user already selected 
     *  a store to visit from the list of avaialable stores
     *  the user has been allocated to.
     */
    public function hasSelectedStoreToVisit()
    {
        /*  If the user already responded to the Select Store Page (Level 3)
         *  with a specific password then return true otherwise false.
         */
        return  $this->completedLevel(3);
    }

    public function visitStore($visit = 1)
    {
        /*  Allow the visit property to be accessd outside this function */
        $this->visit = $visit;

        if ($visit != 1) {
            /*  An offset is only required on every visit except the first. It allows us to target the correct
            *  product in each visit as we return to the store several times. Once we target the right product
            *  we can then see how far along the user is with that product e.g have they selected variable?
            *  Have they added the quantity?, do they want to pay? ... The offset helps us know which
            *  product we are now focusing on for each visit.
            */

            /*  We know that everytime we create a product we need to provide 3 levels of information describing
             *  that product. We need the (1) "choosen product", (2) "choosen variation" (0 if the product does
             *  not have variations) and the (3) "choosen quantity". When visiting a store the first time, the
             *  first product details are immediately accessible (no offset required) however to get the details
             *  of the next product on the following visitation we need to offset to properly target the next
             *  product. The offset is calculated by the number of visitations minus 1 multiplied by 3.
             *
             *  Visit (1): 1*001 (Access to store) *1*0*2 (Product 1 info)
             *  Visit (2): 1*001 (Access to store) *1*0*2 (Product 1 info) *2*1*3 (Product 2 info)
             *
             *  In visit 1 the product info says we selected product (1) which has no variables (0) and took (3) quantities of it
             *  In visit 2 the product info says we selected product (2) and chose variation (1) and took (3) quantities of it
             *
             *  If we think about it, in visit (1) we don't have any previously added products since we never visited
             *  the store before (past visitation = products added already). To calculate the past visitations we
             *  say the [current visit - 1]. On the first visit our past visitation is [1 - 1 = 0] meaning we never
             *  visited before. In visit (2) our past visitations to the store is [2 - 1 = 1] meaning we previously
             *  visited the store once. This means so far we have one item that was added to the cart. To access the
             *  current item we need to skip over all the past items, in visit (2) only one item must be skipped. To
             *  do this we multiply by three (3) since we know each product only has (3) entities to describe them.
             *
             *  visit (1) = (1 - 1) * 3 = 0 (no need to offset)
             *  visit (2) = (2 - 1) * 3 = 3 (offset by three to target the second item)
             *  visit (3) = (3 - 1) * 3 = 6 (offset by six to target the third item)
             *  e.t.c
             *
             *  This allows us to visit the store as many times as we want and still access the relevant product details.
             *
             */
            $this->offset = ($visit - 1) * 3;
        } else {
            $this->offset = 0;
        }

        /*  Get the Ussd Interface
         *
         *  The interface contains the USSD name, description and the USSD Code. The
         *  interface also gives us access to the owning store, which allows us to 
         *  access the store products, discounts, taxes, e.t.c
         */

        /*  Get the selected store  */
        $this->store = $this->getSelectedStore();

        /*  Get the selected store USSD Interface - Only if the store exists  */
        $this->ussd_interface = $this->store ? $this->store->ussdInterface : null;

        /*  Get all the store Products - Only if the store exists  */
        $this->products = $this->store ? $this->store->products : null;

        /*  Get the store USSD Products - Only if the store exists  */
        $this->ussd_products = $this->store ? $this->store->ussdProducts : null;

        /*  Get the store orders only if the store exists  */
        $this->orders = $this->store ? $this->store->orders : null;

        /*  If no store using the provided ussd code was found. Maybe the store
         *  was deleted or we could not gain access to it for some reason
         */
        if (!$this->store) {

            /*  Notify the user that we have issues connecting to the store  */
            return $this->displayIssueConnectingToStorePage();

        }

        /*  If the user already selected an option from the (Store Landing Page)  */
        if ($this->hasSelectedStoreLandingPageOption()) {

            /*  If the user has selected the edit store option from the (Store Landing Page)  */
            if($this->hasSelectedEditStoreOption()) {

                /*  If the user was on the "Edit Store Page" but wants to 
                 *  go back to the (Store Landing Page)"
                 */
                if ($this->wantsToGoBackToHomePageFromEditStorePage()) {

                    /*  Go back only (1) Level to the (Store Landing Page)  */
                    $response = $this->goBack($how_many_times = 1);

                /*  If the user wants to change the store name  */
                } elseif( wantsToChangeStoreName() ){

                    /*  If the user has not provided the store name */
                    if( $this->hasProvidedStoreName() ){

                        $this->changeStoreName();

                    /*  If the user has not provided the store name */
                    } else {

                        /*  Show the user the "Change Store Name Page"  */
                        $response = $this->displayChangeStoreNamePage();
            
                    }

                /*  If the user wants to change the store description  */
                } elseif( wantsToChangeStoreDescription() ){

                    /*  If the user has not provided the store description */
                    if( $this->hasProvidedStoreDescription() ){

                        $this->changeStoreDescription();

                    /*  If the user has not provided the store description */
                    } else {

                        /*  Show the user the "Change Store Description Page"  */
                        $response = $this->displayChangeStoreDescriptionPage();
            
                    }

                /*  If the selected an option that does not exist  */
                } else {

                    return $this->displayCustomErrorPage('You selected an incorrect option. Please try again');

                }
            
            /*  If the user has selected the view orders option from the (Store Landing Page)  */
            }elseif($this->hasSelectedViewOrdersOption()){

                if( $this->hasSelectedSearchOrdersOption() ){

                    if( $this->hasProvidedOrderNumberToSearch() ){

                        if( $this->searchedOrderExists() ){

                            /*  Make sure the serched order is accessible from here on  */
                            $this->order = $this->getSearchedOrder();

                            if( $this->hasSelectedMarkOrderAsCompleted() ){

                                /*  Show the user the "Order Completion Confirmation Page"  */
                                $response = $this->displayOrderCompletionConfirmationPage();

                            }elseif( $this->hasSelectedViewMoreActions() ){

                                /*  Show the user the "More Order Actions Page"  */
                                $response = $this->displayMoreOrderActionsPage();

                            }elseif( $this->hasSelectedViewOrderItems() ){

                                /*  Show the user the "Order Items Page"  */
                                $response = $this->displayOrderItemsPage();

                            }elseif( $this->hasSelectedViewOrderCustomer() ){

                                /*  Show the user the "Order Customer Page"  */
                                $response = $this->displayOrderCustomerPage();

                            }elseif( $this->hasSelectedViewOrderCostBreakdown() ){

                                /*  Show the user the "Order Cost Breakdown Page"  */
                                $response = $this->displayOrderCostBreakdownPage();

                            }else{

                                /*  Show the user the "Order Summary Page"  */
                                $response = $this->displayOrderSummaryPage();

                            }
                            
                        }else{

                            /*  Show the user the "Order Not Found Page"  */
                            $response = $this->displayOrderNotFoundPage();

                        }

                    }else{

                        /*  Show the user the "Enter Order Number Page"  */
                        $response = $this->displayEnterOrderNumberPage();

                    }

                }elseif( $this->hasSelectedViewOrderByStatusOption() ){

                    /*  If the user already selected a specific order category
                     *  from the (View Orders Page).
                     */
                    if($this->hasSelectedOrderCategory()){

                        /*  If the user was on the "Orders Page" but wants to 
                        *  go back to the (Store Landing Page)"
                        */
                        if ($this->wantsToGoBackToHomePageFromOrderCategoriesPage()) {

                            /*  Go back only (1) Level to the (Store Landing Page)  */
                            $response = $this->goBack($how_many_times = 1);

                        } elseif( $this->wantsToViewOpenOrders() ){

                            /*  Get the orders that are currently "Open"  */
                            $openOrders = $this->store->orders()->open()->get();

                            /*  Show the user the "Select Order Page"  */
                            $response = $this->displayOrderListPage( $openOrders );

                        } elseif( wantsToViewCancelledOrders() ){

                            return 'cancelled orders';

                        } elseif( wantsToViewPendingPaymentOrders() ){

                            return 'pending payment orders';

                        } elseif( wantsToViewPaidOrders() ){

                            return 'paid orders';

                        } elseif( wantsToViewPendingDeliveryOrders() ){
                            
                            return 'pending delivery orders';

                        } elseif( wantsToViewDeliveredOrders() ){
                            
                            return 'delivered orders';

                        } elseif( wantsToViewCompletedOrders() ){
                            
                            return 'completed orders';

                        /*  Selected an option that does not exist  */
                        } else {

                            return $this->displayCustomErrorPage('You selected an incorrect option. Please try again');

                        }
                    }else{

                        /*  Show the user the "Select Order Category Page"  */
                        $response = $this->displayOrderCategoryPage();

                    }

                }else{

                    /*  Show the user the "Search Order Or View Orders By Status Page"  */
                    $response = $this->displaySearchOrderOrViewOrdersByStatusPage();

                }
            
            /*  If the user has selected the view products option from the (Store Landing Page)  */
            }elseif($this->hasSelectedViewProductsOption()){

                /*  If the user already selected a specific product
                 *  from the (View Products Page).
                 */
                if($this->hasSelectedProduct()){

                    /*  If the user was on the "View Products Page" but wants to 
                     *  go back to the (Store Landing Page)"
                     */
                    if ($this->wantsToGoBackToHomePageFromProductsPage()) {

                        /*  Go back only (1) Level to the (Store Landing Page)  */
                        $response = $this->goBack($how_many_times = 1);

                    }
                }else{

                    /*  Show the user the "Select Order Category Page"  */
                    $response = $this->displayOrderCategoryPage();

                }

            /*  Selected an option that does not exist  */
            } else {

                return $this->displayCustomErrorPage('You selected an incorrect option. Please try again');

            }

            /*  If the user has not selected any product  */
        } else {

            /*  Show the user the store landing page  */
            $response = $this->displayStoreLandingPage();

        }

        return $response;
    }

    public function hasSelectedStoreLandingPageOption()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 3)
         *  by selecting an option.
         */
        return  $this->completedLevel(4 + $this->offset);
    }

    public function getSelectedStore()
    {
        /*  Get the selected store option from the "Select Store Page" (Level 3)
         *  We can use the selected option to retrieve the store. We also
         *  make sure to convert the value received to an integer
         */
        $selected_store_option = (int) $this->getResponseFromLevel(3);

        /*  If we have a selected store option (e.g 1, 2 or 3)  */
        if ($selected_store_option) {
            /*  Retrieve the actual store that was selected. Note that the user
             *  would have replied "1" to select the first store on the list.
             *  However the first store on "$this->stores" variable is of
             *  index "0", this means we need to always subtract "1" from the
             *  user reply to access the correct store.
             */
            return $this->stores[$selected_store_option - 1];
        }
    }

    public function hasSelectedEditStoreOption()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 4)
         *  by selecting the "Edit Store" option.
         */
        return  $this->completedLevel(4 + $this->offset) && $this->getResponseFromLevel(4 + $this->offset) == '1';
    }

    public function hasSelectedViewOrdersOption()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 4)
         *  by selecting the "View Orders" option.
         */
        return  $this->completedLevel(4 + $this->offset) && $this->getResponseFromLevel(4 + $this->offset) == '2';
    }

    public function hasSelectedViewProductsOption()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 4)
         *  by selecting the "View Products" option.
         */
        return  $this->completedLevel(4 + $this->offset) && $this->getResponseFromLevel(4 + $this->offset) == '3';
    }

    public function hasSelectedSearchOrdersOption()
    {
        /*  If the user already responded to the "Search Order Or View Orders By Status Page"
         *  (Level 5) by selecting option "1" being the "Search Orders" option.
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '1';
    }

    public function hasSelectedViewOrderByStatusOption()
    {
        /*  If the user already responded to the "Search Order Or View Orders By Status Page"
         *  (Level 5) by selecting option "2" being the "View Orders By Status" option.
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '2';
    }

    public function hasProvidedOrderNumberToSearch()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 6) by providing the order number.
         */
        return  $this->completedLevel(6 + $this->offset);
    }

    public function getProvidedOrderNumber()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 6) by providing the order number. We can get that 
         *  order number they provided
         */
        return  $this->getResponseFromLevel(6 + $this->offset);
    }

    public function getSearchedOrder()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 6) by providing the order number, we can get the order
         *  that matches the provided order number
         */

        /*  Get the order number provided by the user  */
        $orderNumber = $this->getResponseFromLevel(6 + $this->offset);

        return $this->store->orders()->where('number', $orderNumber)->first() ?? null;
    }

    public function searchedOrderExists()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 6) by providing the order number, we can check if the 
         *  order actually exists
         */
        return  !empty( $this->getSearchedOrder() ) ? true : false;
    }

    public function hasSelectedMarkOrderAsCompleted()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 7)
         *  by selecting option "4" being the "Mark Order As Completed" option.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '1';
    }

    public function hasSelectedViewMoreActions()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 7)
         *  by selecting option "5" being the "Mark Order As Completed" option.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '2';
    }

    public function hasSelectedViewOrderItems()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 7)
         *  by selecting option "1" being the "View Order Items" option.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '3';
    }

    public function hasSelectedViewOrderCustomer()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 7)
         *  by selecting option "2" being the "View Order Customers" option.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '4';
    }

    public function hasSelectedViewOrderCostBreakdown()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 7)
         *  by selecting option "3" being the "View Order Cost Breakdown" option.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '5';
    }

    public function getOrderItemsInArray()
    {
        /*  Get the cart items as an (Array) e.g ["1x(Product 1)", "2x(Product 3)"]  */
        $order_items_array = ( new \App\MyCart() )->getItemsSummarizedInArray($this->order->item_lines);

        return $order_items_array;
    }



    

    public function wantsToGoBackToHomePageFromEditStorePage()
    {
        /*  If the user is currently at the "Edit Store Page" (Level 5)
         *  but wants to go back to the "Store Landing Page" (Level 4)
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '0';
    }

    public function wantsToGoBackToHomePageFromOrderCategoriesPage()
    {
        /*  If the user is currently at the "Select Order Category Page" (Level 5)
         *  but wants to go back to the "Store Landing Page" (Level 4)
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '0';
    }

    public function wantsToGoBackToHomePageFromProductsPage()
    {
        /*  If the user is currently at the "Edit Store Page" (Level 5)
         *  but wants to go back to the "Store Landing Page"
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '0';
    }

    public function hasSelectedOrderCategory()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 5)
         *  by selecting the "Edit Store" option.
         */
        return  $this->completedLevel(5 + $this->offset);
    }
    

    public function wantsToViewOpenOrders()
    {
        /*  If the user has responded to the "Select Order Category Page"
         *  by selecting the option "1".
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '1';
    }

    public function wantsToGoToPreviousPageFromQuantityPage()
    {
        /*  If the user is currently at the "Select Product Quantity Page" (Level 5)
         *  but wants to go back
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '0';
    }

    public function isValidProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a quantity. Get the product quantity provided.
         */
        $quantity_provided = (int) $this->getResponseFromLevel(5 + $this->offset);

        /*  Check if the quantity provided by the user does not exceed the maximum item quantity allowed  */
        $doesNotExceedMaximumQty = ($this->maximum_item_quantity >= $quantity_provided);

        /*  Check if the quantity provided by the user is not zero (0)  */
        $doesNotEqualZero = ($quantity_provided != 0);

        /*  If the quantity does not exceed the maximum quantity and also does not equal "0" then it is valid  */
        return $doesNotExceedMaximumQty && $doesNotEqualZero;
    }

    public function wantsToGoToPreviousPageFromCartSummaryPage()
    {
        /*  If the user is currently at the "Cart Summary Page" (Level 6)
         *  but wants to go back to the "Select Product Quantity Page"
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '0';
    }

    /*  revisitStore()
     *
     *   Allows the user the ability to revisit the store to select another product
     */
    public function revisitStore()
    {
        return $this->visitStore($this->visit + 1);
    }

    public function wantsToPay()
    {
        /*  If the user already responded to the Cart summary page (Level 6)
         *  by selecting option (1) for pay now.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '1';
    }

    public function wantsToGoToPreviousPageFromPaymentOptionsPage()
    {
        /*  Get the selected option from the "Selected Payment Method Page" (Level 7)
         *  If the option equals "0" then the user to go back to the "Cart Summary Page",
         *  the previous page.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '0';
    }

    public function hasSelectedPaymentMethod()
    {
        /*  If the user already responded to the Select payment method page (Level 7)
         *  by selecting a specific payment method option.
         */
        return  $this->completedLevel(7 + $this->offset);
    }

    public function wantsToPayWithAirtime()
    {
        /*  If the user already responded to the Select payment method page (Level 6)
         *  by selecting option (1) for pay using Airtime.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '1';
    }

    public function wantsToPayWithOrangeMoney()
    {
        /*  If the user already responded to the Select payment method page (Level 6)
         *  by selecting option (2) for pay using Orange Money.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '2';
    }

    public function wantsToGoToPreviousPageFromPaymentConfirmationPage()
    {
        /*  Get the selected option from the "Confirm Payment Page" (Level 8). If the
         *  option equals "0" then the user to go back to the "Select Payment Method Page",
         *  the previous page.
         */
        return  $this->completedLevel(8 + $this->offset) && $this->getResponseFromLevel(8 + $this->offset) == '0';
    }

    public function hasConfirmedPaymentWithAirtime()
    {
        /*  If the user already responded to the "Confirm Payment Using Airtime Page" (Level 7)
         *  by selecting option (1) to confirm payment.
         */
        return  $this->completedLevel(8 + $this->offset) && $this->getResponseFromLevel(8 + $this->offset) == '1';
    }

    public function hasConfirmedPaymentWithOrangeMoney()
    {
        /*  If the user already responded to the Confirm payment using Orange Money page (Level 7)
         *  by selecting a specific option.
         */
        return  $this->completedLevel(8 + $this->offset);
    }

    public function isValidOrangeMoneyPin()
    {
        /*  If the user already responded to the "Confirm payment using Orange Money page" (Level 7)
         *  by selecting a specific option. Then we can capture the Orange Money pin they provided
         */
        $orange_money_pin = $this->getResponseFromLevel(8 + $this->offset);

        /*  If the Pin provide is 4 digits long  */
        if (strlen($orange_money_pin) == 4) {
            /*********************************************
             *  API HERE TO VERIFY THE ACCOUNT USING PIN
             ********************************************/

            return true;
        }

        return false;
    }

    public function processPaymentWithAirtime()
    {
        /*********************************************
         *  API TO PAY THE ORDER USING AIRTIME
         ********************************************/

        /*  The response we return will be an array holding a status of the
         *  transaction and an error incase the transaction is declined
         */
        return ['status' => true, 'error' => null];
    }

    public function processPaymentWithOrangeMoney()
    {
        /*********************************************
         *  API TO PAY THE ORDER USING Orange Money
         ********************************************/

        /*  The response we return will be an array holding a status of the
         *  transaction and an error incase the transaction is declined
         */
        return ['status' => true, 'error' => null];
    }

    public function procressOrder($payment_method = null)
    {
        /***************************************************************************************
         * Include transaction fee to the grand total amount of the cart before making payment
         **************************************************************************************/

        /* If the user specified to pay using Airtime  */
        if ($payment_method == 'airtime') {
            /*  Attempt to process the payment using Airtime  */
            $payment_response = $this->processPaymentWithAirtime();

        /* If the user specified to pay using Orange Money  */
        } elseif ($payment_method == 'orange_money') {
            /*  Attempt to process the payment using Orange Money  */
            $payment_response = $this->processPaymentWithOrangeMoney();
        } else {
            $payment_response = ['status' => false, 'error' => 'No payment method was specified'];
        }

        /*  If the payment status was successful  */
        if ($payment_response['status']) {
            /******************************************************************
             *  Find/Create a contact
             *  Create a new order for contact
             *  Convert the order to a payable invoice
             *  Create a transation for the invoice (Status=paid)
             *  Update the order lifecycle (Paid)
             *  Update the order lifecycle (Pending Delivery)
             *  Send a payment confirmation sms with an order ref #
             ******************************************************************/

            /*  Get the customer information */
            $customer_info = [
                'name' => 'Julian Tabona',
                'is_vendor' => false,
                'is_customer' => true,
                'is_individual' => true,
                'phone' => [
                    'calling_code' => $this->user['phone']['calling_code'],
                    'number' => $this->user['phone']['number'],
                    'provider' => 'orange',
                    'type' => 'mobile',
                ],
                'address' => null,
                'email' => null,
            ];

            /*  Create a new order using the provided customer information,
             *  merchant id and items. The initiateCreate() method will create,
             *  a new customer, order and payable invoice all linked together.
             */
            $this->order = ( new \App\Order() )->initiateCreate([
                'customer_info' => $customer_info,
                'merchant_id' => $this->store->id,
                'items' => $this->cart['items'],
            ]);

            /*  Send the order as a summarised SMS to the merchant  */
            $merchantSMS = $this->order->smsOrderToMerchant();

            /*  Send the invoice receipt as a summarized SMS to the customer  */
            $customerSMS = $this->order->invoices()->first()->smsInvoiceReceiptToCustomer();

            /*  Mark the order invoice as paid  */
            $payment = $this->order->invoices()->first()->recordAutomaticPayment($transaction = [
                'payment_type' => $payment_method,
                'payment_amount' => $this->cart['grand_total'],
            ]);

            /*  Notify the user of the payment success  */
            $response = $this->displayPaymentSuccessPage();
        } else {
            /*  Fetch the error (Reason why the payment failed)  */
            $error = $payment_response['error'];

            /*  Notify the user of the payment failure  */
            $response = $this->displayPaymentFailedPage($error);
        }

        return $response;
    }

    public function summarize($text, $limit)
    {
        return strlen($text) > $limit ? substr($text, 0, $limit + 3).'...' : $text;
    }

    public function wantsToAddAnotherProduct()
    {
        /*  If the number of products we want to add do not match the number of
         *  visitations we have made to the store then this means we want to
         *  add another product.
         */
        $number_of_products_to_add = $this->getInformationOfProductsToAddToCart();

        if (count($number_of_products_to_add) != $this->visit) {
            return true;
        }

        return false;
    }

    public function getInformationOfProductsToAddToCart()
    {
        /*  We use the "**" symbol to indicate a response by the user to add another product.
         *  When we explode the data using the "**" we retrieve the data in an array. The
         *  first part is the access to the store and the first product selected. The next
         *  datasets are descriptions of the next products selected.
         *
         *  Assume: 1*001*1*0*3
         *
         *  This means the user has access to a store and has selected product option (1)
         *  which has no variables (0) and a quantity of (3). If the user replies with "*",
         *  the USSD will automatically add another "*" to that user response giving us the
         *  "**" result. Its just the same as a use replying "1" or "2" and the USSD always
         *  appends the "*" to give us "*1" or "*2", just that we get "**" since the user
         *  replied with the asterisk symbol instead of a number.
         *
         *  Now we have: 1*001*1*0*3**
         *
         *  When we explode using "**" = ["1*001*1*0*3", ""]
         *
         *  As you can see, we have the shop details and first product info as the first
         *  value and no information about the second product. If the user selects a
         *  product, and its variable and quantity, then
         *
         *  Now we have: 1*001*1*0*3***3*2*4
         *
         *  Notice that now we have "***" and not "**" anymore. This is because when the user
         *  replied with selecting the product of choice the USSD automatically added another
         *  "*" as a separator. This is done automatically and is how USSD works to separate
         *  the user responses.
         *
         *  When we explode using "**" = ["1*001*1*0*3", "*3*2*4"]
         *
         *  Meaning for the second product we selected product option (3), then variable (2)
         *  and a quantity of (4).
         *
         *  We need to avaid starting each following product with an "*", so what we can do
         *  is explode using the "***" to kill off the exta "*" added by the USSD when the
         *  user replies on every new product option selection.
         *
         *  When we explode using "***" = ["1*001*1*0*3", "3*2*4"]
         *
         *  Since it is possible that both "***" and "**" to exist at the same time e.g
         *
         *  When we have: 1*001*1*0*3***3*2*4**
         *
         *  As seen above the user has selected product (1) and product (2) but has also
         *  indicated their interest to add product (3) eventhough they have not made
         *  any selection of that third product. This leaves us with a scenerio where
         *  we have the user reply existing with both "***" and "**". To properly
         *  separate this type of data we need to first explode by "***" and the
         *  explode by "**" to first separate the completed product selctions and
         *  then show the users intent to add another product.
         *
         *  Given: 1*001*1*0*3***3*2*4**
         *
         *  We explode using "***" to get: ["1*001*1*0*3", "3*2*4**"]
         *
         *  Foreach result we explode using "**" to get: ["1*001*1*0*3", "3*2*4", ""]
         *
         *  1st element shows the first store information and the first product information
         *  2nd element shows the second product information
         *  3rd element shows the third product information
         *
         *  Note that the "1*001" only represents the path to the store and the ussd code
         *  used to access the actual store. It does not relate to any product selection
         *  or description.
         *
         *  1 = Level 1 option (Enter ussd code option)
         *  001 = Level 2 option (The ussd code provided)
         */

        /*  Split by completed product selections
         *
         *  Before: 1*001*1*0*3***3*2*4**
         *  After:  ["1*001*1*0*3", "3*2*4**"]
         *
         */
        $result = explode('***', $this->text);

        foreach ($result as $key => $data) {
            /*
             *  Remember that the first element in the $result array will always contain
             *  non-product related information such as the ussd code. We must remove
             *  this information so that we are strickly left with the product related
             *  information
             */

            /*  If this is the first product information  */
            if ($key == 0) {
                /*  Split the data into individual responses e.g
                 *
                 *  $data = 1*001*1*0*3
                 *
                 *  After explode using "*" = ["1", "001","1", "0", "3"]
                 */
                $user_responses = explode('*', $data);

                /*  Remove the store related information
                 *
                 *  Before: $user_responses = ["1", "001","1", "0", "3"]
                 *  After:  $user_responses = ["1", "0", "3"]
                 */
                unset($user_responses[0]);  //  Removes the "landing page response"
                unset($user_responses[1]);  //  Removes the "ussd code response"

                /*  Combine the responses into a single string using "*" as a separator
                 *
                 *  Before: ["1", "0", "3"]
                 *  After:  1*0*3
                 */
                $result[$key] = implode('*', $user_responses);

                /*  Update the currnt data variable  */
                $data = $result[$key];
            }

            /*  Split by completed product selections
             *
             *  $data Before: 3*2*4**
             *  $data After:  ["3*2*4", ""]
             */
            $result[$key] = explode('**', $data);
        }

        /*  We must flatten the result to break all the inner arrays
         *
         *  Before: ["1*0*3", ["3*2*4", ""]]
         *  After:  ["1*0*3", "3*2*4", ""]
         *
         *  The information we now have is the products selected and the relating information.
         *  In this case we can see that:
         *
         *  item 1: 1st product selected, no variable, and quantity of 3
         *  item 2: 3rd product selected, 2nd variable selected, and quantity of 4
         *  item 3: (Empty) The user has not made their 3rd product of choice yet
         */
        $products_to_add_to_cart = array_flatten($result);

        return $products_to_add_to_cart;
    }

    public function getCart()
    {
        /*  We need to first figure out the product the user selected. Once we know the
         *  exact product we can get the products "id" and "quantity". We can then use
         *  this information to get a full cart description with the all items, total
         *  taxes and total discounts calculated.
         */

        /*  Get the information of the products the user wants to add to cart e.g
         *
         *  $products_to_add = ["2*0*1", "3*2*4", "4*3*2", ""]
         *
         *  Then item 1: 2nd product selected, no variable, and quantity of 1
         *  Then item 2: 3rd product selected, 2nd variable selected, and quantity of 4
         *  Then item 3: 4th product selected, 3rd variable selected, and quantity of 2
         *  Then item 4: (Empty) The user has not made their 4th product of choice yet
         */
        $products_to_add = $this->getInformationOfProductsToAddToCart();

        /*  Get the listed store products */
        $products = $this->products;

        /*  The $items variable will hold all the item ids and quantity:
         *
         *  $items = [
                ['id'=>1, quantity=>2],
                ['id'=>2, quantity=>3]
            ]
         */
        $items = [];

        /*  Foreach store visitation */
        foreach ($products_to_add as $product_to_add) {
            /*  If the user has already selected their product */
            if ($product_to_add != '') {
                /*
                 *  Now we must split the $product_to_add into individual responses
                 */
                $user_responses = explode('*', $product_to_add);

                /*
                 *  Three (3) responses are expected for each complete product selection cycle.
                 */
                if (count($user_responses) >= 3) {
                    /*  Get the product selected  */
                    $product = $products[$user_responses[0] - 1];

                    /*  If a variable product was selected  */
                    if ($user_responses[1] != 0) {
                        /*  Replace the simple product with the variable product selected  */
                        $product = $product->childProducts[$user_responses[1] - 1];
                    }

                    /*  Get the product id and quantity  */
                    $product_id = $product['id'];
                    $product_quantity = $user_responses[2];

                    array_push($items, ['id' => $product_id, 'quantity' => $product_quantity]);
                }
            }
        }
        /*  If we have items */
        if (count($items)) {
            /*  Retrieve and return the cart details relating to the merchant and items provided  */
            return ( new \App\MyCart() )->getCartDetails($this->store, $items);
        }
    }

    public function getUserResponses()
    {
        /*  The text variable represent the response from the user.
         *  To extract the users information we must explode the text
         *  to retrieve the users information concatenated using the *
         *  symbol over several interations.
         *
         *  $user_responses[0] = Response from screen 1 (Landing Page)
         *  $user_responses[1] = Response from screen 2
         *  e.t.c
         */

        $responses = explode('*', $this->text);

        /*  Remove empty keys  */
        $responses = array_filter($responses, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return array_values($responses);
    }

    public function getResponseFromLevel($levelNumber = null)
    {
        if ($levelNumber) {
            /*  Get all the user reponses.  */
            $user_responses = $this->getUserResponses();

            /*  We want to say if we have levelNumber = 1 we should get the landing page data
             *  (since thats level 1) but technically $user_responses[0] = landing page response.
             *  This means to get the response for the level we want we must decrement by one unit.
             */
            return isset($user_responses[$levelNumber - 1]) ? $user_responses[$levelNumber - 1] : null;
        }
    }

    public function completedLevel($levelNumber = null)
    {
        /*  If we have a level number  */
        if ($levelNumber) {
            /*  Check if we have a response for this level number  */
            $level = $this->getResponseFromLevel($levelNumber);

            /*  If the level specified is completed (Has a response from the user)  */
            return isset($level) && $level != '';
        }
    }

    public function simulateUserReply($reply = null)
    {
        /*  To simulate a user reply we need to append our reply/information to the
         *  TEXT query string of the current url. After this we must make a redirect.
         *  This simulation represents a user providing information and hitting the
         *  reply button on their phone.
         */

        /* Step 1:
         *
         * We need to simulate the user selecting an option or providing information.
         * To simulate this we must add our reply to the TEXT response. The "*" symbol
         * is added as a separator since USSD uses "*" to distinguish between multiple
         * replies. The $reply provided can be a single reply e.g "john" or multiple
         * replies i.e multiple options in an array e.g ["john", "doe", "25", e.t.c]
         */

        /*  If the reply information was provided */
        if (isset($reply)) {
            /*  If the reply provided is an array - meaning we have multiple replies provided */
            if (is_array($reply)) {
                /*  We need to combine the array data into a single string
                 *  begining and also separated with the "*" symbol:
                 *
                 *  Before: ["john", "doe", "25", e.t.c]
                 *  After: *john*doe*25
                 *
                 */
                $replies = '*'.implode('*', $reply);

                $url = $this->addToUrlText($replies);

            /*  If an array was not provided - meaning we only have only one reply */
            } else {
                $url = $this->addToUrlText('*'.$reply);
            }

            /*  Step 2:
             *
             *  Redirect to the updated URL. Redirecting here simulates the user pressing the "Reply"
             *  button on their phone after selecting an option or providing information. This
             *  conviniently allows us to revisit the USSD endpoint with our revised/updated
             *  information
             */
            return redirect($url);
        }
    }

    public function goBack($how_many_times = 1)
    {
        /*  Redirect back we need to remove the users last reply and make a new
         *  redirect using the updated TEXT information. This will allow the user
         *  to go back to the previous level screen.
         */

        /* Step 1:
         * We need to remove the users last reply and update the url.
         */

        /*  Retrieve the current url without any query strings  */
        $url = url()->current().'?';

        /*  Retrieve all of the current url query string values as an associative array
         *  ['TEXT' => '1*001*2', 'MSISDN' => '26775993221', e.t.c]
         */
        $request_query_array = request()->query();

        /*  Foreach time we should go back. */
        for ($x = 0; $x <= $how_many_times; ++$x) {
            /*  Get the original TEXT responses. */
            $original_text = $request_query_array['TEXT'];

            /*  Remove the last value (the last reply) and update the TEXT query.
             *  We are basically removing any last response the user gave us.
             */

            /*  Get the original text array */
            $original_text_array = explode('*', $original_text);

            /*  Remove the last item from the original text array  */
            array_pop($original_text_array);

            /*  Return array to string  */
            $updated_text = implode('*', $original_text_array);

            /*  Update the TEXT query string  */
            $request_query_array['TEXT'] = $updated_text;
        }

        /*  Re-attach the query strings to the url. This means we rebuild the url as it was, but
         *  obviously with the new updates with just made.
         */
        foreach ($request_query_array as $query_name => $query_value) {
            /*  Append the key/value query string e.g TEXT=1 or MSISDN=26775993221  */
            $url .= ($query_name.'='.$query_value);

            /*  If this is not the last item add "&" otherwise nothing  */
            $url .= (next($request_query_array)) ? '&' : '';
        }

        /*  Step 2:
         *  Redirect with the updated URL. e.g
         *  Before https://www.domain.com/api/ussd?TEXT=1*001*1*3 ...[other query strings]
         *  After https://www.domain.com/api/ussd?TEXT=1*001*1 ...[other query strings]
         *
         *  Redirecting here conviniently allows us to revisit the USSD endpoint with
         *  our revised/updated information
         */
        return redirect($url);
    }

    public function addToUrlText($value = null)
    {
        /*  Retrieve the current url without any query strings  */
        $url = url()->current().'?';

        /*  Retrieve all of the current url query string values as an associative array
         *  ['TEXT' => '1*001*2', 'MSISDN' => '26775993221', e.t.c]
         */
        $request_query_array = request()->query();

        /*  Add the new value to the TEXT query. This is simulating a user selecting an option
         *  or replying with specific text. We do not remove any past information they have
         *  already provided. We only add a new reply.
         */
        $request_query_array['TEXT'] .= $value;

        /*  Re-attach the query strings to the url. This means we rebuild the url as it was, but
         *  obviously with the updated information we just added.
         */
        foreach ($request_query_array as $query_name => $query_value) {
            /*  Append the key/value query string e.g TEXT=1 or MSISDN=26775993221  */
            $url .= ($query_name.'='.$query_value);

            /*  If this is not the last item add "&" otherwise nothing  */
            $url .= (next($request_query_array)) ? '&' : '';
        }

        /*  Return the updated URL. e.g
         *  Before https://www.domain.com/api/ussd?TEXT=1*001*1 ...[other query strings]
         *  After https://www.domain.com/api/ussd?TEXT=1*001*1*3 ...[other query strings]
         */
        return $url;
    }

    public function canAddMoreItems()
    {
        /*  If the number of store visits exceeds the maximum cart items that we can have then
         *  we have exceeded the limit of items we are allowed to checkout with. In this case
         *  lets imagine the number of store visits is also the number of items we have or
         *  want to checkout with. Then:
         *
        /*  If the number of store visits is less than the maximum cart items that we can have then
        /*  we can add more items since we would have not exceeded the limit of items we are allowed
         *  to checkout with. In this case lets imagine the number of store visits is also the number
         *  of items we have or want to checkout with. Then:
         *
         *  Visit=1 then  Items added=1  and Max Items=3 (Allow another item to be added)
         *  Visit=2 then  Items added=2  and Max Items=3 (Allow another item to be added)
         *  Visit=3 then  Items added=3  and Max Items=3 (Don't allow another item to be added)
         *
         *  Therefore only allow addition of items if the number of visits are strickly
         *  less than the maximum cart items
         */
        return $this->visit < $this->maximum_cart_items;
    }

    public function isProductAddedToCart($productId = null)
    {
        /*  Get the items added to cart */
        $items = $this->cart['items'];

        /*  Get all the item ids  */
        $itemIds = collect($items)->map(function ($item) {
            return $item['id'];
        });

        /*  Check if the provided product id exists in the collection  */
        return collect($itemIds)->contains($productId);
    }

    public function convertToMoney($amount = 0){

        return number_format($amount, 2, '.', ',');
        
    }
}
