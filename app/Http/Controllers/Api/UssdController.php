<?php

namespace App\Http\Controllers\Api;

use App\UssdInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class UssdController extends Controller
{
    private $user;
    private $text;
    private $cart;
    private $order;
    private $store;
    private $visit;
    private $offset;
    private $products;
    private $currency;
    private $session_id;
    private $service_code;
    private $ussd_interface;
    private $text_field_name;
    private $selected_product;
    private $selected_products;
    private $maximum_cart_items;
    private $maximum_item_quantity;
    private $selected_variable_options;

    public function __construct(Request $request)
    {
        /*  Get the Authenticated User (If available)
         *  Otherwise create a User Instance for Guest Users
         */
        $this->user = auth('api')->user() ?? (new \App\User());
        
        /*  Get the name of "TEXT" field used to save the user responses  */
        $this->text_field_name = 'text';

        /*  Get the USSD TEXT value (User Response)  */
        $this->text = $request->get( $this->text_field_name );

        /*  Get the USSD MSISDN value (User Phone)  */
        $this->msisdn = $request->get('phoneNumber');

        /*  Get the Session Id  */
        $this->session_id = $request->get('sessionId');

        /*  Get the Service Code  */
        $this->service_code = $request->get('serviceCode');

        $this->user['phone'] = [
            'calling_code' => substr($this->msisdn, 0, 3),
            'number' => substr($this->msisdn, 3, 8),
        ];

        /*  Defines the maximum number of items  */
        $this->maximum_cart_items = 5;
        $this->maximum_item_quantity = 5;

        $this->selected_products = [];
        $this->selected_variable_options = [];
    }

    public function getStores()
    {
        //  Check if the user is authourized to view all ussd stores
        if ($this->user->can('viewAll', UssdInterface::class)) {
            //  Get the ussd stores
            $ussd_stores = UssdInterface::paginate();

            //  Check if the ussd stores exist
            if ($ussd_stores) {
                //  Return an API Readable Format of the UssdInterface Instance
                return ( new \App\UssdInterface() )->convertToApiFormat($ussd_stores);
            } else {
                //  Not Found
                return oq_api_notify_no_resource();
            }
        } else {
            //  Not Authourized
            return oq_api_not_authorized();
        }
    }

    public function getStore($store_code)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $store_code)->first() ?? null;

        //  Check if the ussd store exists
        if ($ussd_store) {
            //  Check if the user is authourized to view the ussd store
            if ($this->user->can('view', $ussd_store)) {
                //  Return an API Readable Format of the UssdInterface Instance
                return $ussd_store->convertToApiFormat();
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
     *  OWNERSHIP RELATED RESOURCES  *
    *********************************/

    public function getUssdInterfaceOwner($store_code)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $store_code)->first() ?? null;

        //  Get the ussd store owner
        $owner = $ussd_store->owner ?? null;

        //  Check if the owner exists
        if ($owner) {
            //  Check if the user is authourized to view the ussd store owner
            if ($this->user->can('view', $ussd_store)) {
                //  Return an API Readable Format of the Owning Model Instance
                return $owner->convertToApiFormat();
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
     *  PRODUCT RELATED RESOURCES    *
    *********************************/

    public function getUssdInterfaceProducts($store_code)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $store_code)->first() ?? null;

        //  Get the ussd store products
        $products = $ussd_store->products()->paginate() ?? null;

        //  Check if the products exist
        if ($products) {
            //  Check if the user is authourized to view the ussd store products
            if ($this->user->can('view', $ussd_store)) {
                //  Return an API Readable Format of the Product Instance
                return ( new \App\Product() )->convertToApiFormat($products);
            } else {
                //  Not Authourized
                return oq_api_not_authorized();
            }
        } else {
            //  Not Found
            return oq_api_notify_no_resource();
        }
    }

    public function getUssdInterfaceProduct($store_code, $product_id)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $store_code)->first() ?? null;

        //  Get the ussd store product
        $product = $ussd_store->products()->where('products.id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {
            //  Check if the user is authourized to view the ussd store product
            if ($this->user->can('view', $ussd_store)) {
                //  Return an API Readable Format of the Product Instance
                return $product->convertToApiFormat();
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
     *  USSD DISPLAY MENU            *
    *********************************/

    /*  home()
     *  This is the first method we hit where all the USSD processes are
     *  sequencially handled as the user makes requests and receices
     *  responses.
     */
    public function home(Request $request)
    {
        /*  If we could not get the user's phone number  */
        if (!$this->hasProvidedPhoneDetails()) {

            
            /*  Notify the user to provide a mobile number first  */
            return $this->displayCustomErrorPage('Sorry, please provide you mobile number.');

        }

        /*  If the user has not responded to the landing page  */
        if (!$this->hasRespondedToLandingPage()) {

            /*  Display the landing page (The first page of the USSD Journey)  */
            $response = $this->displayLandingPage();

        /*  If the user has already responded to the landing page  */
        } else {

            /*  If the user already indicated that they want to provide a Store Code  */
            if ($this->wantsToEnterStoreCode()) {

                /*  If the user already provided the Store Code  */
                if ($this->hasProvidedStoreCode()) {

                    /*  Check if a USSD Interface using the store code provided exists  */
                    if ($this->isValidStoreCode()) {

                        /*  Allow the user to start shopping (At the specified store)  */
                        $response = $this->visitStore();

                    /*  If no store using the provided store code exists  */
                    } else {

                        /*  Notify the user that the store was not found  */
                        return $this->displayCustomErrorPage("Store was not found.\nMake sure you are using the correct store code");

                    }

                /*  If the user hasn't yet provided the store code  */
                } else {

                    $response = $this->displayEnterStoreCodePage();

                }

            /*  If the user already indicated that they want to search a store (They don't have a Store Code)  */
            } elseif ($this->wantsToSearchStore()) {

                /*  If the user already selected a specific category from the "Select Store Category Page"  */
                if ($this->hasSelectedStoreCategory()) {

                    /*  If the user already selected a specific store from the "Select Category Store Page"  */
                    if ($this->hasSelectedStoreFromCategory()) {
                        
                        /*  Make a redirect to the selected store  */
                        $this->redirectToStore();

                    } else {

                        /*  Display the "Select Category Store Page"  */
                        $response = $this->displayCategoryStores();

                    }

                } else {

                    /*  Display the "Select Store Category Page"  */
                    $response = $this->displayStoreCategoriesPage();

                }

            /*  Selected an option that does not exist  */
            } else {

                /*  Notify the user of incorrect option selected  */
                return $this->displayCustomErrorPage('You selected an incorrect option. Please try again');

            }
        }

        /*  Return the response to the user  */
        return response($response)->header('Content-Type', 'text/plain');
        //return response($response)->header('Content-Type', 'application/json');
        //  return response($response."\n\n".'characters: '.strlen($response))->header('Content-Type', 'text/plain');
    }

    /*  hasRespondedToLandingPage()
     *  Returns true/false of whether the user has responded to the 
     *  landing page before. The user must atleast have responded 
     *  once for this to be true
     */
    public function hasRespondedToLandingPage()
    {
        /*  Check if the user has responded to the landing page. If the text 
         *  returned is not empty then the user has responded otherwise the
         *  user has not responded at all
         */
        return (trim($this->text) != '') ? true : false;
    }


    /*  hasProvidedPhoneDetails()
     *  Returns true/false of whether the user has provided their mobile number
     */
    public function hasProvidedPhoneDetails()
    {
        /*  Get the MSISDN/Mobile number and check if it has been provided   
         *  Return true if it exists and false if it does not exist
         */
        return !empty($this->msisdn);
    }

    /*  displayLandingPage()
     *  This is the first page displayed when accessing the USSD.
     *  In this page we ask the user to either choose to enter
     *  a valid store code or search a store instead
     */
    public function displayLandingPage()
    {
        $response = "CON Find stores ka BONAKO,\nSelect (1) to enter the store code or (2) search for a store \n";
        $response .= "1. Enter store code \n";
        $response .= '2. Search popular stores';

        return $response;
    }

    /*  displayIssueConnectingToStorePage()
     *  This is the page displayed when a store existed during the session at some point 
     *  but for some reason we cannot seem to access it again. Maybe the store got deleted 
     *  while a user was shopping or mayb issues where encontered during an SQL Query.
     *  Whatever the case we show this page
     */
    public function displayIssueConnectingToStorePage()
    {
        return $this->displayCustomErrorPage('Sorry, we could not access/connect to the store. Please try again');
    }

    /*  displayCustomErrorPage()
     *  This is the page displayed when a problem was encountered and we want
     *  to end the session with a custom error message.
     */
    public function displayCustomErrorPage($error_message = '')
    {
        $response = 'END '.$error_message;

        return $response;
    }

    /*  displayEnterStoreCodePage()
     *  This is the page displayed when a user must enter the Store Code.
     */
    public function displayEnterStoreCodePage()
    {
        $response = 'CON Enter the store code to visit your local store';

        return $response;
    }

    /*  displayStoreCategoriesPage()
     *  This is the page displayed when a user must select a store category.
     *  A store category groups stores that operate in the same industry
     *  e.g Transport, Accomodation, e.t.c
     */
    public function displayStoreCategoriesPage()
    {
        $response = "CON Search by category\n";
        $response .= "1. Accomodation Services (12)\n";
        $response .= "2. Transport Services (4)\n";
        $response .= "3. Fast Food Services (18)\n";

        return $response;
    }

    /*  displayCategoryStores()
     *  This is the page displayed when a user must select a store from a category.
     *  Assuming the user selected the "Accomodation" category, they must select
     *  a store from that category here.
     */
    public function displayCategoryStores()
    {
        $response = "CON Category: Transport, Select option to visit\n";
        $response .= "1. Smiley Cabs\n";
        $response .= "2. Deluxe Cabs\n";

        return $response;
    }

    /*  displayStoreLandingPage()
     *  This is the first page displayed when accessing the any store.
     *  In this page we ask the user to select product to add to cart
     */
    public function displayStoreLandingPage()
    {
        /*  If this is the first visit to the store  */
        if ($this->visit == 1) {
            /*  Get the store details to show the the store name and description  */
            $response = 'CON '.$this->ussd_interface['name'].': '.$this->ussd_interface['description']."\n";

        /*  If this is not the first visit  */
        } else {
            /*  Show the store name and the number of products added to cart so far
             *  The number of products already added will be the number of visits
             *  to the store excluding the current visit. This will represent the
             *  number of past visitations since we select one item per visitation.
             */

            /*  Get total number of items already added to cart */
            $items_added_count = ($this->visit - 1);

            /*  Get total price of items already added to cart */
            $items_added_total = $this->currency.$this->convertToMoney($this->cart['grand_total']);

            $response = 'CON '.$this->ussd_interface['name'].': Add another item. ';
            $response .= 'You already added ('.($items_added_count).') '.($items_added_count == 1 ? 'item. ' : 'items. ')."\n";
            $response .= 'Cart Total: '.$items_added_total."\n";
        }

        /*  List the store products  */
        $response .= $this->getStoreLandingPageProducts();
        $response .= ($this->visit > 1) ? "0. Go Back (Previous Item)\n" : '';

        return $response;
    }

    /*  displayProductVariablePage()
     *  This is the page displayed when a user must select a product variable
     */
    public function displayProductVariablePage( $variant_attribute_name, $variant_attribute_options, $is_last_variant_page )
    {
        $response = "CON ".$this->selected_product->name.": Select an option\n";

        foreach( $variant_attribute_options as $key => $option ){

            $additional_variable_options = [$variant_attribute_name => $option];
            $product_variation = $this->getSelectedProductVariation( $additional_variable_options );

            /*  If we atleast have one variant avaialable  */
            if( $product_variation ){

                $option_number = $key + 1;
                $product_id = $product_variation['id'];
                $product_price = $product_variation['unit_price'];
                $product_on_sale = $this->isOnSale( $product_variation );
    
                $response .= $option_number.". ".$option;

                if( $is_last_variant_page ){

                    //  If this variation product is not out of stock
                    if ( $this->hasStock( $product_variation ) ) {
                    
                        /*  Check if the product has been added to the cart already  */
                        if ($this->isProductAddedToCart($product_id)) {

                            /*  Indicate that the product is in the cart already  */
                            $response .= " (added)";

                        /*  If the product hasn't been added to the cart already  */
                        } else {

                            /*  Show the product name, currency and price  */
                            $response .= $product_price ? ' -'. $this->currency.$this->convertToMoney($product_price) : ' (Not Available)';

                            /*  If the product is on sale then make an indication  */
                            $response .= ($product_on_sale ? ' (on sale)' : '');

                        }

                    //  Otherwise show this variation product is out of stock
                    } else {

                        /*  Show the product name, and indicate that this product is out of stock  */
                        $response .= " (out of stock)";

                    }

                }

                $response .= "\n";

            }

        }

        $response .= "0. Go Back\n";

        return $response;
    }

    public function getSelectedProductVariation( $additional_variable_options = []  )
    {
        $product_variations = $this->selected_product->variations();

        if( count( $additional_variable_options ) ){

            foreach($additional_variable_options as $option_name => $option_value){

                $product_variations = $product_variations->whereHas('variables', function (Builder $query) 
                        use( $option_name, $option_value ){
                            $query->where('name', $option_name)
                                ->where('value', $option_value);
                        }); 

            }

        }

        $previously_selected_variable_options = $this->selected_variable_options;

        if( count( $previously_selected_variable_options ) ){

            foreach($previously_selected_variable_options as $selected_variable_option){

                foreach($selected_variable_option as $option_name => $option_value){

                    /*  This is the selected variant option name and value.
                    *  For example if the user is selecting a T-shirt:
                    *
                    *  $option_name = size and $option_value = small
                    *  $option_name = color and $option_value = blue
                    *  $option_name = material and $option_value = cotton
                    */
                    $product_variations = $product_variations->whereHas('variables', function (Builder $query) 
                                    use( $option_name, $option_value ){
                                            $query->where('name', $option_name)
                                                ->where('value', $option_value);
                                        }); 
                }

            }

        }

        return $product_variations->first();
    }
    
    /*  displayProductQuantityPage()
     *  This is the page displayed when a user must select a product quantity
     */
    public function displayProductQuantityPage()
    {
        $response = 'CON Select your quantity (how many you want) for this item "'.$this->selected_product['name']."\"\n";
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
        $summary_text = $this->summarize('Total: '.$this->currency.$this->convertToMoney($this->cart['grand_total']).' Items: '.$this->cart['items_summarized_inline'], 100);
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
        $summary_text = $this->summarize('You are paying '.$this->currency.$this->convertToMoney($this->cart['grand_total']).' for '.$this->cart['items_summarized_inline'], 100);
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
        $summary_text = $this->summarize('You are paying '.$this->currency.$this->convertToMoney($this->cart['grand_total']).' for '.$this->cart['items_summarized_inline'], 100);
        $response = 'CON '.$summary_text.' using Airtime. You will be charged ('.$this->currency.$this->convertToMoney($this->getServiceFee()).") as a service fee. Please confirm\n";
        $response .= "1. Confirm\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrangeMoneyPaymentConfirmationPage()
     *  This is the page displayed when a user must confirm payment using Orange Money
     */
    public function displayOrangeMoneyPaymentConfirmationPage()
    {
        $summary_text = $this->summarize('You are paying '.$this->currency.$this->convertToMoney($this->cart['grand_total']).' for '.$this->cart['items_summarized_inline'], 100);
        $response = 'CON '.$summary_text.' using Orange Money. You will be charged ('.$this->currency.$this->convertToMoney($this->getServiceFee()).") as a service fee. Please confirm\n";
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
                $option_number = $key + 1;

                /*  Get the product name, currency symbol and price  */
                $product_id = trim($product['id']);
                $product_name = trim($product['name']);
                $product_price = $product['unit_price'];

                /*  Check if the product is on sale  */
                $product_on_sale = $this->isOnSale( $product );

                /*  Show this product with price only if:
                 *  1 - It has inventory and the quantity is greater than zero (otherwise it is out of stock)
                 *  2 - It does not have inventory (no stock taken)
                 */
                if ( $this->hasStock( $product ) ) {
                    
                    /*  Check if the product has been added to the cart already  */
                    if ($this->isProductAddedToCart($product_id)) {
                        /*  Show the product name, and indicate that the product is in the cart already  */
                        $response .= $option_number.'. '.$product_name." (added)\n";

                    /*  If the product hasn't been added to the cart already  */
                    } else {
                        /*  Show the product name, currency and price  */
                        $response .= $option_number.'. '.$product_name.' -'.$this->currency.$product_price;

                        /*  If the product is on sale then make an indication  */
                        $response .= ($product_on_sale ? ' (on sale)' : '')."\n";
                    }

                    //  Otherwise show this product as out of stock
                } else {
                    /*  Show the product name, and indicate that this product is out of stock  */
                    $response .= $option_number.'. '.$product_name." (out of stock)\n";
                }
            }
        } else {
            /*  If we don't have any products to list  */
            $response .= "\nSorry, no products today :)\n";
        }

        return $response;
    }

    public function hasStock( $product )
    {
        return (($product['stock_quantity'] > 0 && $product['has_inventory']) || !$product['has_inventory']);
    }

    public function isOnSale( $product )
    {
        return !empty($product['unit_sale_price']) ? true : false;
    }

    /*  wantsToEnterStoreCode()
     *  Returns true/false of whether the user wants to enter their Store Code
     */
    public function wantsToEnterStoreCode()
    {
        /*  If the user responded to the landing page (Level 1) with the option (1)
         *  then the user wants to enter their Store Code.
         */
        return  $this->completedLevel(1) && $this->getResponseFromLevel(1) == '1';
    }

    /*  hasProvidedStoreCode()
     *  Returns true/false of whether the user already provided the Store Code
     */
    public function hasProvidedStoreCode()
    {
        /*  First we must ensure that the user explicitly intended to provide a store
         *  code before checking if they already have provided anything at all
         */
        if ($this->wantsToEnterStoreCode()) {

            /*  If the user already responded to the "Enter Store Code Page" (Level 2)
             *  then the user wants to search for a store.
             */
            return  $this->completedLevel(2);

        }

        return false;
    }

    /*  getProvidedStoreCode()
     *  Returns the provided the Store Code
     */
    public function getProvidedStoreCode()
    {
        /*  If the user already responded to the "Enter Store Code Page" (Level 2)
         *  then we can return the Store Code that was provided
         */
        return  $this->getResponseFromLevel(2);
    }

    /*  isValidStoreCode()
     *  Returns true/false if a USSD Interface with the specified Store Code exists
     */
    public function isValidStoreCode()
    {
        /*  First we must ensure that the user explicitly provided a Store Code
         *  before checking if a store using that Store Code exists.
         */
        if ($this->hasProvidedStoreCode()) {

            /*  If the user already responded to the "Enter Store Code Page" (Level 2)
             *  then we can access the provided Store Code.
             */
            $store_code = $this->getProvidedStoreCode();

            /*  If we have a Store Code  */
            if ($store_code) {

                /*  Get the USSD Interface using the Store Code  */
                $store = $this->getUssdInterface($store_code);

                /*  If a store was found */
                if ($store) {

                    return true;

                }
            }
        }

        return false;
    }

    /*  wantsToSearchStore()
     *  Returns true/false of whether the user wants to search for a store
     */
    public function wantsToSearchStore()
    {
        /*  If the user responded to the landing page (Level 1) with the option (2)
         *  then the user wants to search for a store.
         */
        return  $this->completedLevel(1) && $this->getResponseFromLevel(1) == '2';
    }

    /*  hasSelectedStoreCategory()
     *  Returns true/false of whether the user has already selected a store category
     */
    public function hasSelectedStoreCategory()
    {
        /*  First we must ensure that the user explicitly intended to select a store
         *  category. The user must have indicated that they want to search a store.
         *  By default searching a store starts with selecting the search category
         */
        if ($this->wantsToSearchStore()) {
            /*  If the user responded to the second screen (Level 2) indicating
             *  a specific category to search for a store.
             */
            return  $this->completedLevel(2);
        }

        return false;
    }

    /*  hasSelectedStoreFromCategory()
     *  Returns true/false of whether the user has already selected a store
     *  after selecting a store category
     */
    public function hasSelectedStoreFromCategory()
    {
        /*  First we must ensure that the user explicitly selected a category.
         */
        if ($this->hasSelectedStoreCategory()) {
            /*  If the user responded to the third screen (Level 3) indicating
             *  a specific store of choice.
             */
            return  $this->completedLevel(3);
        }

        return false;
    }

    /*  getUssdInterface()
     *  Returns the store object that matches the store code provided
     */
    public function getUssdInterface($store_code = null)
    {
        if ($store_code) {
            /*  Get the USSD Interface that uses ussd store code  */
            return UssdInterface::where('code', $store_code)->first() ?? null;
        }
    }

    /*  redirectToStore()
     *  Forces a redirect in order to access a store using a Store Code
     */
    public function redirectToStore()
    {
        /*  MAKE REDIRECT -
         *  We need to update the TEXT Param with the store code of the selected store.
         *  After this we need to make a POST Request with the updated TEXT so that we can 
         *  access the wantsToEnterStoreCode() method since we would now have the store code. 
         *  This will allow the user to access the visitStore() once to store is verified
         *  with the isValidStoreCode() method. At that point the user will have gained
         *  access to the store and can start shopping.
         */

        /*  Step 1:
         *  First we must get the option number of the store category that was selected.
         *  We can use that option number to indentify the exact category that was selected.
         */
        $category_option = $this->getResponseFromLevel(2);

        /*  Get the categories from the database. 
         *  Categories::wherehas( ... only return categories linked to stores )
         */

        /*  Get selected category from the database.  */
        $selected_category = $categories[$category_option];

        /*  Step 2:
         *  Next we must get the option number of the store that was selected from the category.
         *  We can use that number to indentify the exact store from the category stores listed.
         */
        $store_option = $this->getResponseFromLevel(3);

        //  Get selected store from the database.
        $selected_store = $selected_category->stores[$store_option];

        /*  Redirect to store:
         *  $new_text represents the information provided by the user. We want to alter this information
         *  by removing any previous responses and replacing it with our custom response. The custom
         *  response we want to add will indicate that the user wants to provide a Store Code and will
         *  also indicate that the user have provided a store code. With this $new_text information we 
         *  can replace the TEXT Param value and make a POST Request to regain access to the USSD home()
         *  method with the correct information.
         * 
         *  An example of the $new_text can be "1*001" where 
         *  "1" represents that the first option (1) has been selected from the landing page and where
         *  "001" represents that the Store Code provided.
         *  
         */
        $new_text = '1*'.$selected_store->code;
        
        return $this->simulateUserReply($reply = $new_text, $replace_previous_replies = true);

    }


    public function calculateOffset()
    {
        $responses = [];

        $first_level_breakdown = explode('***', $this->text);

        foreach ($first_level_breakdown as $breakdown) {

            $second_level_breakdown = explode('**', $breakdown);

            foreach ($second_level_breakdown as $breakdown) {

                $third_level_breakdown = explode('*', $breakdown);

                foreach( $third_level_breakdown as $unit_response ){

                    if( !is_null($unit_response) && $unit_response !== '' ){
                    
                        array_push($responses, $unit_response);

                    }

                }

            }
        }
            
        $number_of_responses = count( $responses ) ;

        $offset = $number_of_responses - 3;

        return $offset;
    }

    /*  visitStore()
     *  This method allows the user to access the store they specified to view products and services.
     *  The method allows the user to view store details, contacts and also make a call to action.
     *  A call to action alloes the user to view and interact with the store and products and 
     *  services by adding to cart and making payments on checkout. It manages the entire 
     *  shopping experience.
     */
    public function visitStore($visit = 1)
    {
        /*  Allow the visit property to be accessd outside this method */
        $this->visit = $visit;

        if ($visit != 1) {

            /*  
             *  An offset is only required on every visit except the first. It allows us to target the correct
             *  product in each visit as we return to the store several times. Once we target the right product
             *  we can then see how far along the user is with that product e.g have they selected variable?
             *  Have they added the quantity?, do they want to pay? ... The offset helps us know which
             *  product we are now focusing on for each visit.
             */

            /* 
             *  We know that everytime we select a product we need to provide 3 levels of information describing
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

            $this->offset = $this->calculateOffset();

        } else {
            
            $this->offset = 0;

        }

        /*  Get the store code the user provided from the "Enter Store Code Page" (Level 2).
         *  We can use the store code to get the USSD Interface. The USSD Interface can
         *  then get us the exact store
         */
        $store_code = $this->getProvidedStoreCode();

        /*  Get the Ussd Interface
         *
         *  The interface contains the USSD screen name, description and its "live_mode" 
         *  status to inform us whether to allow the user access the USSD landing page. 
         *  The interface also gives us access to the owning merchant such as a store, 
         *  which allows us to access the merchant products, discounts, taxes, e.t.c
         */
        $this->ussd_interface = $this->getUssdInterface($store_code);

        /*  Get the Ussd Interface owning store only if the interface exists */
        $this->store = $this->ussd_interface ? $this->ussd_interface->owner : null;

        /*  Get the Ussd Interface products only if the interface exists */
        $this->products = $this->ussd_interface ? $this->ussd_interface->products : null;
        
        /*  Get the store currency symbol or currency code if only if the store exists */
        $this->currency = $this->store ? ($this->store['currency']['symbol'] ?? $this->store['currency']['code']) : null;

        /*  If no store using the provided store code was found. Maybe the store
         *  was deleted or we could not gain access to it for some reason
         */
        if (!$this->store) {

            /*  Notify the user that we have issues connecting to the store  */
            return $this->displayIssueConnectingToStorePage();

        }

        /*  If the user added more items than is allowed to their cart,
         *  (has exceeded the maximum items allowed)
         */
        if ($this->hasExceededMaximumItems()) {

            $allowed_cart_items = $this->maximum_cart_items.($this->maximum_cart_items == 1 ? ' item' : ' items');

            /*  Notify the user that they have exceeded that maximum items allowed in the cart  */
            return $this->displayCustomErrorPage('Sorry, you are only allowed to add a maximum of '.$allowed_cart_items);

        }

        /*  If the user was visiting the store again and was on the "Store Landing Page (Select A Product Page)"
         *  but wanted to go back to the "Previous Product Cart Summary Page"
         */
        if ($this->hasVisitedBefore() && $this->wantsToGoToPreviousAddedProductPage()) {

            /*  Go back only (1) Level to the "Previous Product Cart Summary Page"  */
            $response = $this->goBack($how_many_times = 1);

        /*  If the user already selected a product  */
        } elseif ($this->hasSelectedProduct()) {

            /*  Make sure the selected product is always available from here on  */
            $this->selected_product = $this->getSelectedProduct();

            /*  If the selected product has variables  */
            if ($this->hasVariables()) {

                /*  Assumming the product has three different variables being "Size", "Color" and "Material". 
                 *  This means that we need to show the user 3 different pages which will show the variable
                 *  options e.g ['Small', 'Medium', 'Large'] for variable 1, ['Blue', 'Red'] for variable 2 
                 *  and ['Cotton', 'Nylon'] for variable 3. If $variant_attribute_offset=1 then this is the 
                 *  first variant attribute page where the user selects the product size.
                 */
                $variant_attributes = $this->selected_product->variant_attributes ?? [];
                $variant_attribute_offset = 1;

                /*  Foreach product variant page number  */
                foreach( $variant_attributes as $variant_attribute_name => $variant_attribute_options ){
                    
                    /*  Adjust the offset by including the $variant_attribute_offset since we are now 
                     *  selecting variable options.
                     */
                    $this->offset = $this->offset + 1;

                    /*  If the user has not already selected an option for this variable page.  */
                    if( !$this->hasSelectedProductVariantPageOption() ){
                        
                        /*  Determine if this is the last variant attribute in the loop */
                        $is_last_variant_page = ($variant_attribute_offset == count($variant_attributes));

                        /*  Display the menu for the user to select a product variable */
                        return $this->displayProductVariablePage( $variant_attribute_name, $variant_attribute_options, $is_last_variant_page);

                    }else{

                        /*  If the user was on the "Select Variant Options Page" but wants to go back
                         *  to the "Select Product Page (Store Landing Page)"
                         */
                        if ($this->wantsToGoToPreviousPageFromProductVariantPage()) {
                            
                            /*  Go back only (1) Level to the "Select Product Page (Store Landing Page)"  */
                            return $this->goBack($how_many_times = 1);

                        /*  If the user already selected the product quantity  */
                        }else{

                            /*  Get the selected option for this variable page.  */
                            $selected_option = $this->getSelectedVariableOption( $variant_attribute_name, $variant_attribute_options );
                        
                            array_push($this->selected_variable_options, $selected_option);

                        }

                    }

                    $variant_attribute_offset = ++$variant_attribute_offset;

                }
                

                /*  Make sure the selected product variation is always available from here on  */
                $this->selected_product = $this->getSelectedProductVariation();

            }

            /*  If the user already selected the product quantity  */
            if ($this->hasSelectedProductQuantity()) {

                /*  If the user was on the "Select Product Quantity Page" but wants to go back
                 *  to the "Select Variable Page" (if its a variable product) or go back to the
                 *  "Select Product Page (Store Landing Page)" (if its a simple product)
                 */
                if ($this->wantsToGoToPreviousPageFromQuantityPage()) {

                    /*  Go back only (1) Level to the "Select Variant Options Page"  */
                    $response = $this->goBack($how_many_times = 1);
                
                } elseif ($this->isValidProductQuantity()) {

                    /*  Update the selected product quantity */
                    $this->selected_product['quantity'] = $this->getSelectedProductQuantity();

                    /* Add the selected product to the rest of the other selected products */
                    array_push($this->selected_products, $this->selected_product);

                    /*  Get the cart and make sure the cart is always available from here on */
                    $this->cart = $this->getCart();

                    /*  If the user was on the "Cart Summary Page" but wants to go back
                     *  to the "Select Product Quantity Page"
                     */
                    if ($this->hasVariables() && $this->wantsToGoToPreviousPageFromCartSummaryPage()) {

                        /*  Go back only (1) Level to the "Select Product Quantity Page"  */
                        $response = $this->goBack($how_many_times = 1);

                    /*  If the user already selected that they want to add another product  */
                    } elseif ($this->wantsToAddAnotherProduct()) {
                        
                        /*  Revisit the store to select another product  */
                        $response = $this->revisitStore();

                    /*  If the user already selected that they want to checkout and pay  */
                    } elseif ($this->wantsToPay()) {

                        /*  If the user was on the "Select Payment Method Page" but wants to go back
                         *  to the "Cart Summary Page"
                         */
                        if ($this->wantsToGoToPreviousPageFromPaymentOptionsPage()) {

                            /*  Go back only (1) Level to the "Cart Summary Page"  */
                            $response = $this->goBack($how_many_times = 1);

                        /*  If the user already selected the payment method  */
                        } elseif ($this->hasSelectedPaymentMethod()) {

                            /*  If the user was on the "Confirm Payment Page" but wants to go back
                             *  to the "Select Payment Method Page"
                             */
                            if ($this->wantsToGoToPreviousPageFromPaymentConfirmationPage()) {

                                /*  Go back only (1) Level to the "Select Payment Method Page"  */
                                $response = $this->goBack($how_many_times = 1);

                            /*  If the user already selected that they want to pay with Airtime  */
                            } elseif ($this->wantsToPayWithAirtime()) {

                                /*  If the user already confirmed that they want to pay with Airtime  */
                                if ($this->hasConfirmedPaymentWithAirtime()) {

                                    /*  Process the order using Airtime  */
                                    $response = $this->procressOrder($payment_method = 'airtime');

                                /*  If the user has not already confirmed that they want to pay with Airtime  */
                                } else {

                                    /*  Show the user the Airtime payment confirmation page  */
                                    $response = $this->displayAirtimePaymentConfirmationPage();

                                }

                                /*  If the user already selected that they want to pay with Orange Money  */
                            } elseif ($this->wantsToPayWithOrangeMoney()) {

                                /*  If the user already confirmed that they want to pay with Orange Money  */
                                if ($this->hasConfirmedPaymentWithOrangeMoney()) {

                                    /*  If the user provided a valid Orange Money pin  */
                                    if ($this->isValidOrangeMoneyPin()) {

                                        /*  Process the order using Orange Money  */
                                        $response = $this->procressOrder($payment_method = 'orange_money');

                                    } else {

                                        /*  Notify the user of incorrect pin  */
                                        $response = $this->displayCustomErrorPage('Incorrect pin provided. Please try again');

                                    }

                                } else {

                                    /*  Show the user the Orange Money payment confirmation page  */
                                    $response = $this->displayOrangeMoneyPaymentConfirmationPage();

                                }

                                /*  If the user selected an option that does not exist  */
                            } else {

                                /*  Notify the user of incorrect method of payment selected  */
                                $response = $this->displayCustomErrorPage('You selected an incorrect method of payment. Please try again');

                            }

                            /*  If the user has not already selected the payment method  */
                        } else {
                            /*  Show the user the payment options page  */
                            $response = $this->displayPaymentOptionsPage();
                        }

                        /*  Otherwise they haven't made up their mind yet  */
                    } else {

                        /*  Show the user the cart summary page with options to decide what to do next  */
                        $response = $this->displayCartSummaryPage();
                        
                    }
                } else {
                    /*  Notify the user of provide a valid quantity  */
                    $response = $this->displayCustomErrorPage('The product quantity you provided is not available');
                }
            } else {
                /*  Show the user the product quantity selection page  */
                $response = $this->displayProductQuantityPage();
            }

            /*  If the user has not selected any product  */
        } else {
            /*  Show the user the store landing page  */
            $response = $this->displayStoreLandingPage();
        }

        return $response;
    }

    /*  revisitStore()
     *
     *   Allows the user the ability to revisit the store to select another product
     */
    public function revisitStore()
    {
        return $this->visitStore($this->visit + 1);
    }

    public function hasVisitedBefore()
    {
        /*  Checks if the user has visited the store before. Anymore than one
         *  confirms that the user has visited the store before.
         */
        return  $this->visit > 1;
    }

    public function wantsToGoToPreviousAddedProductPage()
    {
        /*  Get the selected product option from the Store landing page (Level 3)
         *  If the option equals "0" then the user wants to go to the previous
         *  added product
         */
        return  $this->completedLevel(3 + $this->offset) && $this->getResponseFromLevel(3 + $this->offset) == '0';
    }

    public function hasSelectedProduct()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 3)
         *  by selecting a product.
         */
        return  $this->completedLevel(3 + $this->offset);
    }

    public function getSelectedProduct()
    {
        /*  Get the selected product option from the "Store Landing Page (Select A Product Page)"
         *  (Level 3). We can use the selected option to retrieve the product.
         */

         /*  Get the selected option and convert it to an interger  */
        $selected_product_option = (int) $this->getResponseFromLevel(3 + $this->offset);

        /*  If we have a selected product option (e.g 1, 2 or 3)  */
        if ($selected_product_option) {

            /*  Retrieve the actual product that was selected. Note that the user would have
             *  replied "1" to select the first product on the list. However the first
             *  product on "$this->products" variable is of index "0", this means we need
             *  to always subtract "1" from the user reply to access the correct product.
             */
            return $this->products[$selected_product_option - 1];

        }
    }

    public function hasSelectedProductVariantPageOption()
    {
        /*  If the user already responded to the "Select Product Variable Option Page" (Level 4++)
         *  by selecting a specific product variant option.
         */
        return  $this->completedLevel(3 + $this->offset);
    }

    public function wantsToGoToPreviousPageFromProductVariantPage()
    {
        /*  If the user is currently at the "Select Product Variable Page" (Level 4++)
         *  but wants to go back
         */
        return  $this->completedLevel(3 + $this->offset) && $this->getResponseFromLevel(3 + $this->offset) == '0';
    }

    public function getSelectedVariableOption( $variant_attribute_name, $variant_attribute_options )
    {
        /*  If the user already responded to the "Select Product Variables Page" (Level 4++)
         *  by selecting a specific product variant option. We can return the selected option.
         */

        /*  Get the selected number option e.g 1, 2 or 3  */
        $selected_number_option = $this->getResponseFromLevel(3 + $this->offset);

        /*  Get the selected attribute option e.g Small, Medium or Large  */
        $selected_attribute_option = $variant_attribute_options[ $selected_number_option - 1 ];

        /*  Get the selected attribute name and option e.g ['size' => 'Small'] or ['color' => 'Blue']  */
        return [ $variant_attribute_name => $selected_attribute_option ];
    }

    public function hasVariables()
    {
        /*  If we have the actual product that was selected  */
        if ($this->selected_product) {
            
            /*  Determine if the selected product has variants  */
            return  $this->selected_product['allow_variants'] == true && $this->selected_product->variations()->count();
            
        }

        return false;
    }

    public function hasSelectedProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a specific product quantity.
         */
        return  $this->completedLevel(4 + $this->offset);
    }

    public function getSelectedProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a specific product quantity. We can return this quantity
         */
        return  $this->getResponseFromLevel(4 + $this->offset);
    }

    public function wantsToGoToPreviousPageFromQuantityPage()
    {
        /*  If the user is currently at the "Select Product Quantity Page" (Level 5)
         *  but wants to go back
         */
        return  $this->completedLevel(4 + $this->offset) && $this->getResponseFromLevel(4 + $this->offset) == '0';
    }

    public function isValidProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a quantity. Get the product quantity provided.
         */
        $quantity_provided = (int) $this->getResponseFromLevel(4 + $this->offset);

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
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '0';
    }

    public function wantsToPay()
    {
        /*  If the user already responded to the Cart summary page (Level 6)
         *  by selecting option (1) for pay now.
         */
        return  $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '1';
    }

    public function wantsToGoToPreviousPageFromPaymentOptionsPage()
    {
        /*  Get the selected option from the "Selected Payment Method Page" (Level 7)
         *  If the option equals "0" then the user to go back to the "Cart Summary Page",
         *  the previous page.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '0';
    }

    public function hasSelectedPaymentMethod()
    {
        /*  If the user already responded to the Select payment method page (Level 7)
         *  by selecting a specific payment method option.
         */
        return  $this->completedLevel(6 + $this->offset);
    }

    public function wantsToPayWithAirtime()
    {
        /*  If the user already responded to the Select payment method page (Level 6)
         *  by selecting option (1) for pay using Airtime.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '1';
    }

    public function wantsToPayWithOrangeMoney()
    {
        /*  If the user already responded to the Select payment method page (Level 6)
         *  by selecting option (2) for pay using Orange Money.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '2';
    }

    public function wantsToGoToPreviousPageFromPaymentConfirmationPage()
    {
        /*  Get the selected option from the "Confirm Payment Page" (Level 8). If the
         *  option equals "0" then the user to go back to the "Select Payment Method Page",
         *  the previous page.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '0';
    }

    public function hasConfirmedPaymentWithAirtime()
    {
        /*  If the user already responded to the "Confirm Payment Using Airtime Page" (Level 7)
         *  by selecting option (1) to confirm payment.
         */
        return  $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '1';
    }

    public function hasConfirmedPaymentWithOrangeMoney()
    {
        /*  If the user already responded to the Confirm payment using Orange Money page (Level 7)
         *  by selecting a specific option.
         */
        return  $this->completedLevel(7 + $this->offset);
    }

    public function isValidOrangeMoneyPin()
    {
        /*  If the user already responded to the "Confirm payment using Orange Money page" (Level 7)
         *  by selecting a specific option. Then we can capture the Orange Money pin they provided
         */
        $orange_money_pin = $this->getResponseFromLevel(7 + $this->offset);

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
         *  Note that the "1*001" only represents the path to the store and the store code
         *  used to access the actual store. It does not relate to any product selection
         *  or description.
         *
         *  1 = Level 1 option (Enter store code option)
         *  001 = Level 2 option (The store code provided)
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
             *  non-product related information such as the store code. We must remove
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
                unset($user_responses[1]);  //  Removes the "store code response"

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
         * 
         *  The $items variable will hold all the item ids and quantities:
         *
         *  $items = [
                ['id'=>1, quantity=>2],
                ['id'=>2, quantity=>3]
            ]
         */
        $items = [];

        /*  Foreach selected product */
        foreach ($this->selected_products as $selected_product) {
            
            /*  Get the product id and quantity */
            array_push($items, ['id' => $selected_product['id'], 'quantity' => $selected_product['quantity']]);

        }

        /*  Retrieve and return the cart details relating to the merchant and items provided  */
        return ( new \App\MyCart() )->getCartDetails($this->store, $items);
    }

    public function getUserResponses($text = null)
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

        $responses = explode('*', $text ?? $this->text);

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

    public function skipVariableSelection()
    {
        /*  Since this product does not have a variable but we are still forced to give an option,
         *  we can reply with "0" as an indication that this product has no variable that was selected
         *  since its a simple product just like how a user can reply with an option 1, 2 or 3 if a product
         *  has variables. This will allow both simple and variable products to properly transition
         *  through different levels without messing up the next sequencial steps. To simulate a user
         *  replying with the option "0" we use the following method:
         */
        return $this->simulateUserReply($reply = '0', $replace_previous_replies = false);
    }

    public function simulateUserReply($reply = null, $replace_previous_replies = false)
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
                $reply = '*'.implode('*', $reply);

            /*  If an array was not provided - meaning we only have only one reply */
            } else {

                $reply = '*'.$reply;

            }

            /*  Retrieve the current url without any query strings  */
            $url = url()->current();

            /*  Retrieve all of the current Post Request Form Data as an associative array
             *  ['TEXT' => '1*001*2', 'MSISDN' => '26775993221', e.t.c]
             */
            $url_params = request()->all();

            if( $replace_previous_replies ){

                /*  Replace the current value of the TEXT Param with the new value. This is simulating 
                 *  a user providing new information and wants us to ignore their previous responses
                 *  In this case we remove all past information they have already provided and 
                 *  replace it all with the new reply.
                 */

                $url_params[$this->text_field_name] = $reply;

            }else{

                /*  Append the new value to the current value of the TEXT Param. This is simulating 
                 *  a user continuing to provide information supporting their previous responses
                 *  In this case we do not remove any past information they have already provided. 
                 *  We only add a new reply.
                 */
                $url_params[$this->text_field_name] .= $reply;

            }

            /*  Make a POST Request with the updated Form Data. e.g
             *  Before TEXT=1*001*1
             *  After  TEXT=1*001*1*3
             *
             *  Making a POST Request here conviniently allows us to revisit 
             *  the USSD endpoint with our revised/updated information
             */

            /*  Return the POST Request response  */
            return $this->makePostRequest($url, $url_params);

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
        $url = url()->current();

        /*  Retrieve all of the current Post Request Form Data as an associative array
         *  ['TEXT' => '1*001*2', 'MSISDN' => '26775993221', e.t.c]
         */
        $url_params = request()->all();

        /*  Foreach time we should go back. */
        for ($x = 0; $x <= $how_many_times; ++$x) {

            /*  Get the original TEXT responses. */
            $original_text = $url_params[$this->text_field_name];

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
            $url_params[$this->text_field_name] = $updated_text;

        }

        /*  Step 2:
         *  Make a POST Request with the updated Form Data. e.g
         *  Before TEXT=1*001*1*3
         *  After  TEXT=1*001*1
         *
         *  Making a POST Request here conviniently allows us to revisit 
         *  the USSD endpoint with our revised/updated information
         */

        /*  Return the POST Request response  */
        return $this->makePostRequest($url, $url_params);

    }

    public function makePostRequest($url, $url_params = [])
    {
        /*  Retrieve the HTTP Client  */
        $http = new \GuzzleHttp\Client();

        /*  Make a POST Request with the updated information  */
        $response = $http->post( $url, [
            'form_params' => $url_params
        ]);

        /*  Return the POST Request response  */
        return $response->getBody();
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

    public function hasExceededMaximumItems()
    {
        /*  If the number of store visits exceeds the maximum cart items that we can have then
         *  we have exceeded the limit of items we are allowed to checkout with. In this case
         *  lets imagine the number of store visits is also the number of items we have or
         *  want to checkout with. Then:
         *
         *  Visit=1 has  Items=1  and if Max Items=2 (Max Not Exceeded)
         *  Visit=2 has  Items=2  and if Max Items=2 (Max Not Exceeded)
         *  Visit=3 has  Items=3  and if Max Items=2 (Max Exceeded)
         *
         *  Therefore we exceed the maximum items when the number of visits are strickly
         *  greater than the maximum cart items
         *
         *  Therefore if the the items are more than the maximum cart items allowed this 
         *  method will return true.
         */
        return $this->visit > $this->maximum_cart_items;
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
