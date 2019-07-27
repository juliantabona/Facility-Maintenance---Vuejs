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
            $cartContentIds = collect( $cartContent )->map(function ($item) {
                                    return $item->id;
                                })->values() ?? [];
    
            if( count($cartContentIds) ){
                $cartItems = Product::whereIn('id', $cartContentIds)->get();
    
                $cartItems->map(function ($item) use ($cartContent)  {
                    foreach( $cartContent as $x => $itemContent){
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

    public function initiateGetCartDetails()
    {
        try{

            $cart_items = $this->initiateGetAll()['response'];
            $grand_total = 0;
            $sub_total = 0;
            $cart_tax_total = 0;

            foreach($cart_items as $key => $item){
                
                //  Calculate the sub total
                $sub_total += ($item['unit_price'] * $item['quantity']);

                //  Calculate the grand total
                $grand_total += ($item['unit_price'] * $item['quantity']);

                //  Calculate the cart item taxes
                foreach($item['taxes'] as $key => $tax){
                    $cart_tax_total += $tax['rate'] * ($item['unit_price'] * $item['quantity']);
                }
            }
            
            $cartDetails = [
                'cart_items' => $cart_items,
                'grand_total' => $grand_total,
                'sub_total' => $sub_total,
                'cart_tax_total' => $cart_tax_total,
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
