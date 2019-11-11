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
    private $orders;
    private $offset;
    private $contact;
    private $products;
    private $currency;
    private $session_id;
    private $service_code;
    private $phone_number;
    private $ussd_interface;
    private $order_per_page;
    private $text_field_name;
    private $favourite_stores;
    private $variable_options;
    private $selected_product;
    private $selected_products;
    private $orders_on_display;
    private $products_per_page;
    private $maximum_cart_items;
    private $products_on_display;
    private $ussd_character_limit;
    private $maximum_item_quantity;
    private $variant_attribute_name;
    private $variable_options_per_page;
    private $selected_variable_options;
    private $stores_per_page;
    private $stores_on_display;

    

    public function __construct(Request $request)
    {
        /*  Get the name of "TEXT" field used to save the user responses  */
        $this->text_field_name = 'text';

        /*  Get the USSD TEXT value (User Response)  */
        $this->text = $request->get($this->text_field_name);

        /*  Get the USSD Phone Number value. We use the "preg_replace" method
         *  to remove "+" symbol that comes with the phone number. This way
         *  we only keep the numbers.
         *
         *  Before: +26775993221
         *  After:  26775993221
         */
        $this->phone_number = preg_replace('/[^0-9]/', '', $request->get('phoneNumber'));

        /*  Get the Session Id  */
        $this->session_id = $request->get('sessionId');

        /*  Get the Service Code  */
        $this->service_code = $request->get('serviceCode');

        /*  Define the user's mobile number  */
        $this->user['phone'] = [
            'calling_code' => substr($this->phone_number, 0, 3),
            'number' => substr($this->phone_number, 3, 8),
        ];

        /*  Define the maximum character limit for every USSD response  */
        $this->ussd_character_limit = 160;

        /*  Define the maximum number of stores to display on screen  */
        $this->stores_per_page = 4;

        /*  Define the maximum number of products to display on screen  */
        $this->products_per_page = 4;

        /*  Define the maximum number of variant options to display on screen  */
        $this->variable_options_per_page = 4;

        /*  Define the maximum number of orders to display on screen  */
        $this->order_per_page = 4;

        /*  Define the maximum number of items that can be added to cart  */
        $this->maximum_cart_items = 5;

        /*  Define the maximum quantity per product added  */
        $this->maximum_item_quantity = 5;

        $this->visit = 1;
        $this->selected_products = [];
        $this->variable_options = [];
        $this->selected_variable_options = [];
    }

    /*********************************
     *  HOME            *
    *********************************/

    /*  home()
     *  This is the first method we hit where all the USSD processes are
     *  sequencially handled as the user makes requests and receices
     *  responses.
     */
    public function home()
    {
        $this->verifyUssdDetails();

        $this->manageEncodedRequests();

        $this->manageGoBackRequests();

        $this->managePaginationRequests();

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
                        return $this->displayCustomGoBackPage("Store was not found. Make sure you are using the correct store code\n");
                    }

                    /*  If the user hasn't yet provided the store code  */
                } else {
                    $response = $this->displayEnterStoreCodePage();
                }

                /*  If the user already indicated that they want to search a store (They don't have a Store Code)  */
            } elseif ($this->wantsToSearchStore()) {

                /*  If the user already selected a specific option from the "Find Stores Page"  */
                if( $this->hasSelectedHowToSearchStore() ){

                    /*  If the user wants to search stores from their favourite store list  */
                    if ($this->wantsToSearchMyFavouriteStores()) {
                    
                        /*  Make sure the users favourite stores are accessible from here on  */
                        $this->stores = $this->getMyStores();

                        /*  Manage pagination requests  */
                        $this->handleStorePagination();
                        
                        /*  If the user already selected a specific store from the "Stores Page"  */
                        if ($this->hasSelectedStore()) {

                            /*  Visit the selected store  */
                            return $this->visitSelectedStore();

                        } else {

                            /*  Display the "Select Category Store Page"  */
                            $response = $this->displayStoresPage();

                        }

                    /*  If the user wants to search popular stores  */
                    }elseif ($this->wantsToSearchPopularStores()) {
                    
                        /*  Make sure the popular stores are accessible from here on  */
                        $this->stores = $this->getPopularStores();

                        /*  Manage pagination requests  */
                        $this->handleStorePagination();

                        /*  If the user already selected a specific store from the "Stores Page"  */
                        if ($this->hasSelectedStore()) {
                            
                            /*  Visit the selected store  */
                            return $this->visitSelectedStore();

                        } else {

                            /*  Display the "Select Popular Store Page"  */
                            $response = $this->displayStoresPage();

                        }


                    /*  If the user already selected a specific category from the "Select Store Category Page"  */
                    }elseif ($this->hasSelectedStoreCategory()) {
                    
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


                }else {

                    /*  Display the "Find Stores Page"  */
                    $response = $this->displayFindStoresPage();

                }

                /*  Selected an option that does not exist  */
            } else {
                /*  Notify the user of incorrect option selected  */
                return $this->displayIncorrectOptionPage();
            }
        }

        /*  Return the response to the user  */
        return response($response)->header('Content-Type', 'text/plain');
        //return response($response)->header('Content-Type', 'application/json');
        //  return response($response."\n\n".'characters: '.strlen($response))->header('Content-Type', 'text/plain');
    }
    
    public function handleStorePagination()
    {
        /*  If the user indicated to paginate the "Stores Page"  */
        if ($this->wantsToPaginateStoresPage()) {

            /*  Paginate the "Stores Page"  */
            $this->stores_on_display = $this->getPaginatedStoresToList();

            $this->offset = $this->offset + 1;

        } else {

            $this->stores_on_display = $this->getFirstStoresToList();

        }
    }

    public function visitSelectedStore()
    {
        /*  Get the selected store  */
        $this->store = $this->getSelectedStore();

        /*  Selected a store that does not exist  */
        if (!$this->store) {

            /*  Notify the user of incorrect option selected  */
            return $this->displayIncorrectOptionPage();

        }

        /*  Get the store code  */
        $store_code = $this->store->ussdInterface->code ?? null;

        /*  First we need to remove the first three options we provided. The first option was
         *  when we indicated that we wanted to search for a store. The second option was when
         *  we indicated that we wanted to search favourite stores or popular stores, e.t.c. 
         *  The third option was when
         *  we indicated the store we wanted to visit. We need to remove all three responses
         *  and add new information as their replacement. We will replace them with two responses. 
         *  The first response will be of value equal to (1) to indicate that the user wants to 
         *  provide a store code so that we can utilise the wantsToEnterStoreCode(). The second
         *  response will be the store code itself so that we can gain access to the visitStore()
         *  after we satisfy the hasProvidedStoreCode() and isValidStoreCode(). After replacing
         *  the information we will re-run the home() method to access the selected store.
         */

        /*  If we have the store and the store code */
        if($store_code){

            /*  Get all the responses as an array  */
            $responses = explode('*', $this->text);

            /*  Remove the first three (3) responses of the array  */
            $responses = array_slice($responses, 3);

            /*  Add the two (2) new responses to the array  */
            array_unshift($responses, "1", $store_code);

            /*  Join the remaining responses */
            $this->text = implode('*', $responses);

            /*  Run home() again to access the store */
            return $this->home();
            
        }else{

            /*  Notify the user that we have issues connecting to the store  */
            return $this->displayIssueConnectingToStorePage();

        }

    }

    public function getSelectedStore()
    {
        /*  Get the selected option from the "Select Store Page" (Level 3).
         *  We can use the selected option to retrieve the order.
         */

        /*  Get the selected option and convert it to an interger  */
        $selected_option = (int) $this->getResponseFromLevel(3 + $this->offset);

        /*  If we have a selected option (e.g 1, 2 or 3)  */
        if ($selected_option) {

            /*  Retrieve the actual store that was selected. Note that the user would have
             *  replied "1" to select the first store on the list. However the first store
             *  on "$this->stores" variable is of index "0", this means we need to always
             *  subtract "1" from the user reply to access the correct store.
             */

            return $this->stores[$selected_option - 1] ?? null;
        }
    }

    public function visitStore()
    {
        $this->getStoreDetails();

        /*  If the user already selected an option from the "Store Landing Page"  */
        if ($this->hasSelectedStoreLandingPageOption()) {
            /*  If the user already indicated that they want to start shopping  */
            if ($this->wantsToStartShopping()) {
                /*  Allow the user to start shopping  (At the specified store)  */
                $response = $this->startShopping();

            /*  If the user already indicated that they want to view past orders  */
            } elseif ($this->wantsToViewMyOrders()) {
                /*  Allow the user to view their past orders  */
                $response = $this->viewMyOrders();

            /*  If the user already indicated that they want to view the contact us information  */
            } elseif ($this->wantsToViewContactUs()) {
                /*  Allow the user to view the store's contact information  */
                $response = $this->viewContactUs();

            /*  If the user already indicated that they want to view the about us information  */
            } elseif ($this->wantsToViewAboutUs()) {
                /*  Allow the user to view information about the store  */
                $response = $this->viewAboutUs();

            /*  Selected an option that does not exist  */
            } else {
                /*  Notify the user of incorrect option selected  */
                return $this->displayIncorrectOptionPage();
            }
        } else {
            /*  Show the user the "Store Landing Page"  */
            $response = $this->displayStoreLandingPage();
        }

        return $response;
    }

    public function getStoreDetails()
    {
        /*  Get the store code the user provided from the "Enter Store Code Page".
         *  We can use the store code to get the USSD Interface. The USSD Interface
         *  can then get us the exact store.
         */
        $store_code = $this->getProvidedStoreCode();

        /*  Get the Ussd Interface
         *
         *  The interface contains the USSD screen name, description and its "live_mode"
         *  status to inform us whether to allow the user access the store landing page.
         *  The interface also gives us access to the owning store, which allows us to
         *  access the store products, discounts, taxes, e.t.c
         */
        $this->ussd_interface = $this->getUssdInterface($store_code);

        /*  Get the Ussd Interface owning store only if the interface exists */
        $this->store = $this->ussd_interface ? $this->ussd_interface->owner : null;

        /*  Get the Ussd Interface products only if the interface exists */
        $this->products = $this->ussd_interface ? $this->ussd_interface->products : null;

        /*  Get the store currency symbol or currency code if only if the store exists */
        $this->currency = $this->store ? ($this->store['currency']['symbol'] ?? $this->store['currency']['code']) : null;

        /*  If no store using the provided store code was found, or maybe the store
         *  was deleted or we could not gain access to it for some reason
         */
        if (!$this->store) {

            /*  Notify the user that we have issues connecting to the store  */
            return $this->displayIssueConnectingToStorePage();

        }

        /*  If the store is not supporting USSD at this time  */
        if( !$this->store->support_ussd){

            /*  Notify the user that the store is not available  */
            return $this->displayCustomGoBackPage("Sorry, the store is currently offline.\n");
            
        }

    }

    public function startShopping()
    {
        /*  If the user added more items than is allowed to their cart,
         *  (has exceeded the maximum items allowed)
         */
        if ($this->hasExceededMaximumItems()) {
            $allowed_cart_items = $this->maximum_cart_items.($this->maximum_cart_items == 1 ? ' item' : ' items');

            /*  Notify the user that they have exceeded that maximum items allowed in the cart  */
            return $this->displayCustomGoBackPage('Sorry, you are only allowed to add a maximum of '.$allowed_cart_items."\n");
        }

        return $this->showProductCatalog();
    }

    public function showProductCatalog()
    {
        /*  If the user indicated to paginate the "Store Products Page"  */
        if ($this->wantsToPaginateProductsPage()) {
            /*  Paginate the products page "Previous Product Cart Summary Page"  */
            $this->products_on_display = $this->getPaginatedProductsToList();

            $this->offset = $this->offset + 1;
        } else {
            $this->products_on_display = $this->getFirstProductsToList();
        }

        /*  If the user already selected a product  */
        if ($this->hasSelectedProduct()) {
            $response = $this->handleSelectedProduct();

        /*  If the user has not selected any product  */
        } else {
            /*  Show the user the "Product Catalog Page"  */
            $response = $this->displayProductCatalogPage();
        }

        return $response;
    }

    public function handleSelectedProduct()
    {
        /*  Make sure the selected product is always available from here on  */
        $this->selected_product = $this->getSelectedProduct();

        /*  Selected a product that does not exist  */
        if (!$this->selected_product) {
            /*  Notify the user of incorrect option selected  */
            return $this->displayIncorrectOptionPage();
        }

        /*  If the selected product has variables  */
        if ($this->hasVariables()) {
            if ($response = $this->handleProductVariables()) {
                return $response;
            }
        }

        /*  If the user already selected the product quantity  */
        if ($this->hasSelectedProductQuantity()) {
            $response = $this->handleProductQuantity();
        } else {
            /*  Show the user the product quantity selection page  */
            $response = $this->displayProductQuantityPage();
        }

        return $response;
    }

    public function handleProductVariables()
    {
        /*  Assumming the product has three different variables being "Size", "Color" and "Material".
         *  This means that we need to show the user 3 different pages which will show the variable
         *  options e.g ['Small', 'Medium', 'Large'] for variable 1, ['Blue', 'Red'] for variable 2
         *  and ['Cotton', 'Nylon'] for variable 3. If $variant_attribute_offset=1 then this is the
         *  first variant attribute page where the user selects the product size.
         */
        $variant_attributes = $this->selected_product->variant_attributes ?? [];
        $variant_attribute_offset = 1;

        /*  Foreach product variant page number  */
        foreach ($variant_attributes as $variant_attribute_name => $variable_options) {
            /*  Adjust the offset by including the $variant_attribute_offset since we are now
                *  selecting variable options.
                */
            $this->offset = $this->offset + 1;

            $this->variant_attribute_name = $variant_attribute_name;
            $this->variable_options = $variable_options;

            /*  If the user indicated to paginate the "Store Products Page"  */
            if ($this->wantsToPaginateVariantOptionsPage()) {
                /*  Paginate the products page "Previous Product Cart Summary Page"  */
                $variable_options_to_display = $this->getPaginatedVariantOptionsToList();

                $this->offset = $this->offset + 1;
            } else {
                $variable_options_to_display = $this->getFirstVariantOptionsToList();
            }

            /*  If the user has not already selected an option for this variable page.  */
            if (!$this->hasSelectedProductVariantPageOption()) {
                /*  Determine if this is the last variant attribute in the loop */
                $is_last_variant_page = ($variant_attribute_offset == count($variant_attributes));

                /*  Display the menu for the user to select a product variable */
                return $this->displayProductVariablePage($variant_attribute_name, $variable_options_to_display, $is_last_variant_page);
            } else {
                /*  Get the selected option for this variable page.  */
                $selected_option = $this->getSelectedVariableOption();

                /*  If selected variable does not exist  */
                if (!$selected_option) {
                    /*  Notify the user of incorrect option selected  */
                    return $this->displayIncorrectOptionPage();
                }

                array_push($this->selected_variable_options, $selected_option);
            }

            $variant_attribute_offset = ++$variant_attribute_offset;
        }

        /*  Make sure the selected product variation is always available from here on  */
        $this->selected_product = $this->getSelectedProductVariation();
    }

    public function handleProductQuantity()
    {
        if ($this->isValidProductQuantity()) {
            /*  Update the selected product quantity */
            $this->selected_product['quantity'] = $this->getSelectedProductQuantity();

            /* Add the selected product to the rest of the other selected products */
            array_push($this->selected_products, $this->selected_product);

            /*  Get the cart and make sure the cart is always available from here on */
            $this->cart = $this->getCart();

            /*  If the user already selected the payment method  */
            if ($this->hasSelectedOrderSummaryOption()) {
                /*  If the user already selected that they want to add another product  */
                if ($this->wantsToAddAnotherProduct()) {
                    /*  Revisit the store to select another product  */
                    $response = $this->revisitStore();

                /*  If the user already selected that they want to checkout and pay  */
                } elseif ($this->wantsToPay()) {
                    $response = $this->handleCartCheckout();

                /*  Selected an option that does not exist  */
                } else {
                    /*  Notify the user of incorrect option selected  */
                    return $this->displayIncorrectOptionPage();
                }
            } else {
                /*  Show the user the cart summary page with options to decide what to do next  */
                $response = $this->displayCartSummaryPage();
            }
        } else {
            /*  Notify the user to provide a valid quantity  */
            return $this->displayCustomGoBackPage("The product quantity you provided is not available.\n");
        }

        return $response;
    }

    public function handleCartCheckout()
    {
        /*  If the user already selected the payment method  */
        if ($this->hasSelectedPaymentMethod()) {
            /*  If the user already selected that they want to pay with Airtime  */
            if ($this->wantsToPayWithAirtime()) {
                /*  If the user already selected an option from the "Pay With Airtime Confirmation Page"  */
                if ($this->hasSelectedAirtimeConfirmationOption()) {
                    /*  If the user already confirmed that they want to pay with Airtime  */
                    if ($this->hasConfirmedPaymentWithAirtime()) {
                        /*  Process the order using Airtime  */
                        $response = $this->procressOrder($payment_method = 'airtime');

                    /*  Selected an option that does not exist  */
                    } else {
                        /*  Notify the user of incorrect option selected  */
                        return $this->displayIncorrectOptionPage();
                    }

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

                    /*  If the user's Orange Money pin was not valid  */
                    } else {
                        /*  Notify the user of incorrect pin  */
                        $response = $this->displayCustomGoBackPage("Incorrect pin provided. Please try again.\n");
                    }
                } else {
                    /*  Show the user the Orange Money payment confirmation page  */
                    $response = $this->displayOrangeMoneyPaymentConfirmationPage();
                }

                /*  If the user selected an option that does not exist  */
            } else {
                $response = $this->displayCustomGoBackPage("You selected an incorrect method of payment. Please try again.\n");
            }

            /*  If the user has not already selected the payment method  */
        } else {
            /*  Show the user the payment options page  */
            $response = $this->displayPaymentOptionsPage();
        }

        return $response;
    }

    /*  revisitStore()
     *
     *   Allows the user the ability to revisit the store to select another product
     */
    public function revisitStore()
    {
        $this->visit = ++$this->visit;

        $this->offset = $this->offset + 3;

        return $this->visitStore();
    }

    public function viewMyOrders()
    {
        /*  Make sure the orders are accessible from here on  */
        $this->orders = $this->getMyOrders();

        if ($this->hasSelectedOrderHomePageOption()) {
            if ($this->wantsToViewAllOrders()) {
                /*  If the user indicated to paginate the "Orders Page"  */
                if ($this->wantsToPaginateOrdersPage()) {
                    /*  Paginate the "Orders Page"  */
                    $this->orders_on_display = $this->getPaginatedOrdersToList();

                    $this->offset = $this->offset + 1;
                } else {
                    $this->orders_on_display = $this->getFirstOrdersToList();
                }

                if ($this->hasSelectedOrder()) {
                    /*  Make sure the selected order is accessible from here on  */
                    $this->order = $this->getSelectedOrder();

                    /*  Selected an option that does not exist  */
                    if (!$this->order) {
                        /*  Notify the user of incorrect option selected  */
                        return $this->displayIncorrectOptionPage();
                    }
                } else {
                    /*  Show the user the "Orders Page"  */
                    return $this->displayOrderListPage();
                }
            } elseif ($this->wantsToSearchOrders()) {
                if ($this->hasProvidedOrderNumberToSearch()) {
                    if ($this->searchedOrderExists()) {
                        /*  Make sure the serched order is accessible from here on  */
                        $this->order = $this->getSearchedOrder();
                    } else {
                        /*  Notify the user that the searched order was not found  */
                        return $this->displayCustomGoBackPage('Sorry, Order #'.$this->getOrderNumber()." was not found.\n");
                    }
                } else {
                    /*  Show the user the "Enter Order Number Page"  */
                    return $this->displayCustomGoBackPage("Enter the order number:\n");
                }

                /*  Selected an option that does not exist  */
            } else {
                /*  Notify the user of incorrect option selected  */
                return $this->displayIncorrectOptionPage();
            }
        } else {
            /*  Show the user the "View All Orders Or Search Order Page"  */
            return $this->displayViewAllOrdersOrSearchOrderPage();
        }

        if ($this->hasSelectedMyOrderSummaryOption()) {
            if ($this->hasSelectedViewOrderItems()) {
                /*  Show the user the "Order Items Page"  */
                return $this->displayOrderItemsPage();
            } elseif ($this->hasSelectedViewOrderCostBreakdown()) {
                /*  Show the user the "Order Cost Breakdown Page"  */
                return $this->displayOrderCostBreakdownPage();

            /*  Selected an option that does not exist  */
            } else {
                /*  Notify the user of incorrect option selected  */
                return $this->displayIncorrectOptionPage();
            }
        } else {
            /*  Show the user the "Order Summary Page"  */
            return $this->displayOrderSummaryPage();
        }
    }

    public function viewContactUs()
    {
        return $this->displayContactUsPage();
    }

    public function viewAboutUs()
    {
        return $this->displayAboutUsPage();
    }

    /********************************
     *  DISPLAY SCREENS             *
     ********************************/

    /*  displayLandingPage()
     *  This is the first page displayed when accessing the USSD.
     *  In this page we ask the user to either choose to enter
     *  a valid store code or search a store instead
     */
    public function displayLandingPage()
    {
        $response = "CON Find stores ka BONAKO,\nSelect (1) to enter the store code or (2) to search for a store \n";
        $response .= "1. Enter store code\n";
        $response .= "2. Find stores\n";

        return $response;
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

    /*  displayCustomGoBackPage()
     *  This is the page displayed when a problem was encountered and but we want
     *  to still continue the session. We therefore display the custom error
     *  message but also display the option to go back.
     */
    public function displayCustomGoBackPage($message = '', $include_line_breaker = true)
    {
        $response = 'CON '.$message.($include_line_breaker ? "\n" : '');
        $response .= '0. Go Back';

        return $response;
    }

    /*  displayEnterStoreCodePage()
     *  This is the page displayed when a user must enter the Store Code.
     */
    public function displayEnterStoreCodePage()
    {
        return $this->displayCustomGoBackPage("Enter the store code to visit your local store:\n");
    }

    /*  displayFindStoresPage()
     *  This is the page displayed when a user must select a method to search
     *  for stores e.g by listing favourite stores, popular stores or 
     *  categorised stores
     */
    public function displayFindStoresPage()
    {
        $response = "Find Stores:\n";
        $response .= "1. My favourites (".count($this->getMyStores()).")\n";
        $response .= "2. Popular stores\n";
        $response .= "3. Stores by category";

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayStoresPage()
     *  This is the page displayed when a user must select a store to visit
     */
    public function displayStoresPage()
    {

        $response = "Select store:\n";

        if( count($this->stores_on_display) ){

            foreach ($this->stores_on_display as $key => $store) {
                
                $number = ++$key;
                $ussd_interface = $store->ussdInterface;
                $response .= $number.". ".$ussd_interface['name']."\n";

            }
            
            $response .= "99. Show more";

        }else{
            
            $response .= count($this->stores) ? "\nNo more stores to show.\n" : "\nNo stores found.\n";
            
        }

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayStoreCategoriesPage()
     *  This is the page displayed when a user must select a store category.
     *  A store category groups stores that operate in the same industry
     *  e.g Transport, Accomodation, e.t.c
     */
    public function displayStoreCategoriesPage()
    {
        $response = "Search by category\n";
        $response .= "1. Accomodation Services (12)\n";
        $response .= "2. Transport Services (4)\n";
        $response .= '3. Fast Food Services (18)';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayCategoryStores()
     *  This is the page displayed when a user must select a store from a category.
     *  Assuming the user selected the "Accomodation" category, they must select
     *  a store from that category here.
     */
    public function displayCategoryStores()
    {
        $response = "Category: Transport, Select option to visit\n";
        $response .= "1. Smiley Cabs\n";
        $response .= '2. Deluxe Cabs';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayIncorrectOptionPage()
     *  This is the page displayed when the user selects an incorrect option
     *  from the previous screen provided. On this screen we notify the user
     *  of the problem and instruct them to go back.
     */
    public function displayIncorrectOptionPage()
    {
        return $this->displayCustomGoBackPage("You selected an incorrect option. Please try again.\n");
    }

    /*  displayIssueConnectingToStorePage()
     *  This is the page displayed when a store existed during the session at some point
     *  but for some reason we cannot seem to access it again. Maybe the store got deleted
     *  while a user was shopping or maybe issues where encontered while running an SQL Query.
     *  Whatever the case we show this page
     */
    public function displayIssueConnectingToStorePage()
    {
        return $this->displayCustomGoBackPage("Sorry, we could not access/connect to the store.\n");
    }

    /*  displayStoreLandingPage()
     *  This is the first page displayed when accessing the any store.
     *  In this page we provide the user with options to view the stores
     *  available products, contact us, about us and the users past orders.
     */
    public function displayStoreLandingPage()
    {
        /*  Show the store name  */
        $response = $this->ussd_interface['name'].":\n";
        $call_to_action = $this->ussd_interface['call_to_action'] ?? 'View Products';
        $number_of_orders = count($this->getMyOrders());

        /*  Show available store options  */
        $response .= '1. '.$call_to_action."\n";
        $response .= '2. My Orders('.$number_of_orders.")\n";
        $response .= "3. Contact Us\n";
        $response .= '4. About Us';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayProductCatalogPage()
     *  This is the page displayed when viewing the product catalog.
     *  It displays available store products that users can checkout
     *  with
     */
    public function displayProductCatalogPage()
    {
        /*  Show the store name and the number of products added to cart so far
         *  The number of products already added will be the number of visits
         *  to the store excluding the current visit. This will represent the
         *  number of past visitations since we select one item per visitation.
         */

        /*  Get total number of items already added to cart */
        $items_added_count = ($this->visit - 1);

        /*  Get total price of items already added to cart */
        $items_added_total = $this->currency.$this->convertToMoney($this->cart['grand_total']);

        $response = 'Added ('.($items_added_count).') '.($items_added_count == 1 ? 'item. ' : 'items. ')."\n";
        $response .= 'Cart Total: '.$items_added_total."\n";

        /*  List the store products  */
        $response .= $this->getStoreLandingPageProducts();

        return $this->displayCustomGoBackPage($response);
    }

    public function getStoreLandingPageProducts()
    {
        $response = '';

        /*  If we have any products  */
        if (count($this->products_on_display)) {
            /*  List the products available  */
            foreach ($this->products_on_display as $key => $product) {
                $option_number = $key + 1;

                /*  Get the product name, currency symbol and price  */
                $product_id = trim($product['id']);
                $product_name = trim($product['name']);
                $product_price = $product['unit_price'];
                
                /*  Check if the product has variables  */
                $product_has_variables = $this->hasVariables($product);

                /*  Check if the product is on sale  */
                $product_on_sale = $this->isOnSale($product);

                /*  Show this product with price only if:
                 *  1 - It has inventory and the quantity is greater than zero (otherwise it is out of stock)
                 *  2 - It does not have inventory (no stock taken)
                 */
                if ($this->hasStock($product)) {
                    /*  Check if the product has been added to the cart already  */
                    if ($this->isProductAddedToCart($product_id)) {

                        /*  Show the product name, and indicate that the product is in the cart already  */
                        $response .= $option_number.'. '.$product_name." (added)\n";

                    /*  If the product hasn't been added to the cart already  */
                    } else {

                        if( $product_has_variables ){
                            
                            /*  Show the product name only  */
                            $response .= $option_number.'. '.$product_name."\n";

                        }else{

                            /*  Show the product name, currency and price  */
                            $response .= $option_number.'. '.$product_name.' -'.$this->currency.$product_price;

                            /*  If the product is on sale then make an indication  */
                            $response .= ($product_on_sale ? ' (on sale)' : '')."\n";
                            
                        }
                    }

                    //  Otherwise show this product as out of stock
                } else {
                    /*  Show the product name, and indicate that this product is out of stock  */
                    $response .= $option_number.'. '.$product_name." (out of stock)\n";
                }
            }

            $response .= '99. Show More';
        } else {
            /*  If we don't have any products to list  */
            $response = count($this->products) ? "\nNo more items to show.\n" : "\nNo items found :(\n";
        }

        return $response;
    }

    /*  displayProductVariablePage()
     *  This is the page displayed when a user must select a product variable
     */
    public function displayProductVariablePage($variant_attribute_name, $variable_options, $is_last_variant_page)
    {
        $response = $this->selected_product->name.": Select an option\n";

        if (count($variable_options)) {
            foreach ($variable_options as $key => $option) {
                $additional_variable_options = [$variant_attribute_name => $option];
                $product_variation = $this->getSelectedProductVariation($additional_variable_options);

                /*  If we atleast have one variant avaialable  */
                if ($product_variation) {
                    $option_number = $key + 1;
                    $product_id = $product_variation['id'];
                    $product_price = $product_variation['unit_price'];
                    $product_on_sale = $this->isOnSale($product_variation);

                    $response .= $option_number.'. '.$option;

                    if ($is_last_variant_page) {
                        //  If this variation product is not out of stock
                        if ($this->hasStock($product_variation)) {
                            /*  Check if the product has been added to the cart already  */
                            if ($this->isProductAddedToCart($product_id)) {
                                /*  Indicate that the product is in the cart already  */
                                $response .= ' (added)';

                            /*  If the product hasn't been added to the cart already  */
                            } else {
                                /*  Show the product name, currency and price  */
                                $response .= $product_price ? ' -'.$this->currency.$this->convertToMoney($product_price) : ' (Not Available)';

                                /*  If the product is on sale then make an indication  */
                                $response .= ($product_on_sale ? ' (on sale)' : '');
                            }

                            //  Otherwise show this variation product is out of stock
                        } else {
                            /*  Show the product name, and indicate that this product is out of stock  */
                            $response .= ' (out of stock)';
                        }
                    }
                }

                $response .= "\n";
            }
        } else {
            /*  If we don't have anymore options to list  */
            $response .= count($this->variable_options) ? "\nNo more options to show.\n\n" : "\nNo options found :(\n\n";
        }

        return $this->displayCustomGoBackPage($response, $include_line_breaker = false);
    }

    /*  displayProductQuantityPage()
     *  This is the page displayed when a user must select a product quantity
     */
    public function displayProductQuantityPage()
    {
        $response = 'How many do you want (Quantity) of "'.$this->selected_product['name']."\"\n\n";
        $response .= 'Select between 1 and '.$this->maximum_item_quantity."\n";

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayCartSummaryPage()
     *  This is the page displayed when a user to show them a summary of what they
     *  are about to purchase at that point in time.
     */
    public function displayCartSummaryPage()
    {
        $cart_total = 'Total: '.$this->currency.$this->convertToMoney($this->cart['grand_total']);
        $cart_items = 'Items: '.$this->cart['items_summarized_inline'];

        $summary_text = $this->summarize($cart_total.' '.$cart_items, 100);
        $response = $summary_text."\n";
        $response .= '1. Pay Now';

        $response = $this->displayCustomGoBackPage($response);

        $response .= $this->canAddMoreItems() ? "\nEnter # to add another item\n" : '';

        return $response;
    }

    /*  displayPaymentOptionsPage()
     *  This is the page displayed when a user must select a payment method
     */
    public function displayPaymentOptionsPage()
    {
        $cart_total = $this->currency.$this->convertToMoney($this->cart['grand_total']);
        $cart_items = $this->cart['items_summarized_inline'];

        $summary_text = $this->summarize('You are paying '.$cart_total.' for '.$cart_items, 100);
        $response = $summary_text.". Select payment method\n";
        $response .= "1. Airtime\n";
        $response .= '2. Orange Money';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayAirtimePaymentConfirmationPage()
     *  This is the page displayed when a user must confirm payment using Airtime
     */
    public function displayAirtimePaymentConfirmationPage()
    {
        $cart_total = $this->currency.$this->convertToMoney($this->cart['grand_total']);
        $cart_items = $this->cart['items_summarized_inline'];
        $service_fee = $this->currency.$this->convertToMoney($this->getServiceFee());

        $summary_text = $this->summarize('You are paying '.$cart_total.' for '.$cart_items, 100);
        $response = $summary_text.' using Airtime. You will be charged ('.$service_fee.") as a service fee. Please confirm\n";
        $response .= '1. Confirm';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayOrangeMoneyPaymentConfirmationPage()
     *  This is the page displayed when a user must confirm payment using Orange Money
     */
    public function displayOrangeMoneyPaymentConfirmationPage()
    {
        $cart_total = $this->currency.$this->convertToMoney($this->cart['grand_total']);
        $cart_items = $this->cart['items_summarized_inline'];
        $service_fee = $this->currency.$this->convertToMoney($this->getServiceFee());

        $summary_text = $this->summarize('You are paying '.$cart_total.' for '.$cart_items, 100);
        $response = $summary_text.' using Orange Money. You will be charged ('.$service_fee.") as a service fee. Please confirm\n";
        $response .= '1. Enter pin to confirm';

        return $this->displayCustomGoBackPage($response);
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

    /*  displayViewAllOrdersOrSearchOrderPage()
     *  This is the page where the user must select whether they want
     *  view all orders or search for a specific order
     */
    public function displayViewAllOrdersOrSearchOrderPage()
    {
        $response = "Select option:\n";
        $response .= "1. Recent Orders\n";
        $response .= '2. Search Order';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayOrderListPage()
     *  This is the page where the user must select a specific order
     *  from all available orders listed.
     */
    public function displayOrderListPage()
    {
        if (count($this->orders_on_display)) {
            $response = "Select Order:\n";

            foreach ($this->orders_on_display as $key => $order) {
                $number = (++$key);

                $order_number = $order['number'];
                $order_date = (new \Carbon\Carbon($order['created_date']))->format('d/m/Y');

                $response .= $number.'. Order #'.$order_number.' ('.$order_date.')'."\n";
            }

            $response .= '99. Show More';

            $response = $this->displayCustomGoBackPage($response);
        } else {
            $response = count($this->orders) ? "No more orders to show.\n" : "No orders found.\n";

            $response = $this->displayCustomGoBackPage($response);
        }

        return $response;
    }

    /*  displayOrderSummaryPage()
     *  This is the page where the order summary is displayed.
     *  It shows the order details
     */
    public function displayOrderSummaryPage()
    {
        $order_number = $this->order->number;
        $order_total = $this->currency.$this->convertToMoney($this->order->grand_total);
        $order_date = (new \Carbon\Carbon($this->order['created_date']))->format('M d Y, h:iA');
        $number_of_items = count($this->order->item_lines) ?? 0;

        $response = 'Order #'.$order_number."\n";
        $response .= 'Amount: '.$order_total."\n";
        $response .= 'Date: '.$order_date."\n";
        $response .= "---\n";
        $response .= '1. View Items('.$number_of_items.")\n";
        $response .= '2. View Cost Breakdown';

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayOrderItemsPage()
     *  This is the page where the order items are displayed.
     *  It shows all the items that were placed on the order
     */
    public function displayOrderItemsPage()
    {
        /*  Get the cart items (Array) e.g ["1x(Product 1)", "2x(Product 3)"]  */
        $order_items_array = $this->getOrderItemsInArray();

        $response = 'Order #'.$this->order->number." Items:\n\n";

        foreach ($order_items_array as $item) {
            $response .= $item."\n";
        }

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayOrderCostBreakdownPage()
     *  This is the page where the order cost breakdown is
     *  displayed. It shows the orders Sub-Total, Tax-Total,
     *  Discount-Total and Grand-Total
     */
    public function displayOrderCostBreakdownPage()
    {
        $response = 'Order #'.$this->order->number." Breakdown:\n\n";
        $response .= 'Sub Total ('.$this->convertToMoney($this->order->sub_total).")\n";
        $response .= 'Tax Total ('.$this->convertToMoney($this->order->grand_tax_total).")\n";
        $response .= 'Discount Total ('.$this->convertToMoney($this->order->grand_discount_total).")\n";
        $response .= 'Grand Total ('.$this->convertToMoney($this->order->grand_total).")\n";

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayContactUsPage()
     *  This is the page displayed when the user wants to view the stores
     *  contact us information.
     */
    public function displayContactUsPage()
    {
        /*  Show the store contact us information  */
        $response = $this->ussd_interface['contact_us']."\n";

        return $this->displayCustomGoBackPage($response);
    }

    /*  displayAboutUsPage()
     *  This is the page displayed when the user wants to view the stores
     *  about us information.
     */
    public function displayAboutUsPage()
    {
        /*  Show the store about us information  */
        $response = $this->ussd_interface['about_us']."\n";

        return $this->displayCustomGoBackPage($response);
    }

    /********************************
     *  HELPER METHODS             *
     ********************************/

    public function verifyUssdDetails()
    {
        if (empty($this->phone_number)) {
            /*  Notify the user to provide a mobile number first  */
            return $this->displayCustomErrorPage('Mobile number was not found.');
        } elseif (empty($this->session_id)) {
            /*  Notify the user to provide a mobile number first  */
            return $this->displayCustomErrorPage('Session Id was not found.');
        } elseif (empty($this->service_code)) {
            /*  Notify the user to provide a mobile number first  */
            return $this->displayCustomErrorPage('Service code was not found.');
        }
    }

    public function manageEncodedRequests()
    {
        /*  Get the user's response text value.
         */
        $text = $this->text;

        /*  Assuming the $text value is as follows:
         *
         *  1*001*3*2*%23
         *
         *  Where "%23" is an encoded value representing "#"
         *
         *  We want to convert all encoded values to their
         *  decoded counterparts
         *
         *  Before: 1*001*3*2*%23
         *  After:  1*001*3*2*#
         *
         */
        $responses = explode('*', $this->text);

        for ($x = 0; $x < count($responses); ++$x) {
            $responses[$x] = urldecode($responses[$x]);
        }

        $updated_text = implode('*', $responses);

        $this->text = $updated_text;
    }

    /*  Scan and remove any responses the user indicated to omit. This is to help
     *  simulate the ability for the user to go back to previous screens so that
     *  they can choose another option. This will help the appllication to focus
     *  on the important responses knowing that any irrelevant response was
     *  already removed.
     */
    public function manageGoBackRequests()
    {
        /*  Get the user's response text value.
         */
        $text = $this->text;

        /*  Assuming the $text value is as follows:
         *
         *  1*001*002*003*0*0*0
         *
         *  We can explode it into an array of responses to get
         *
         *  ["1", "001", "002", "003", "0", "0", "0"]
         *
         */
        $responses = explode('*', $this->text);

        /*  Lets count how many times the zero (0) value appears
         *  from the responses we have.
         */
        $count = 0;

        foreach ($responses as $response) {
            if ($response == '0') {
                $count = ++$count;
            }
        }

        /*  Since we now know the number of times the value zero (0) appears on the
         *  user responses, we can loop through each instance knowing that we will
         *  find a zero (0) value. Lets assume we have the following responses
         *
         *  ["1", "001", "002", "003", "0", "0", "0"]
         *
         *  At this point our application can count the number of times the zero (0)
         *  value appears which is 2 times in the above example. This means we need
         *  to setup a looping function that will loop three times where for each
         *  loop we will locate the corresponding zero (0) value. Once any zero (0)
         *  value is located we will remove that zero (0) value and the immediate
         *  value that appears before that zero (0). In our example above we want
         *  that foreach time we loop we create a new loop that we go through all
         *  the response values trying to find the zero (0) value. once the value
         *  is located, we will remove it and then remove the value before. This
         *  is like we are cancelling or making that value non-existent. This will
         *  simulate the idea of going back since we cancel or remove the users
         *  previous response. So for instance in first loop, we will make a loop
         *  go through all the responses and locate a zero (0) and then remove it
         *  and the value before it, we will have the following result
         *
         *  ["1", "001", "002", "0", "0"]
         *
         *  Once we locate that zero value and remove it along with the previous
         *  value, we need to update a special array called $updated_responses
         *  with the new updated responses. After the first loop we have:
         *
         *  $updated_responses Before = ["1", "001", "002", "003", "0", "0", "0"]
         *  $updated_responses After  = ["1", "001", "002", "0", "0"]
         *
         *  On the second loop we have
         *
         *  $updated_responses Before = ["1", "001", "002", "0", "0"]
         *  $updated_responses After  = ["1", "001", "0"]
         *
         *  $updated_responses Before = ["1", "001", "0"]
         *  $updated_responses After  = ["1"]
         *
         *  In the end the result will be:
         *
         *  $updated_responses After = ["1"]
         *
         *  This makes sense because we started with three zero (0) values. Each
         *  zero (0) value was meant to cancel out each previous response thereby
         *  simulating a go back functionality
         *
         */

        $updated_responses = $responses;

        for ($x = 0; $x < $count; ++$x) {
            for ($y = 0; $y < count($updated_responses); ++$y) {
                if ($updated_responses[$y] == '0') {
                    unset($updated_responses[$y]);

                    if (isset($updated_responses[$y - 1])) {
                        unset($updated_responses[$y - 1]);
                    }

                    $updated_responses = array_values($updated_responses);

                    break;
                }
            }
        }

        /*  Now since we have updated the responses, we need to update the
         *  actual text value so that future methods and functions can use
         *  the updated text responses without any zero (0) values and the
         *  omitted responses.
         */

        $updated_text = implode('*', $updated_responses);

        $this->text = $updated_text;
    }

    public function managePaginationRequests()
    {
        /*  Get the user's response text value.
         */
        $text = $this->text;

        /*  Assuming the $text value is as follows:
         *
         *  1*001*99*99*99
         *
         *  We can explode it into an array of responses to get
         *
         *  ["1", "001", "99", "99", "99"]
         *
         */
        $responses = explode('*', $this->text);

        /*  Since we now know the number of times the value zero (0) appears on the
         *  user responses, we can loop through each instance knowing that we will
         *  find a zero (0) value. Lets assume we have the following responses
         *
         *  ["1", "001", "002", "003", "0", "0", "0"]
         *
         *  At this point our application can count the number of times the zero (0)
         *  value appears which is 2 times in the above example. This means we need
         *  to setup a looping function that will loop three times where for each
         *  loop we will locate the corresponding zero (0) value. Once any zero (0)
         *  value is located we will remove that zero (0) value and the immediate
         *  value that appears before that zero (0). In our example above we want
         *  that foreach time we loop we create a new loop that we go through all
         *  the response values trying to find the zero (0) value. once the value
         *  is located, we will remove it and then remove the value before. This
         *  is like we are cancelling or making that value non-existent. This will
         *  simulate the idea of going back since we cancel or remove the users
         *  previous response. So for instance in first loop, we will make a loop
         *  go through all the responses and locate a zero (0) and then remove it
         *  and the value before it, we will have the following result
         *
         *  ["1", "001", "002", "0", "0"]
         *
         *  Once we locate that zero value and remove it along with the previous
         *  value, we need to update a special array called $updated_responses
         *  with the new updated responses. After the first loop we have:
         *
         *  $updated_responses Before = ["1", "001", "002", "003", "0", "0", "0"]
         *  $updated_responses After  = ["1", "001", "002", "0", "0"]
         *
         *  On the second loop we have
         *
         *  $updated_responses Before = ["1", "001", "002", "0", "0"]
         *  $updated_responses After  = ["1", "001", "0"]
         *
         *  $updated_responses Before = ["1", "001", "0"]
         *  $updated_responses After  = ["1"]
         *
         *  In the end the result will be:
         *
         *  $updated_responses After = ["1"]
         *
         *  This makes sense because we started with three zero (0) values. Each
         *  zero (0) value was meant to cancel out each previous response thereby
         *  simulating a go back functionality
         *
         */

        $updated_responses = $responses;

        while (in_array('99', $updated_responses)) {
            for ($x = 0; $x < count($updated_responses); ++$x) {
                if ($updated_responses[$x] == '99') {
                    $values_to_remove = [];

                    $occurrence = 1;

                    /*  Loop starting from the next response after the current one up until we
                     *  reach the last response available. If that next response is also equal
                     *  to 99 then increment occurence and unset (remove) that value. Keep
                     *  repeating this foreach value until we get a value that is not equal
                     *  to 99. At that point we break (stop) the loop since we only increment
                     *  the occurence for corresponding values e.g 99*99*99...
                     *
                     *  Therefore if we have the following
                     *
                     *  99  Continue - Incremrent occurence and unset value
                     *  99  Continue - Incremrent occurence and unset value
                     *  99  Continue - Incremrent occurence and unset value
                     *  3   Stop     - Break the loop
                     *
                     */
                    for ($y = ($x + 1); $y < count($updated_responses); ++$y) {
                        if ($updated_responses[$y] == '99') {
                            $occurrence = $occurrence + 1;

                            array_push($values_to_remove, $y);
                        } else {
                            break 1;
                        }
                    }

                    $updated_responses[$x] = '99_'.$occurrence;

                    foreach ($values_to_remove as $position) {
                        unset($updated_responses[$position]);
                    }

                    $updated_responses = array_values($updated_responses);

                    break;
                }
            }
        }

        /*  Now since we have updated the responses, we need to update the
         *  actual text value so that future methods and functions can use
         *  the updated text responses without any zero (0) values and the
         *  omitted responses.
         */

        $updated_text = implode('*', $updated_responses);

        $this->text = $updated_text;
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

    /*  wantsToEnterStoreCode()
     *  Returns true/false of whether the user wants to enter their Store Code
     */
    public function wantsToEnterStoreCode()
    {
        /*  If the user responded to the "Main landing page" (Level 1) with
         *  the option (1) then the user wants to enter their Store Code.
         */
        return  $this->completedLevel(1) && $this->getResponseFromLevel(1) == '1';
    }

    /*  hasProvidedStoreCode()
     *  Returns true/false of whether the user already provided the Store Code
     */
    public function hasProvidedStoreCode()
    {
        /*  If the user already responded to the "Enter Store Code Page" (Level 2)
         *  then the user wants to search for a store.
         */
        return  $this->completedLevel(2);
    }

    /*  isValidStoreCode()
     *  Returns true/false if a USSD Interface with the specified Store Code exists
     */
    public function isValidStoreCode()
    {
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

        return false;
    }

    /*  getProvidedStoreCode()
     *  Returns the provided Store Code e.g 001, 002, e.t.c
     */
    public function getProvidedStoreCode()
    {
        /*  If the user already responded to the "Enter Store Code Page" (Level 2)
         *  then we can return the Store Code that was provided.
         */
        return  $this->getResponseFromLevel(2);
    }

    /*  getUssdInterface()
     *  Returns the Ussd Interface that matches the store code provided
     */
    public function getUssdInterface($store_code = null)
    {
        if ($store_code) {
            /*  Get the USSD Interface that uses ussd store code  */
            return UssdInterface::where('code', $store_code)->first() ?? null;
        }
    }

    /*  wantsToSearchStore()
     *  Returns true/false of whether the user wants to search for a store
     */
    public function wantsToSearchStore()
    {
        /*  If the user responded to the "Main landing page" (Level 1) with
         *  the option (2) then the user wants to search for a store.
         */
        return  $this->completedLevel(1) && $this->getResponseFromLevel(1) == '2';
    }

    public function getMyStores()
    {
        $this->contact = ( new \App\Contact)->findByPhone($this->user['phone']);

        if( $this->contact ){

            return $this->contact->stores()->get();

        }

        return [];

    }

    public function getPopularStores()
    {
        return (new \App\Store)->supportUssd()->popular()->get();
    }

    /*  hasSelectedHowToSearchStore()
     *  Returns true/false of whether the user has already selected an option
     *  of how they want to search for stores
     */
    public function hasSelectedHowToSearchStore()
    {
        /*  If the user responded to the "Find Store Page" (Level 2)
         *  with any option.
         */
        return  $this->completedLevel(2);
    }

    /*  wantsToSearchMyFavouriteStores()
     *  Returns true/false of whether the user wants to search for 
     *  favourite stores
     */
    public function wantsToSearchMyFavouriteStores()
    {
        /*  If the user responded to the "Find Stores Page" (Level 2) with the
         *  option (1) then the user wants to search for a favourite store.
         */
        return  $this->completedLevel(2) && $this->getResponseFromLevel(2) == '1';
    }
    
    /*  wantsToSearchPopularStores()
     *  Returns true/false of whether the user wants to search for 
     *  popular stores
     */
    public function wantsToSearchPopularStores()
    {
        /*  If the user responded to the "Find Stores Page" (Level 2) with the
         *  option (2) then the user wants to search for a popular store.
         */
        return  $this->completedLevel(2) && $this->getResponseFromLevel(2) == '2';
    }

    public function wantsToPaginateStoresPage()
    {
        /*  Get the selected store option from the "Stores Page" (Level 3)
         *  If the option contains the value "99_" then the user wants to
         *  paginate the stores page
         */
        return  $this->completedLevel(3) && substr($this->getResponseFromLevel(3), 0, 3) == '99_';
    }

    public function getFirstStoresToList()
    {
        $start_position = 0;

        return collect($this->stores)->slice($start_position, $this->stores_per_page);
    }

    public function getPaginatedStoresToList()
    {
        $response = $this->getResponseFromLevel(3);
        $number_of_times_to_paginate = substr($response, 3, 5);

        $start_position = ($number_of_times_to_paginate * $this->stores_per_page);

        return collect($this->stores)->slice($start_position, $this->stores_per_page);
    }

    /*  hasSelectedStore()
     *  Returns true/false of whether the user has already selected an option
     *  representing a store to visit
     */
    public function hasSelectedStore()
    {
        /*  If the user responded to the "Stores Page" (Level 3)
         *  with any option.
         */
        return  $this->completedLevel(3 + $this->offset);
    }
    

    /*  hasSelectedStoreCategory()
     *  Returns true/false of whether the user has already selected a store category
     */
    public function hasSelectedStoreCategory()
    {
        /*  If the user responded to the "Select Store Category Page" (Level 2)
         *  with a specific category of choice.
         */
        return  $this->completedLevel(2);
    }

    /*  hasSelectedStoreFromCategory()
     *  Returns true/false of whether the user has already selected a store
     *  after selecting a store category
     */
    public function hasSelectedStoreFromCategory()
    {
        /*  If the user responded to the "Select Category Store Page" (Level 3)
         *  with a specific store of choice.
         */
        return  $this->completedLevel(3);
    }

    /*  hasSelectedStoreLandingPageOption()
     *  Returns true/false of whether the user has already selected an
     *  option from the "Store Landing Page". This is the page that
     *  allows the user to select the store Call To Action, About Us,
     *  Contact Us, e.t.c
     */
    public function hasSelectedStoreLandingPageOption()
    {
        /*  If the user responded to the "Select Category Store Page" (Level 3)
         *  with a specific store of choice.
         */
        return  $this->completedLevel(3);
    }

    /*  wantsToStartShopping()
     *  Returns true/false of whether the user wants to start shopping.
     *  This means the user selected the store Call To Action option
     *
     */
    public function wantsToStartShopping()
    {
        /*  If the user responded to the "Store landing page" (Level 3) with
         *  the option (1) then the user wants to start shopping.
         */
        return  $this->completedLevel(3) && $this->getResponseFromLevel(3) == '1';
    }

    /*  wantsToViewMyOrders()
     *  Returns true/false of whether the user wants to view their
     *  past orders
     */
    public function wantsToViewMyOrders()
    {
        /*  If the user responded to the "Store landing page" (Level 3) with
         *  the option (2) then the user wants to view their past orders.
         */
        return  $this->completedLevel(3) && $this->getResponseFromLevel(3) == '2';
    }

    /*  wantsToViewAboutUs()
     *  Returns true/false of whether the user wants to view the store
     *  Contact Us information
     */
    public function wantsToViewContactUs()
    {
        /*  If the user responded to the "Store landing page" (Level 3) with
         *  the option (3) then the user wants to view the stores Contact Us
         *  information.
         */
        return  $this->completedLevel(3) && $this->getResponseFromLevel(3) == '3';
    }

    /*  wantsToViewAboutUs()
     *  Returns true/false of whether the user wants to view the store
     *  About Us information
     */
    public function wantsToViewAboutUs()
    {
        /*  If the user responded to the "Store landing page" (Level 3) with
         *  the option (4) then the user wants to view the stores About Us
         *  information.
         */
        return  $this->completedLevel(3) && $this->getResponseFromLevel(3) == '4';
    }

    /*  hasSelectedOrderHomePageOption()
     *  Returns true/false of whether the user has already selected
     *  an order option
     */
    public function hasSelectedOrderHomePageOption()
    {
        /*  If the user responded to the "My Orders Page" (Level 4)
         *  with any available option
         */
        return  $this->completedLevel(4);
    }

    /*  wantsToViewAllOrders()
     *  Returns true/false of whether the user wants to view all their
     *  past orders
     */
    public function wantsToViewAllOrders()
    {
        /*  If the user responded to the "My Orders Page" (Level 4) with
         *  the option (1) then the user wants to view all their past
         *  orders
         */
        return  $this->completedLevel(4) && $this->getResponseFromLevel(4) == '1';
    }

    public function wantsToPaginateOrdersPage()
    {
        /*  Get the selected order option from the Order Page (Level 3)
         *  If the option contains the value "99_" then the user wants to
         *  paginate the order page
         */
        return  $this->completedLevel(5) && substr($this->getResponseFromLevel(5), 0, 3) == '99_';
    }

    public function getFirstOrdersToList()
    {
        $start_position = 0;

        return collect($this->orders)->slice($start_position, $this->order_per_page);
    }

    public function getPaginatedOrdersToList()
    {
        $response = $this->getResponseFromLevel(5);
        $number_of_times_to_paginate = substr($response, 3, 5);

        $start_position = ($number_of_times_to_paginate * $this->order_per_page);

        return collect($this->orders)->slice($start_position, $this->order_per_page);
    }

    /*  hasSelectedOrder()
     *  Returns true/false of whether the user has already selected
     *  a specific order from the order list
     */
    public function hasSelectedOrder()
    {
        /*  If the user responded to the "Select Order Page" (Level 5)
         *  by selecting a specific order.
         */
        return  $this->completedLevel(5 + $this->offset);
    }

    /*  wantsToSearchOrders()
     *  Returns true/false of whether the user wants to search for
     *  a specific order
     */
    public function wantsToSearchOrders()
    {
        /*  If the user responded to the "My Orders Page" (Level 4) with
         *  the option (2) then the user wants to search for a past
         *  order
         */
        return  $this->completedLevel(4) && $this->getResponseFromLevel(4) == '2';
    }

    public function getMyOrders()
    {
        /*  Check if a contact using the same mobile number exists for the store  */
        $storeContact = $this->store->contactsWithMobilePhone($this->user['phone'])->first();

        /*  If a contact was found  */
        if ($storeContact) {

            /*  Return the store orders where this contact is recognised as the order customer or reference  */
            return $this->store->contactOrders($storeContact->id)->get() ?? [];

        }

        return [];
    }

    public function getSelectedOrder()
    {
        /*  Get the selected order option from the "Select Order Page" (Level 5).
         *  We can use the selected option to retrieve the order.
         */

        /*  Get the selected option and convert it to an interger  */
        $selected_order_option = (int) $this->getResponseFromLevel(5 + $this->offset);

        /*  If we have a selected order option (e.g 1, 2 or 3)  */
        if ($selected_order_option) {
            /*  Retrieve the actual order that was selected. Note that the user would have
             *  replied "1" to select the first order on the list. However the first order
             *  on "$this->orders" variable is of index "0", this means we need to always
             *  subtract "1" from the user reply to access the correct order.
             */
            return $this->orders[$selected_order_option - 1] ?? null;
        }
    }

    /*  hasProvidedOrderNumberToSearch()
     *  Returns true/false of whether the user has already selected a store
     *  after selecting a store category
     */
    public function hasProvidedOrderNumberToSearch()
    {
        /*  If the user responded to the "Enter Order Number Page" (Level 5)
         *  by providing the order number.
         */
        return  $this->completedLevel(5);
    }

    public function getOrderNumber()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 5) by providing the order number, we can get the
         *  provided order number
         */
        return  $this->getResponseFromLevel(5);
    }

    public function getSearchedOrder()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 5) by providing the order number, we can get the order
         *  that matches the provided order number
         */

        /*  Get the order number provided by the user  */
        $order_number = $this->getOrderNumber();

        return collect($this->orders)->where('number', $order_number)->first() ?? null;
    }

    public function searchedOrderExists()
    {
        /*  If the user already responded to the "Enter Order Number Page"
         *  (Level 6) by providing the order number, we can check if the
         *  order actually exists
         */
        return  !empty($this->getSearchedOrder()) ? true : false;
    }

    public function hasSelectedMyOrderSummaryOption()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 6)
         *  by selecting any available option.
         */
        return  $this->completedLevel(6 + $this->offset);
    }

    public function hasSelectedViewOrderItems()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 6)
         *  by selecting option "1" being the "View Order Items" option.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '1';
    }

    public function hasSelectedViewOrderCostBreakdown()
    {
        /*  If the user already responded to the "Order Summary Page" (Level 7)
         *  by selecting option "3" being the "View Order Cost Breakdown" option.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '2';
    }

    public function wantsToPaginateProductsPage()
    {
        /*  Get the selected product option from the Product Page (Level 3)
         *  If the option contains the value "99_" then the user wants to
         *  paginate the products page
         */
        return  $this->completedLevel(4 + $this->offset) && substr($this->getResponseFromLevel(4 + $this->offset), 0, 3) == '99_';
    }

    public function getFirstProductsToList()
    {
        $start_position = 0;

        return collect($this->products)->slice($start_position, $this->products_per_page);
    }

    public function getPaginatedProductsToList()
    {
        $response = $this->getResponseFromLevel(4 + $this->offset);
        $number_of_times_to_paginate = substr($response, 3, 5);

        $start_position = ($number_of_times_to_paginate * $this->products_per_page);

        return collect($this->products)->slice($start_position, $this->products_per_page);
    }

    public function hasSelectedProduct()
    {
        /*  If the user already responded to the "Store Landing Page" (Level 3)
         *  by selecting a product.
         */
        return  $this->completedLevel(4 + $this->offset);
    }

    public function getSelectedProduct()
    {
        /*  Get the selected product option from the "Store Landing Page (Select A Product Page)"
         *  (Level 3). We can use the selected option to retrieve the product.
         */

        /*  Get the selected option and convert it to an interger  */
        $selected_product_option = (int) $this->getResponseFromLevel(4 + $this->offset);

        /*  If we have a selected product option (e.g 1, 2 or 3)  */
        if ($selected_product_option) {
            /*  Retrieve the actual product that was selected. Note that the user would have
             *  replied "1" to select the first product on the list. However the first
             *  product on "$this->products" variable is of index "0", this means we need
             *  to always subtract "1" from the user reply to access the correct product.
             */
            return $this->products[$selected_product_option - 1] ?? null;
        }
    }

    public function hasVariables($product = null)
    {
        /*  Get the provided product otherwise get the selected product */
        $product = $product ?? $this->selected_product;

        /*  If we have the actual product that was selected  */
        if ($product) {

            /*  Determine if the selected product has variants  */
            return  $product['allow_variants'] == true && $product->variations()->count();
        }

        return false;
    }

    public function wantsToPaginateVariantOptionsPage()
    {
        /*  Get the selected variant option from the "Select Variamt Page" (Level 4++)
         *  If the option contains the value "99_" then the user wants to paginate the
         *  variant options page
         */
        return  $this->completedLevel(4 + $this->offset) && substr($this->getResponseFromLevel(4 + $this->offset), 0, 3) == '99_';
    }

    public function getFirstVariantOptionsToList()
    {
        $start_position = 0;

        return collect($this->variable_options)->slice($start_position, $this->variable_options_per_page);
    }

    public function getPaginatedVariantOptionsToList()
    {
        $response = $this->getResponseFromLevel(4 + $this->offset);
        $number_of_times_to_paginate = substr($response, 3, 5);

        $start_position = ($number_of_times_to_paginate * $this->variable_options_per_page);

        return collect($this->variable_options)->slice($start_position, $this->variable_options_per_page);
    }

    public function hasSelectedProductVariantPageOption()
    {
        /*  If the user already responded to the "Select Product Variable Option Page" (Level 4++)
         *  by selecting a specific product variant option.
         */
        return  $this->completedLevel(4 + $this->offset);
    }

    public function getSelectedVariableOption()
    {
        /*  If the user already responded to the "Select Product Variables Page" (Level 4++)
         *  by selecting a specific product variant option. We can return the selected option.
         */

        /*  Get the selected number option e.g 1, 2 or 3  */
        $selected_number_option = $this->getResponseFromLevel(4 + $this->offset);

        /*  Get the selected attribute option e.g Small, Medium or Large  */
        $selected_attribute_option = $this->variable_options[$selected_number_option - 1] ?? null;

        if ($selected_attribute_option) {
            /*  Get the selected attribute name and option e.g ['size' => 'Small'] or ['color' => 'Blue']  */
            return [$this->variant_attribute_name => $selected_attribute_option];
        }
    }

    public function getSelectedProductVariation($additional_variable_options = [])
    {
        $product_variations = $this->selected_product->variations();

        if (count($additional_variable_options)) {
            foreach ($additional_variable_options as $option_name => $option_value) {
                $product_variations = $product_variations->whereHas('variables', function (Builder $query) use ($option_name, $option_value) {
                    $query->where('name', $option_name)
                                ->where('value', $option_value);
                });
            }
        }

        $previously_selected_variable_options = $this->selected_variable_options;

        if (count($previously_selected_variable_options)) {
            foreach ($previously_selected_variable_options as $selected_variable_option) {
                foreach ($selected_variable_option as $option_name => $option_value) {
                    /*  This is the selected variant option name and value.
                    *  For example if the user is selecting a T-shirt:
                    *
                    *  $option_name = size and $option_value = small
                    *  $option_name = color and $option_value = blue
                    *  $option_name = material and $option_value = cotton
                    */
                    $product_variations = $product_variations->whereHas('variables', function (Builder $query) use ($option_name, $option_value) {
                        $query->where('name', $option_name)
                                                ->where('value', $option_value);
                    });
                }
            }
        }

        return $product_variations->first();
    }

    public function hasSelectedProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a specific product quantity.
         */
        return  $this->completedLevel(5 + $this->offset);
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

    public function getSelectedProductQuantity()
    {
        /*  If the user already responded to the "Select Product Quantity Page" (Level 5)
         *  by providing a specific product quantity. We can return this quantity
         */
        return  $this->getResponseFromLevel(5 + $this->offset);
    }

    public function hasSelectedOrderSummaryOption()
    {
        /*  If the user already responded to the "Cart Summary Page" (Level 6)
         *  by selecting any available option.
         */
        return  $this->completedLevel(6 + $this->offset);
    }

    public function wantsToAddAnotherProduct()
    {
        /*  If the user already responded to the Cart summary page (Level 6)
         *  by selecting option (1) for pay now.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '#';
    }

    public function wantsToPay()
    {
        /*  If the user already responded to the Cart summary page (Level 6)
         *  by selecting option (1) for pay now.
         */
        return  $this->completedLevel(6 + $this->offset) && $this->getResponseFromLevel(6 + $this->offset) == '1';
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

    public function hasSelectedAirtimeConfirmationOption()
    {
        /*  If the user already responded to the "Confirm Payment Using Airtime Page" (Level 7)
         *  by selecting any option.
         */
        return  $this->completedLevel(8 + $this->offset);
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

    public function getOrderItemsInArray()
    {
        /*  Get the cart items as an (Array) e.g ["1x(Product 1)", "2x(Product 3)"]  */
        $order_items_array = ( new \App\MyCart() )->getItemsSummarizedInArray($this->order->item_lines);

        return $order_items_array;
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

    public function hasStock($product)
    {
        return ($product['stock_quantity'] > 0 && $product['has_inventory']) || !$product['has_inventory'];
    }

    public function isOnSale($product)
    {
        return !empty($product['unit_sale_price']) ? true : false;
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

        //  return $this->simulateUserReply($reply = $new_text, $replace_previous_replies = true);
    }

    public function hasVisitedBefore()
    {
        /*  Checks if the user has visited the store before. Anymore than one
         *  confirms that the user has visited the store before.
         */
        return  $this->visit > 1;
    }

    public function summarize($text, $limit)
    {
        return strlen($text) > $limit ? substr($text, 0, $limit + 3).'...' : $text;
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

    public function convertToMoney($amount = 0)
    {
        return number_format($amount, 2, '.', ',');
    }
}
