<?php

namespace App\Traits;

use Cart;
use App\Product;
use Illuminate\Http\Request;

trait MyCartTraits
{

    /*  initiateGetAll() method:
     *
     *  This is used to return all the cart items.
     *
     */
    public function initiateGetAll()
    {
        try{

            $store = [];
            $cartContent = Cart::content();

            //  Get the cart product ids
            $cartContentIds = collect( $cartContent )->map(function ($item) {
                                    return $item->id;
                                })->values() ?? [];
                            
            //  If we have products in the cart
            if( count($cartContentIds) ){

                //  Fetch the products listed in the cart
                $cartItems = Product::whereIn('id', $cartContentIds)->get();
    
                //  Foreach product returned matching a cart item
                $cartItems->map(function ($item) use ($cartContent)  {
                    //  Foreach item in the actual cart
                    foreach( $cartContent as $x => $itemContent){
                        // If the id of returned product matches that of the cart item
                        if( $itemContent->id == $item->id){
                            //  Add the cart row id to the product
                            $item['cart_row_id'] = $itemContent->rowId;
                            //  Add the cart quantity to the product
                            $item['quantity'] = $itemContent->qty;
                            //  Identify the product as added to cart
                            $item['inside_cart'] = true;
                            return $item;
                        }
                    }
                });
            }else{
                //  Otherwise we don't have any cart items
                $cartItems = [];
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $cartItems];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    public function initiateGetCartDetails( $storeId )
    {
        try{

            //  Get the store and settings
            $store = Store::find( $storeId );
            $storeSettings = $store->settings;

            $cart_items = $this->initiateGetAll()['response'];

            //  Total of only the cart items combined
            $sub_total = 0;

            //  Total of only the cart taxes combined
            $cart_tax_total = 0;

            //  Total of only the shop taxes combined
            $shop_tax_total = 0;

            //  Total of only the shop taxes and cart taxes combined
            $grand_tax_total = 0;

            //  Total of only the cart discounts combined
            $cart_discount_total = 0;

            //  Total of only the shop discounts combined
            $shop_discount_total = 0;

            //  Total of only the shop discounts and cart discounts combined
            $grand_discount_total = 0;

            //  The total of shipping costs
            $shipping_total = 0;

            //  The total of all the cart item costs and taxes
            $grand_total = 0;

            foreach($cart_items as $key => $item){
                
                //  If we have a sale price then use the sale price otherwise use the regular price
                $price = !empty($item['unit_sale_price']) ? $item['unit_sale_price'] : $item['unit_price'];
                $item_total_price = ($price * $item['quantity']);

                //  Calculate the sub total 
                $sub_total += $item_total_price;

                //  Calculate the cart item taxes
                foreach($item['taxes'] as $key => $tax){
                    $cart_tax_total += $tax['rate'] * $item_total_price;
                }

                //  Calculate the cart item discounts
                foreach($item['discounts'] as $key => $discount){

                    //  If its a percentage rate based discount
                    if( $discount['type'] == 'rate' ){

                        $cart_discount_total += $discount['rate'] * $item_total_price;

                    //  If its a fixed rate based discount
                    }else if( $discount['type'] == 'fixed' ){

                        $cart_discount_total += $discount['amount'] * $item_total_price;

                    }
                }

            }

            //  Calculate the shop taxes
            foreach($shop['taxes'] as $key => $tax){
                $shop_tax_total += $tax['rate'] * $sub_total;
            }

            //  Calculate the shop discounts
            foreach($shop['discounts'] as $key => $discount){

                //  If its a percentage rate based discount
                if( $discount['type'] == 'rate' ){

                    $shop_discount_total += $discount['rate'] * $sub_total;

                //  If its a fixed rate based discount
                }else if( $discount['type'] == 'fixed' ){

                    $shop_discount_total += $discount['amount'] * $sub_total;

                }
            }

            //  Calculate the grand total tax
            $grand_tax_total = $cart_tax_total + $shop_tax_total;

            //  Calculate the grand total discount
            $grand_discount_total = $cart_discount_total + $shop_discount_total;

            //  Calculate the grand total
            $grand_total = $sub_total + $grand_tax_total + $shipping_total - $grand_discount_total;

            $cartDetails = [
                'items' => $cart_items,
                'cart_tax_total' => $cart_tax_total,
                'shop_tax_total' => $shop_tax_total,
                'grand_tax_total' => $grand_tax_total,
                'cart_discount_total' => $cart_discount_total,
                'shop_discount_total' => $shop_discount_total,
                'grand_discount_total' => $grand_discount_total,
                'grand_total' => $grand_total,
            ];

            //  Action was executed successfully
            return ['success' => true, 'response' => $cartDetails];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateCreate() method:
     *
     *  This is used to create a new product. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the product.
     *
     */
    public function initiateCreate($id=null, $name=null, $quantity=null, $unit_price=null)
    {
        try {
            
            $id = $id ?? request('id');
            $name = $name ?? request('name');
            $quantity = $quantity ?? request('quantity');
            $unit_price = $unit_price ?? (!empty(request('unit_sale_price')) ? request('unit_sale_price') : request('unit_price'));
    
            //  Add item to cart
            $item = Cart::add($id, $name, $quantity, $unit_price)->associate('App\Product');

            //  Action was executed successfully
            return ['success' => true, 'response' => $item];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing product. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the product.
     *
     */
    public function initiateUpdate($item_id, $title=null, $quantity=null, $unit_price=null)
    {
        try {
            
            $title = $title ?? request('title');
            $quantity = $quantity ?? request('quantity');
            $unit_price = $unit_price ?? (!empty(request('sale_price')) ? request('sale_price') : request('unit_price'));
    
            //  Product Instance
            $item = Cart::update($item_id, ['name' => $title, 'qty' => $quantity, 'price' => $unit_price]);

            //  Action was executed successfully
            return ['success' => true, 'response' => $item];
            
        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

    public function initiateDelete($item_id)
    {
        try {
            
            //  Remove item from the cart
            Cart::remove($item_id);

            //  Action was executed successfully
            return ['success' => true, 'response' => null];
            
        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

    public function initiateEmptyCart()
    {
        try {
            
            //  Completely empty or delete the cart
            Cart::destroy();

            //  Action was executed successfully
            return ['success' => true, 'response' => null];
            
        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

    public function initiateGetCartGrandTotal()
    {
        try {
            
            //  Get the calculated cart total
            $total = Cart::total();

            //  Action was executed successfully
            return ['success' => true, 'response' => $total];
            
        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

    public function initiateGetCartSubTotal()
    {
        try {
            
            //  Get the calculated sub total
            $total = Cart::subtotal();

            //  Action was executed successfully
            return ['success' => true, 'response' => $total];
            
        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

    public function initiateGetCartTax()
    {
        try {
            
            //  Get the calculated tax total
            $tax = Cart::tax();

            //  Action was executed successfully
            return ['success' => true, 'response' => $tax];
            
        } catch (\Exception $e) {

            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        }
    }

}
