<?php

namespace App\Http\Controllers\Api;

use App\UssdInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdController extends Controller
{
    private $user;
    private $text;
    private $cart;
    private $store;
    private $visit;
    private $offset;
    private $product;
    private $products;
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
            'number' => substr($this->msisdn, 3, 8)
        ];  

        /*  Defines the maximum number of items  */
        $this->maximum_cart_items = 5;
        $this->maximum_item_quantity = 5;

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

    public function getStore($ussd_code)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $ussd_code)->first() ?? null;

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

    public function getUssdInterfaceOwner($ussd_code)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $ussd_code)->first() ?? null;

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

    public function getUssdInterfaceProducts($ussd_code)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $ussd_code)->first() ?? null;

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

    public function getUssdInterfaceProduct($ussd_code, $product_id)
    {
        //  Get the ussd store
        $ussd_store = UssdInterface::where('code', $ussd_code)->first() ?? null;

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
     *  This is the first method we hit were all the USSD processes are
     *  sequencially handled as the user makes requests and receices
     *  responses.
     */
    public function home(Request $request)
    {
        /*  If the user has not responded to the landing page  */
        if( !$this->hasResponded() ){

            /*  Display the landing page (The first page of the USSD)  */
            $response = $this->displayLandingPage();

        /*  If the user has already responded to the landing page  */
        }else{

            /*  If the user already indicated to provide a ussd code  */
            if( $this->wantsToEnterUssdCode() ){

                /*  If the user already provided the ussd code  */
                if( $this->hasProvidedUssdCode() ){

                    /*  Check if a store using the ussd code provided exists  */
                    if( $this->isValidUssdCode() ){

                        /*  Allow the user to start shopping (At the store specified)  */
                        $response = $this->visitStore();

                    /*  If no store using the provided ussd code exists  */
                    }else{

                        $this->displayStoreDoesNotExistPage();

                    }                    

                /*  If the user hasn't yet provided the ussd code  */
                }else{

                    $response = $this->displayEnterUssdCodePage();

                }

            /*  If the user already indicated to search a store (They don't have a ussd code)  */
            }else if( $this->wantsToSearchStore() ){

                /*  If the user already selected a category  */
                if( $this->hasSelectedStoreCategory() ){

                    /*  If the user already selected a specific store from the category list  */
                    if( $this->hasSelectedStoreFromCategory() ){

                        /*  Make a redirect to the store specified  */
                        $this->redirectToStore();

                    }else{

                        $response = $this->displayCategoryStores();

                    }

                }else{

                    $response = $this->displayStoreCategoriesPage();

                }

            /*  Selected an option that does not exist  */
            }else{

                return $this->displayCustomErrorPage('You selected an incorrect option. Please try again');

            }

        }

        /*  Return the response to the user  */
        //  return response($response."\n\n".'characters: '.strlen($response))->header('Content-Type', 'text/plain');
        return response($response."\n\n".'characters: '.strlen($response))->header('Content-Type', 'application/json');
        
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

    /*  displayLandingPage()
     *  This is the first page displayed when accessing the USSD. 
     *  In this page we ask the user to either choose to enter 
     *  a valid ussd code or search a store instead
     */
    public function displayLandingPage(){

        $response = "CON Please enter the ussd code or search for a store \n";
        $response .= "1. Enter ussd code \n";
        $response .= '2. Search store';

        return $response;

    }

    /*  displayStoreDoesNotExistPage()
     *  This is the page displayed when a store is not found. 
     */
    public function displayStoreDoesNotExistPage()
    {

        return $this->displayCustomErrorPage("Store was not found.\nMake sure you are using the correct ussd code");

    }

    /*  displayIssueConnectingToStorePage()
     *  This is the page displayed when a store existed during the session but
     *  we cannot seem to access it again. Maybe the store got deleted while a
     *  user was shopping or issues where encontered during a query 
     */
    public function displayIssueConnectingToStorePage()
    {

        return $this->displayCustomErrorPage("Sorry, we could not access/connect to the store. Please try again");

    }

    /*  displayCustomErrorPage()
     *  This is the page displayed when a problem was encountered and we want
     *  to end the session with a custom error message.
     */
    public function displayCustomErrorPage($error_message)
    {
        $response = "END ".$error_message;

        return $response;
    }
    

    /*  displayEnterUssdCodePage()
     *  This is the page displayed when a user must enter the ussd code. 
     */
    public function displayEnterUssdCodePage()
    {
        $response = 'CON Enter the ussd code to visit your local store';

        return $response;
    }

    /*  displayStoreCategoriesPage()
     *  This is the page displayed when a user must select a store category
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
     *  This is the page displayed when a user must select a store from a category
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
    public function displayStoreLandingPage(){

        /*  If this is the first visit to the store  */
        if($this->visit == 1){

            /*  Get the store details to show the the store name and description  */
            $response = "CON " . $this->ussd_interface['name'].": ". $this->ussd_interface['description']."\n";

        /*  If this is not the first visit  */
        }else{

            /*  Show the store name and the number of products added to cart so far 
             *  The number of products already added will be the number of visits
             *  to the store excluding the current visit. This will represent the
             *  number of past visitations since we select one item per visitation.
             */

            /*  Get total number of items already added to cart */
            $items_added_count = ($this->visit - 1);

            /*  Get total price of items already added to cart */
            $items_added_total = $this->cart['currency']['code'].$this->cart['grand_total'];

            $response = "CON ".$this->ussd_interface['name'].": Add another item. ";
            $response .= "You already added (".($items_added_count).") ".($items_added_count == 1 ? 'item. ': 'items. ')."\n";
            $response .= "Cart Total: ".$items_added_total."\n";

        }

        /*  List the store products  */
        $response .= $this->getStoreLandingPageProducts();
        $response .= ($this->visit > 1) ? "0. Go Back (Previous Item)\n" : "";
        

        return $response;

    }

    /*  displayProductVariablePage()
     *  This is the page displayed when a user must select a product variable
     */
    public function displayProductVariablePage()
    {
        $response = "CON Product 1: Select an option\n";
        $response .= "1. Variable 1\n";
        $response .= "2. Variable 2\n";
        $response .= "3. Variable 3\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayProductQuantityPage()
     *  This is the page displayed when a user must select a product quantity
     */
    public function displayProductQuantityPage()
    {
        $response = "CON Select your quantity (how many you want) for this item \"".$this->product['name']."\"\n";
        $response .= "1. Select between 1 and ".$this->maximum_item_quantity."\n";
        $response .= "0. Go Back\n";
        

        return $response;
    }

    /*  displayCartSummaryPage()
     *  This is the page displayed when a user to show them a summary of what they
     *  are about to purchase at that point in time.
     */
    public function displayCartSummaryPage()
    {   
        $summary_text = $this->summarize("Total: ".$this->cart['currency']['code'].$this->cart['grand_total']." Items: ".$this->cart['items_inline'], 100);
        $response = "CON ".$summary_text."\n";
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
        $response .= $this->canAddMoreItems() ? "Enter * to add another item\n" : "";

        return $response;
    }

    /*  displayPaymentOptionsPage()
     *  This is the page displayed when a user must select a payment method
     */
    public function displayPaymentOptionsPage()
    {
        $summary_text = $this->summarize("You are paying ".$this->cart['currency']['code']. $this->cart['grand_total']." for ". $this->cart['items_inline'], 100);
        $response = "CON " . $summary_text. ". Select payment method\n";
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
        $summary_text = $this->summarize("You are paying ".$this->cart['currency']['code']. $this->cart['grand_total']." for ". $this->cart['items_inline'], 100);
        $response = "CON ".$summary_text. " using Airtime. You will be charged (".$this->cart['currency']['code'].$this->getServiceFee().") as a service fee. Please confirm\n";
        $response .= "1. Confirm\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayOrangeMoneyPaymentConfirmationPage()
     *  This is the page displayed when a user must confirm payment using Orange Money
     */
    public function displayOrangeMoneyPaymentConfirmationPage()
    {
        $summary_text = $this->summarize("You are paying ".$this->cart['currency']['code']. $this->cart['grand_total']." for ". $this->cart['items_inline'], 100);
        $response = "CON ".$summary_text. " using Orange Money. You will be charged (".$this->cart['currency']['code'].$this->getServiceFee().") as a service fee. Please confirm\n";
        $response .= "1. Enter pin to confirm\n";
        $response .= "0. Go Back\n";

        return $response;
    }

    /*  displayPaymentSuccessPage()
     *  This is the page displayed when a user made a payment and it was successful.
     *  We display a success message as well as the order reference number
     */
    public function displayPaymentSuccessPage( $order = null )
    {
        $response = "CON Payment completed successfully. You will receive your payment confirmation details via SMS. ".
        $response = "Refer to your Order Reference Number #1270012 when receiving your items. Thank you :)\n";
        $response .= "1. Continue shopping\n";
        $response .= "2. Exit\n";;

        return $response;
    }

    /*  displayPaymentFailedPage()
     *  This is the page displayed when a user made a payment and it failed.
     */
    public function displayPaymentFailedPage( $error_message = '' )
    {

        return $this->displayCustomErrorPage('Sorry, payment failed. '.$error_message.' Try again.');

    }
    
    public function getServiceFee()
    {
        $cartTotal = $this->cart['grand_total'];

        if( $cartTotal >= 30 ){

            return 5.00;

        }elseif( $cartTotal > 15 ){

            return 1.50;

        }else{

            return 0.60;

        }
    }
    
    public function getStoreLandingPageProducts()
    {
        $response = '';

        /*  If we have any products  */
        if( count( $this->products ) ){

            /*  List the products available  */
            foreach($this->products as $key => $product){

                $option = $key + 1;

                /*  Get the product name, currency symbol and price  */
                $product_id = trim($product['id']);
                $product_name = trim($product['name']);
                $currency_symbol = $product['currency']['symbol'] ?? $product['currency']['code'];
                $product_price = $product['unit_price'];

                /*  Check if the product is on sale  */
                $product_on_sale = !empty($product['unit_sale_price']) ? true : false;

                /*  Show this product with price only if:
                 *  1 - It has inventory and the quantity is greater than zero (otherwise it is out of stock)
                 *  2 - It does not have inventory (no stock taken)
                 */
                if( ($product['stock_quantity'] > 0 && $product['has_inventory']) || !$product['has_inventory']){

                    /*  Check if the product has been added to the cart already  */
                    if( $this->isProductAddedToCart( $product_id ) ){

                        /*  Show the product name, and indicate that the product is in the cart already  */
                        $response .= $option.". ". $product_name." (added)\n";

                    /*  If the product hasn't been added to the cart already  */
                    }else{

                        /*  Show the product name, currency and price  */
                        $response .= $option.". ". $product_name." -".$currency_symbol . $product_price;

                        /*  If the product is on sale then make an indication  */
                        $response .= ($product_on_sale ? ' (on sale)': '')."\n";

                    }

                //  Otherwise show this product as out of stock
                }else{

                    /*  Show the product name, and indicate that this product is out of stock  */
                    $response .= $option.". ". $product_name." (out of stock)\n";
                }
            }
        }else{

            /*  If we don't have any products to list  */
            $response .= "\nSorry, no products today :)\n";

        }

        return $response;

    }
    
    /*  wantsToEnterUssdCode()
     *  Returns true/false of whether the user wants to enter their ussd code.
     */
    public function wantsToEnterUssdCode(){

        /*  If the user responded to the landing page (Level 1) with the option (1)
         *  then the user wants to enter their ussd code. 
         */
        return ( $this->completedLevel(1) && $this->getResponseFromLevel(1) == '1' );

    }

    /*  hasProvidedUssdCode()
     *  Returns true/false of whether the user already provided the ussd code
     */
    public function hasProvidedUssdCode(){

        /*  First we must ensure that the user explicitly intended to provide a store
         *  code before checking if they already have provided anything at all
         */
        if( $this->wantsToEnterUssdCode() ){

            /*  If the user already responded to the Enter ussd code page (Level 2)
             *  then the user wants to search for a store. 
             */
            return ( $this->completedLevel(2) );

        }

        return false;

    }

    /*  isValidUssdCode()
     *  Returns true/false of a store with the specified ussd code exists
     */
    public function isValidUssdCode(){

        /*  First we must ensure that the user explicitly provided a ussd code
         *  before checking if a store using that ussd code exists
         */
        if( $this->hasProvidedUssdCode() ){

            /*  If the user already responded to the Enter ussd code page (Level 2)
             *  then we can access the ussd code
             */
            $ussd_code = $this->getResponseFromLevel(2);

            if( $ussd_code ){

                /*  Get the store using the ussd code  */
                $store = $this->getUssdInterface($ussd_code);

                /*  If a store was found */
                if( $store ){

                    return true;

                }

            }

        }

        return false;

    }

    /*  wantsToSearchStore()
     *  Returns true/false of whether the user wants to search for a store
     */
    public function wantsToSearchStore(){

        /*  If the user responded to the landing page (Level 1) with the option (2)
         *  then the user wants to search for a store. 
         */
        return ( $this->completedLevel(1) && $this->getResponseFromLevel(1) == '2' );

    }

    /*  hasSelectedStoreCategory()
     *  Returns true/false of whether the user has already selected a store category
     */
    public function hasSelectedStoreCategory(){

        /*  First we must ensure that the user explicitly intended to select a store 
         *  category. The user must have indicated that they want to search a store. 
         *  By default searching a store starts with selecting the search category
         */
        if( $this->wantsToSearchStore() ){

            /*  If the user responded to the second screen (Level 2) indicating
             *  a specific category to search for a store. 
             */
            return ( $this->completedLevel(2) );

        }

        return false;

    }

    /*  hasSelectedStoreFromCategory()
     *  Returns true/false of whether the user has already selected a store 
     *  after selecting a store category
     */
    public function hasSelectedStoreFromCategory(){

        /*  First we must ensure that the user explicitly selected a category.
         */
        if( $this->hasSelectedStoreCategory() ){

            /*  If the user responded to the third screen (Level 3) indicating
             *  a specific store of choice. 
             */
            return ( $this->completedLevel(3) );

        }

        return false;

    }

    /*  getUssdInterface()
     *  Returns the store object that matches the ussd code provided
     */
    public function getUssdInterface($ussd_code = null){

        if( $ussd_code ){

            /*  Get the USSD Interface that uses ussd store code  */
            return UssdInterface::where('code', $ussd_code)->first() ?? null;

        }

    }

    /*  redirectToStore()
     *  Forces a redirect in order to access a store using a ussd code
     */
    public function redirectToStore(){

        /*  MAKE REDIRECT -
         *  We need to update the TEXT query string with the ussd code of the store selected
         *  Then we need to make a redirect with the updated url so that we can access the
         *  wantsToEnterUssdCode() method since we would now have the ussd code. This
         *  will allow the user to access the visitStore() once to store is verified 
         *  with the isValidUssdCode() method. At that point the user will have 
         *  entered the store and can start shopping.
         */

        /*  Step 1:
         *  First we must get the option number of the store category that was selected. 
         *  We can use that number to indentify the exact category that was selected.
         */
        $category_option = $this->getResponseFromLevel(2);

        //  Get categories from the database. Categories::wherehas(... only return categories linked to stores )
        //  Get selected category from the database.
        $category = $categories[ $category_option ];

        /*  Step 2:
         *  Next we must get the option number of the store that was selected from the category. 
         *  We can use that number to indentify the exact store from the category stores listed.
         */
        $store_option = $this->getResponseFromLevel(3);
        
        //  Get selected store from the database.
        $store = $category->stores[ $store_option ];

        /*  Build the url:
         *  $text represents the information provided by the user. We want to alter this information
         *  so that when we redirect we can go exactly to the store that was selected. The "1*" just
         *  means that the first option (1) should be selected from the landing page. After that the
         *  ussd code provided will be used to locate and verify the store. The verification helps
         *  us know if the store still exists and if the store is active.
         */
        $text = '1*'.$this->store->code;   
        $redirectURL = $this->changeUrlText($text);

        return redirect($redirectURL);

    }

    public function visitStore($visit = 1)
    {
        
        /*  Allow the visit property to be accessd outside this function */
        $this->visit = $visit;

        if($visit != 1){

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
        
        }else{

            $this->offset = 0;
        
        }

        /*  Get the ussd code the user provided from the Enter ussd code page (Level 2).
         *  We can use the ussd code to access the store
         */
        $ussd_code = $this->getResponseFromLevel(2);

        /*  Get the Ussd Interface
         *
         *  The interface contains the USSD name, description and its "live_mode" status 
         *  to inform us whether to allow the user to access the USSD landing page. The 
         *  interface also gives us access to the owning merchant such as a store, which
         *  allows us to access the merchants products, discounts, taxes, e.t.c
         */
        $this->ussd_interface = $this->getUssdInterface($ussd_code);

        /*  Get the ussd interface store only if the interface exists */
        $this->store = $this->ussd_interface ? $this->ussd_interface->owner : null;

        /*  Get the ussd interface products only if the interface exists */
        $this->products = $this->ussd_interface ? $this->ussd_interface->products : null;

        /*  Get the cart */
        $this->cart = $this->getCart();

         /*  If no store using the provided ussd code was found. Maybe the store 
          *  was deleted or we could not gain access to it for some reason
          */
        if( !$this->store ){

            /*  Notify the user that we have issues connecting to the store  */
            return $this->displayIssueConnectingToStorePage();

        }
         /*  If the user added more items than is allowed
          *  (has exceeded the maximum items allowed)
          */
        if( $this->hasExceededMaximumItems() ){

            /*  Notify the user that they have exceeded that maximum item allowed in the cart  */
            $allowed_cart_items = $this->maximum_cart_items . ($this->maximum_cart_items == 1 ? ' item' : ' items');
            return $this->displayCustomErrorPage('Sorry, you are only allowed to add a maximum of '.$allowed_cart_items);

        }

        /*  If the user was visiting the store again and was on the "Landing Page (Select A Product Page)" 
         *  but wanted to go back to the "Previous Product Cart Summary Page" 
         */
        if( $this->hasVisitedBefore() && $this->wantsToGoToPreviousAddedProductPage() ){

            /*  Go back only (1) Level to the "Previous Product Cart Summary Page"  */
            $response = $this->goBack( $how_many_times = 1 );

        /*  If the user already selected a product  */
        }elseif( $this->hasSelectedProduct() ){

            /*  Make sure the selected product is always available from here on  */
            $this->product = $this->getSelectedProduct();

            /*  If the product selected has variables  */
            if( $this->hasVariables() ){
                
                /*  If the user has not already selected a product variable */
                if( !$this->hasSelectedProductVariable() ){

                    /*  Display the menu for the user to select a product variable */
                    return $this->displayProductVariablePage();

                }

            /*  If the product selected does not have variables (Simple Product)  */
            }else{
                
                /*  If the we haven't specified that the selected product does
                 *  not have variables 
                 */
                if( !$this->hasSkippedVariableSelection() ){

                    /*  Since this is a simple product, we need to indicate this before we continue.
                     *  The skipVariableSelection() method will simulate a reply of a user selecting
                     *  option "0". This "0" will be used as a sign to show that this item has no 
                     *  variables, therefore no variable option was selected hence "0". Other
                     *  methods below need to know whether the product has variables or not.
                     */
                    return $this->skipVariableSelection();

                }

            }

            /*  If the user was on the "Select Variant Options Page" but wants to go back
             *  to the "Select Product Page (Store Landing Page)"   
             */
            if( $this->hasVariables() && $this->wantsToGoToPreviousPageFromVariablePage() ){
            
                /*  Go back only (1) Level to the "Select Product Page (Store Landing Page)"  */
                $response = $this->goBack( $how_many_times = 1 );

            /*  If the user already selected the product quantity  */
            }elseif( $this->hasSelectedProductQuantity() ){

                /*  If the user was on the "Select Product Quantity Page" but wants to go back
                 *  to the "Select Variable Page" (if its a variable product) or go back to the 
                 *  "Select Product Page (Store Landing Page)" (if its a simple product)   
                 */
                if( $this->wantsToGoToPreviousPageFromQuantityPage() ){

                    /*  If the product selected has variables  */
                    if( $this->hasVariables() ){

                        /*  Go back only (1) Level to the "Select Variant Options Page"  */
                        $response = $this->goBack( $how_many_times = 1 );
                        
                    /*  If the product selected has no variables (Simple Product)  */
                    }else{

                        /*  Go back only (2) Levels to the "Select Product Page (Store Landing Page)"  
                         *  We have to say two times because we added "0" to say this product does
                         *  not have variables. Since we don't have any we don't need to be on the
                         *  "Select Variant Options Page", therefore we must go back one more level 
                         *  to reach the (Store Landing Page).
                         */
                        $response = $this->goBack( $how_many_times = 2 );

                    }

                }elseif( $this->isValidProductQuantity() ){

                    /*  If the user was on the "Cart Summary Page" but wants to go back
                     *  to the "Select Product Quantity Page"   
                     */
                    if( $this->hasVariables() && $this->wantsToGoToPreviousPageFromCartSummaryPage() ){
                        
                        /*  Go back only (1) Level to the "Select Product Quantity Page"  */
                        $response = $this->goBack( $how_many_times = 1 );

                    /*  If the user already selected that they want to add another product  */
                    }elseif( $this->wantsToAddAnotherProduct() ){
                        
                        /*  Revisit the store to select another product  */
                        $response = $this->revisitStore();

                    /*  If the user already selected that they want to checkout and pay  */
                    }elseif( $this->wantsToPay() ){

                        /*  If the user was on the "Select Payment Method Page" but wants to go back
                         *  to the "Cart Summary Page"   
                         */
                        if( $this->wantsToGoToPreviousPageFromPaymentOptionsPage() ){

                            /*  Go back only (1) Level to the "Cart Summary Page"  */
                            $response = $this->goBack( $how_many_times = 1 );

                        /*  If the user already selected the payment method  */
                        }elseif( $this->hasSelectedPaymentMethod() ){

                            /*  If the user was on the "Confirm Payment Page" but wants to go back
                             *  to the "Select Payment Method Page"   
                             */
                            if( $this->wantsToGoToPreviousPageFromPaymentConfirmationPage() ){

                                /*  Go back only (1) Level to the "Select Payment Method Page"  */
                                $response = $this->goBack( $how_many_times = 1 );

                            /*  If the user already selected that they want to pay with Airtime  */
                            }elseif( $this->wantsToPayWithAirtime() ){

                                /*  If the user already confirmed that they want to pay with Airtime  */
                                if( $this->hasConfirmedPaymentWithAirtime() ){

                                    /*  Process the order using Airtime  */
                                    $response = $this->procressOrder( $payment_method = 'airtime' );

                                /*  If the user has not already confirmed that they want to pay with Airtime  */
                                }else{

                                    /*  Show the user the Airtime payment confirmation page  */
                                    $response = $this->displayAirtimePaymentConfirmationPage();  

                                }

                            /*  If the user already selected that they want to pay with Orange Money  */
                            }elseif( $this->wantsToPayWithOrangeMoney() ){

                                /*  If the user already confirmed that they want to pay with Orange Money  */
                                if( $this->hasConfirmedPaymentWithOrangeMoney() ){

                                    /*  If the user provided a valid Orange Money pin  */
                                    if( $this->isValidOrangeMoneyPin() ){

                                        /*  Process the order using Orange Money  */
                                        $response = $this->procressOrder( $payment_method = 'orange_money' );

                                    }else{

                                        /*  Notify the user of incorrect pin  */
                                        $response = $this->displayCustomErrorPage('Incorrect pin provided. Please try again');

                                    }

                                }else{

                                    /*  Show the user the Orange Money payment confirmation page  */
                                    $response = $this->displayOrangeMoneyPaymentConfirmationPage();  

                                }

                            /*  If the user selected an option that does not exist  */
                            }else{

                                /*  Notify the user of incorrect method of payment selected  */
                                $response = $this->displayCustomErrorPage('You selected an incorrect method of payment. Please try again');

                            }

                        /*  If the user has not already selected the payment method  */
                        }else{

                            /*  Show the user the payment options page  */
                            $response = $this->displayPaymentOptionsPage();  

                        }

                    /*  Otherwise they haven't made up their mind yet  */
                    }else{

                        /*  Show the user the cart summary page with options to decide what to do next  */
                        $response = $this->displayCartSummaryPage();

                    }

                }else{

                    /*  Notify the user of provide a valid quantity  */
                    $response = $this->displayCustomErrorPage('The product quantity you provided is not available');

                }


            }else{

                /*  Show the user the product quantity selection page  */
                $response = $this->displayProductQuantityPage();

            }

        /*  If the user has not selected any product  */
        }else{

            /*  Show the user the store landing page  */
            $response = $this->displayStoreLandingPage();

        }

        return $response;
    }

    public function hasVisitedBefore(){

        /*  Checks if the user has visited the store before. Anymore than one
         *  confirms that the user has visited the store before. 
         */
        return ( $this->visit > 1 );

    }

    public function hasSelectedProduct(){

        /*  If the user already responded to the "Store Landing Page" (Level 3)
         *  by selecting a product. 
         */
        return ( $this->completedLevel(3 + $this->offset) );

    }

    public function getSelectedProduct(){

        /*  Get the selected product option from the "Store Landing Page" (Level 3)
         *  We can use the store selected option to retrieve the product. We also 
         *  make sure to convert the value received to an integer
         */
        $selected_product_option = (int) $this->getResponseFromLevel(3 + $this->offset);

        /*  If we have a selected product option (e.g 1, 2 or 3)  */
        if( $selected_product_option ){

            /*  Retrieve the actual product that was selected. Note that the user
             *  would have replied "1" to select the first product on the list. 
             *  However the first product on "$this->products" variable is of
             *  index "0", this means we need to always subtract "1" from the
             *  user reply to access the correct product.
             */
            return $this->products[ $selected_product_option - 1 ];  

        }

    }

    public function hasSelectedProductVariable(){

        /*  If the user already responded to the "Select Product Variable Page" (Level 4)
         *  by selecting a specific product variant. 
         */
        return ( $this->completedLevel(4 + $this->offset) );

    }

    public function hasSkippedVariableSelection(){

        /*  If the we already responded with "0" to the "Select Product Variable Page" (Level 4)
         *  to indicate that the product does not have any variables.
         */
        return ( $this->completedLevel(4 + $this->offset) && $this->getResponseFromLevel(4 + $this->offset) == '0' );

    }

    public function hasVariables(){

        /*  Retrieve the actual product that was selected  */
        $selected_product = $this->product;    

        /*  If we have the product  */
        if( $selected_product ){

            /*  Determine if the selected product has variants  */
            return ( $selected_product['allow_variants'] == true );

        }

        return false;

    }

    public function wantsToGoToPreviousPageFromVariablePage()
    {
        /*  If the user is currently at the "Select Product Variable Page" (Level 4)
         *  but wants to go back
         */
        return ( $this->completedLevel(4 + $this->offset) && $this->getResponseFromLevel(4 + $this->offset) == '0' );
    }


    public function hasSelectedProductQuantity(){

        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by selecting a specific product variant. 
         */
        return ( $this->completedLevel(5 + $this->offset) );

    }

    public function wantsToGoToPreviousPageFromQuantityPage()
    {
        /*  If the user is currently at the "Select Product Quantity Page" (Level 5)
         *  but wants to go back
         */
        return ( $this->completedLevel(5 + $this->offset) && $this->getResponseFromLevel(5 + $this->offset) == '0' );
    }

    public function isValidProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a quantity. Get the product quantity provided.
         */
        $quantity_provided = (int) $this->getResponseFromLevel(5 + $this->offset);

        /*  Check if the quantity provided by the user does not exceed the maximum item quantity allowed  */
        $doesNotExceedMaximumQty = ( $this->maximum_item_quantity >= $quantity_provided );

        /*  Check if the quantity provided by the user is not zero (0)  */
        $doesNotEqualZero = ( $quantity_provided != 0 );

        /*  If the quantity does not exceed the maximum quantity and also does not equal "0" then it is valid  */
        return ($doesNotExceedMaximumQty && $doesNotEqualZero);

    }

    public function wantsToGoToPreviousPageFromCartSummaryPage()
    {
        /*  If the user is currently at the "Cart Summary Page" (Level 6)
         *  but wants to go back to the "Select Product Quantity Page"
         */
        return ( $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '0' );
    }

    public function wantsToGoToPreviousAddedProductPage()
    {
        /*  Get the selected product option from the Store landing page (Level 3)
         *  If the option equals "0" then the user wants to go to the previous
         *  added product
         */
        return ( $this->completedLevel(3 + $this->offset) && $this->getResponseFromLevel(3 + $this->offset) == '0' );
    }

    /*  revisitStore()
     *
     *   Allows the user the ability to revisit the store to select another product  
     */
    public function revisitStore()
    {
        return $this->visitStore( $this->visit + 1 );

    }

    public function wantsToPay()
    {
        /*  If the user already responded to the Cart summary page (Level 6)
         *  by selecting option (1) for pay now.
         */
        return ( $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '1' );
    }

    public function wantsToGoToPreviousPageFromPaymentOptionsPage()
    {
        /*  Get the selected option from the "Selected Payment Method Page" (Level 7)
         *  If the option equals "0" then the user to go back to the "Cart Summary Page",
         *  the previous page.
         */
        return ( $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '0' );

    }

    public function hasSelectedPaymentMethod()
    {
        /*  If the user already responded to the Select payment method page (Level 7)
         *  by selecting a specific payment method option. 
         */
        return ( $this->completedLevel(7 + $this->offset) );
    }

    public function wantsToPayWithAirtime()
    {
        /*  If the user already responded to the Select payment method page (Level 6)
         *  by selecting option (1) for pay using Airtime.
         */
        return ( $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '1' );
    }

    public function wantsToPayWithOrangeMoney()
    {
        /*  If the user already responded to the Select payment method page (Level 6)
         *  by selecting option (2) for pay using Orange Money.
         */
        return ( $this->completedLevel(7 + $this->offset) && $this->getResponseFromLevel(7 + $this->offset) == '2' );
    }

    public function wantsToGoToPreviousPageFromPaymentConfirmationPage()
    {
        /*  Get the selected option from the "Confirm Payment Page" (Level 8). If the
         *  option equals "0" then the user to go back to the "Select Payment Method Page",
         *  the previous page.
         */
        return ( $this->completedLevel(8 + $this->offset) && $this->getResponseFromLevel(8 + $this->offset) == '0' );

    }

    public function hasConfirmedPaymentWithAirtime()
    {

        /*  If the user already responded to the "Confirm Payment Using Airtime Page" (Level 7)
         *  by selecting option (1) to confirm payment. 
         */
        return ( $this->completedLevel(8 + $this->offset) && $this->getResponseFromLevel(8 + $this->offset) == '1' );

    }

    public function hasConfirmedPaymentWithOrangeMoney()
    {

        /*  If the user already responded to the Confirm payment using Orange Money page (Level 7)
         *  by selecting a specific option. 
         */
        return ( $this->completedLevel(8 + $this->offset) );

    }

    public function isValidOrangeMoneyPin()
    {
        /*  If the user already responded to the "Confirm payment using Orange Money page" (Level 7)
         *  by selecting a specific option. Then we can capture the Orange Money pin they provided
         */
        $orange_money_pin = $this->getResponseFromLevel(8 + $this->offset);

        /*  If the Pin provide is 4 digits long  */
        if( strlen($orange_money_pin) == 4  ){

            /*********************************************
             *  API HERE TO VERIFY THE ACCOUNT USING PIN
             ********************************************/

            return true;

        }

        return false;

    }

    public function processPaymentWithAirtime(){

        /*********************************************
         *  API TO PAY THE ORDER USING AIRTIME
         ********************************************/

        /*  The response we return will be an array holding a status of the  
         *  transaction and an error incase the transaction is declined
         */
        return ['status' => true, 'error' => null];

    }

    public function processPaymentWithOrangeMoney(){

        /*********************************************
         *  API TO PAY THE ORDER USING Orange Money
         ********************************************/

        /*  The response we return will be an array holding a status of the  
         *  transaction and an error incase the transaction is declined
         */
        return ['status' => true, 'error' => null];

    }
    
    public function procressOrder($payment_method = null){

        /***************************************************************************************
         * Include transaction fee to the grand total amount of the cart before making payment
         **************************************************************************************/

        /* If the user specified to pay using Airtime  */
        if( $payment_method == 'airtime' ){

            /*  Attempt to process the payment using Airtime  */
            $payment_response = $this->processPaymentWithAirtime();

        /* If the user specified to pay using Orange Money  */
         }elseif( $payment_method == 'orange_money' ){

            /*  Attempt to process the payment using Orange Money  */
            $payment_response = $this->processPaymentWithOrangeMoney();

         }else{
            
            $payment_response = ['status' => false, 'error' => 'No payment method was specified'];

         }

        /*  If the payment status was successful  */
        if( $payment_response['status'] ){

            /******************************************************************
             *  Find/Create a contact
             *  Create a new order for contact
             *  Convert the order to a payable invoice
             *  Create a transation for the invoice (Status=paid)
             *  Update the order lifecycle (Paid)
             *  Update the order lifecycle (Pending Delivery)
             *  Send a payment confirmation sms with an order ref #
             ******************************************************************/

            /*  Get the contact information */
            $contactInfo = [
                'name' => 'Julian Tabona',
                'is_vendor' => false,
                'is_customer' => true,
                'is_individual' => true,
                'phone' => [
                    'calling_code' => $this->user['phone']['calling_code'],
                    'number' => $this->user['phone']['number'],
                    'provider' => 'orange',
                    'type' => 'mobile'
                ],
                'address' => null,
                'email' => null
            ];

            /*  Create a new store contact  */
            $contact = $this->store->createContact($contactInfo);

            /*  Create a new order using the provided contact details
             *  as (customer and reference) as well as the store details
             *  as (merchant) and cart items as (items being purchased)
             */
            $order = ( new \App\Order )->initiateCreate([
                'customer_id' => $contact->id,
                'reference_id' => $contact->id,
                'merchant_id' => $this->store->id,
                'items' => $this->cart['items']
            ]);

            return dd( $order );

            /*  Mark the order invoice as paid  */
            $payment = $order->invoice->recordAutomaticPayment($transaction = [
                /*  Add these details to the makeAutomaticPayment() method
                'type' => 'payment',
                'status' => 'success',
                'automatic' => true,
                */
                'payment_type' => 'airtime',
                'payment_amount' => $this->cart['grand_total_amount']
            ]);

            /*  Send the order as a summarised SMS to the merchant  */
            $merchantSMS = $order->smsOrderToMerchant();

            /*  Send the receipt as a summarized SMS to the customer  */
            $customerSMS = $order->invoice->smsReceiptToCustomer();

            /*  Notify the user of the payment success  */
            $response = $this->displayPaymentSuccessPage();

        }else{

            /*  Fetch the error (Reason why the payment failed)  */
            $error = $payment_response['error'];

            /*  Notify the user of the payment failure  */
            $response = $this->displayPaymentFailedPage($error);

        }

        return $response;

    }

    public function summarize($text, $limit){

        return strlen($text) > $limit ? substr($text, 0, $limit + 3)."..." : $text;

    }

    public function wantsToAddAnotherProduct()
    {
        /*  If the number of products we want to add do not match the number of 
         *  visitations we have made to the store then this means we want to 
         *  add another product.
         */
        $number_of_products_to_add = $this->getInformationOfProductsToAddToCart();

        if(count($number_of_products_to_add) != $this->visit){

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

        foreach($result as $key => $data){
            
            /*
             *  Remember that the first element in the $result array will always contain
             *  non-product related information such as the ussd code. We must remove
             *  this information so that we are strickly left with the product related 
             *  information
             */

            /*  If this is the first product information  */ 
            if( $key == 0 ){
                
                /*  Split the data into individual responses e.g
                 *  
                 *  $data = 1*001*1*0*3
                 * 
                 *  After explode using "*" = ["1", "001","1", "0", "3"] 
                 */ 
                $user_responses =  explode('*', $data);

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
        $products_to_add_to_cart = array_flatten( $result );

        return $products_to_add_to_cart;

    }

    public function getCart(){

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
        foreach($products_to_add as $product_to_add){

            /*  If the user has already selected their product */
            if( $product_to_add != '' ){
                
                /*
                 *  Now we must split the $product_to_add into individual responses
                 */
                $user_responses = explode('*', $product_to_add);

                /*  
                 *  Three (3) responses are expected for each complete product selection cycle. 
                 */
                if( count($user_responses) >= 3 ){

                    /*  Get the product selected  */
                    $product = $products[ $user_responses[0] - 1 ];

                    /*  If a variable product was selected  */
                    if( $user_responses[1] != 0 ){

                        /*  Replace the simple product with the variable product selected  */
                        $product = $product->childProducts[ $user_responses[1] - 1 ];

                    }

                    /*  Get the product id and quantity  */
                    $product_id = $product['id'];
                    $product_quantity = $user_responses[2];

                    array_push($items, ['id'=>$product_id, 'quantity'=>$product_quantity]);

                }

            }

        }
        /*  If we have items */
        if( count($items) ){

            /*  Retrieve and return the cart details relating to the merchant and items provided  */
            return ( new \App\MyCart() )->getCartDetails( $this->store, $items );

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
        $responses = array_filter($responses, function($value) { 

            return !is_null($value) && $value !== ''; 

        });

        return array_values($responses);

    }

    public function getResponseFromLevel($levelNumber = null)
    {
        if( $levelNumber ){

            /*  Get all the user reponses.  */
            $user_responses = $this->getUserResponses();

            /*  We want to say if we have levelNumber = 1 we should get the landing page data 
             *  (since thats level 1) but technically $user_responses[0] = landing page response. 
             *  This means to get the response for the level we want we must decrement by one unit.
             */
            return isset( $user_responses[ $levelNumber - 1 ] ) ? $user_responses[ $levelNumber - 1 ] : null;
        }
    }

    public function completedLevel($levelNumber = null)
    {
        /*  If we have a level number  */
        if( $levelNumber ){

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
        return $this->simulateUserReply($reply = '0');
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
          if( isset( $reply ) ){

            /*  If the reply provided is an array - meaning we have multiple replies provided */
            if( is_array( $reply ) ){

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
            }else{

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

    public function goBack( $how_many_times = 1 )
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
        for( $x=0; $x <= $how_many_times; $x++ ){

            /*  Get the original TEXT responses. */
            $original_text = $request_query_array['TEXT'];

            /*  Remove the last value (the last reply) and update the TEXT query. 
             *  We are basically removing any last response the user gave us.
             */
            
            /*  Get the original text array */
            $original_text_array = explode("*", $original_text);
            
            /*  Remove the last item from the original text array  */
            array_pop($original_text_array);

            /*  Return array to string  */
            $updated_text = implode("*", $original_text_array);

            /*  Update the TEXT query string  */
            $request_query_array['TEXT'] = $updated_text;

        }
        
        /*  Re-attach the query strings to the url. This means we rebuild the url as it was, but
         *  obviously with the new updates with just made.
         */
        foreach($request_query_array as $query_name => $query_value){
            
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
        foreach($request_query_array as $query_name => $query_value){
            
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

    public function canAddMoreItems(){

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

    public function hasExceededMaximumItems(){

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
         *  Therefore if the the items are more than the more than the 
         *  maximum allowed cart items this method returns true.
         */
        return $this->visit > $this->maximum_cart_items;

    }

    public function isProductAddedToCart( $productId = null )
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

}