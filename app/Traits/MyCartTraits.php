<?php

namespace App\Traits;

use Cart;
use App\Store;
use App\Product;

trait MyCartTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return all the cart items.
     *
     */
    public function initiateGetAll()
    {
    }

    /*  getCartDetails() method:
     *
     *  This method is used to get the details of the a cart. It requires the merchant
     *  details as well as the items in the cart. The items variable is an array of
     *  individual items with an "id" and "quantity". The method will fetch all the
     *  items by "id" and get their details as well as calculate the individual 
     *  items and cart sub-totals, grand-totals, tax-totals and discounts-totals.
     *  Finally the method will return all the calculations and items.
     */
    public function getCartDetails($merchant, $items)
    {
        try {

            //  Get the cart items
            $cart_items = $this->buildItems($items);

            //  Total of only the cart items combined
            $sub_total = 0;

            //  Total of only the cart taxes combined
            $item_tax_total = 0;

            //  Total of only the store taxes combined
            $global_tax_total = 0;

            //  Total of only the store taxes and cart taxes combined
            $grand_tax_total = 0;

            //  Total of only the cart discounts combined
            $item_discount_total = 0;

            //  Total of only the store discounts combined
            $global_discount_total = 0;

            //  Total of only the store discounts and cart discounts combined
            $grand_discount_total = 0;

            //  The total of shipping costs
            $shipping_total = 0;

            //  The total of all the cart item costs and taxes
            $grand_total = 0;

            //  Foreach cart item
            foreach ($cart_items as $key => $item) {

                /*  Calculate the sub total  */
                $sub_total += $item['sub_total'];

                /*  Calculate the total cart item taxes  */
                $item_tax_total += $item['tax_total'];

                /*  Calculate the total cart item discounts  */  
                $item_discount_total += $item['discount_total'];

            }

            //  Foreach merchant tax, calculate the total merchant tax
            foreach ($merchant->taxes as $key => $tax) {

                $global_tax_total += $tax['rate'] * $sub_total;

            }            

            //  Foreach merchant discount, calculate the total merchant discount
            foreach ($merchant->discounts as $key => $discount) {

                //  If its a percentage rate based discount
                if ($discount['type'] == 'rate') {

                    $global_discount_total += $discount['rate'] * $sub_total;


                //  If its a fixed rate based discount
                } elseif ($discount['type'] == 'fixed') {

                    $global_discount_total += $discount['amount'];

                }
            }

            /*  Calculate the grand total tax  */   
            $grand_tax_total = $item_tax_total + $global_tax_total;

            /*  Calculate the grand total discount  */     
            $grand_discount_total = $item_discount_total + $global_discount_total;

            /*  Calculate the grand total  */     
            $grand_total = $sub_total + $grand_tax_total + $shipping_total - $grand_discount_total;

            $cartDetails = [
                'items' => $cart_items,
                'items_summarized_array' => $this->getItemsSummarizedInArray($cart_items),
                'items_summarized_inline' => $this->getItemsSummarizedInline($cart_items),
                'currency' => $merchant->currency,
                'sub_total' => $this->convertToMoney($sub_total),
                'item_tax_total' => $this->convertToMoney($item_tax_total),
                'global_tax_total' => $this->convertToMoney($global_tax_total),
                'grand_tax_total' => $this->convertToMoney($grand_tax_total),
                'item_discount_total' => $this->convertToMoney($item_discount_total),
                'global_discount_total' => $this->convertToMoney($global_discount_total),
                'grand_discount_total' => $this->convertToMoney($grand_discount_total),
                'shipping_total' => $this->convertToMoney($shipping_total),
                'grand_total' => $this->convertToMoney($grand_total)
            ];

            //  Action was executed successfully
            return $cartDetails;

        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

    public function convertToMoney($amount = 0){

        return number_format($amount, 2, '.', ',');
        
    }

    public function buildItems($items)
    {
        /*  Item Structure
         *  
         *  Each item from the $items array should contain the item price
         *  as well as the item quantity.
         *
         *  $items = [
                ['id'=>1, quantity=>2],
                ['id'=>2, quantity=>3]
            ]
         */

        $cartItems = [];

        //  Lets get the ids of the items in the cart
        $itemIds = collect($items)->map(function ($item) {
            
            return $item['id'];

        });

        //  Lets get the items from the DB that are related to the cart items
        $relatedItems = Product::whereIn('id', $itemIds)->get();

        //  Foreach item
        foreach ($items as $key => $item) {

            //  Foreach related item
            foreach ($relatedItems as $key => $relatedItem) {

                //  If the related item id and the cart item id match
                if ($relatedItem['id'] == $item['id']) {

                    /*  Get the item quantity  */ 
                    $quantity =  $item['quantity'] ?? 1;

                    /*  Update the related item sub total  */   
                    $relatedItem['price'] = $relatedItem['price'];
                    $sub_total = $relatedItem['price'];

                    /*  Get the related item grand total  */  
                    $grand_total = $relatedItem['grand_total'];

                    /*  Build the cart item using the related item information  */
                    $cartItem = collect($relatedItem->toArray())->only([

                        /** Product Details  */
                        'id', 'name', 'description', 'type', 'cost_per_item', 'unit_regular_price', 'unit_sale_price',
                        'sku', 'barcode',

                        /** Attribute Information  */
                        'primary_image', 'unit_price', 'discount_total', 'tax_total', 'sub_total', 'grand_total', 'on_sale',
                        'currency', 'resource_type'

                    ]);

                    /*  Update the details of the cart item to match its quantity  */
                    $cartItem['quantity'] = $quantity;
                    $cartItem['sub_total'] = $cartItem['sub_total'] * $quantity;
                    $cartItem['tax_total'] = $cartItem['tax_total'] * $quantity;
                    $cartItem['discount_total'] = $cartItem['discount_total'] * $quantity;
                    $cartItem['grand_total'] = $cartItem['grand_total'] * $quantity;

                    /*  Add the current cart item to the rest of the cart items  */
                    array_push($cartItems, $cartItem);
                }
            }
        }

        return $cartItems;
    }

    /*  getItemsSummarizedInArrayInArray()
     *  
     * This method returns the cart items listed in an array
     * showing each item with its name and quantity e.g:
     * ["3x(Tomato)", "2x(Anion)", "1x(Garlic)"]
     * 
     */
    public function getItemsSummarizedInArray($items)
    {
        $items_inline = [];

        foreach($items as $key => $item){

            //  Get the item quantity and name then add to array
            $items_inline[$key] = $item['quantity']."x(".ucfirst($item['name']).")";

        }

        return $items_inline;
        
    }

    /*  getItemsSummarizedInline()
     *  
     * This method returns the cart items listed in a single string
     * showing each item with its name and quantity separated with
     * a comma e.g 3x(Tomato), 2x(Anion), 1x(Garlic)
     * 
     */
    public function getItemsSummarizedInline($items)
    {
        $items_inline = '';

        foreach($items as $item){

            //  Get the item quantity and name
            $items_inline .= $item['quantity']."x(".ucfirst($item['name']).")";
            
            //  Separate items using a comma
            $items_inline .= (next($items)) ? ', ' : '';

        }

        return $items_inline;
        
    }


    public function initiateCreate()
    {
    }

    public function initiateUpdateItem()
    {
    }

    public function initiateDeleteItem()
    {
    }

    public function initiateEmptyCart()
    {
    }
}
