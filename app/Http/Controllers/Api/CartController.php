<?php

namespace App\Http\Controllers\Api;

use App\MyCart;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        //  MyCart Instance
        $data = ( new MyCart() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the cart items were found successfully
        if ($success) {
            //  If this is a success then we have the list of items
            $cartItems = $response;

            //  Action was executed successfully
            return oq_api_notify($cartItems, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store()
    {
        //  MyCart Instance
        $data = ( new MyCart() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the item was stored successfully
        if ($success) {
            //  If this is a success then we have the cart item
            $cartItem = $response;

            //  Action was executed successfully
            return oq_api_notify($cartItem, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($item_id)
    {
        //  MyCart Instance
        $data = ( new MyCart() )->initiateUpdate($item_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the item was updated successfully
        if ($success) {
            //  If this is a success then we have the cart item
            $cartItem = $response;

            //  Action was executed successfully
            return oq_api_notify($cartItem, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function delete($item_id)
    {
        //  MyCart Instance
        $data = ( new MyCart() )->initiateDelete($item_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the item was deleted successfully
        if ($success) {
            //  Action was executed successfully
            return oq_api_notify($response, 200);
        }

        //  If the data was not a success then return the response
        return $response;

    }

    public function empty()
    {
        //  MyCart Instance
        $data = ( new MyCart() )->initiateEmptyCart();
        $success = $data['success'];
        $response = $data['response'];

        //  If the item was deleted successfully
        if ($success) {
            //  Action was executed successfully
            return oq_api_notify($response, 200);
        }

        //  If the data was not a success then return the response
        return $response;

    }

}
