<?php

namespace App\Http\Controllers\Api;

use App\Order;
use App\Company;
use App\CompanyBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        //  Order Instance
        $data = ( new Order() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the orders were found successfully
        if ($success) {
            //  If this is a success then we have the orders
            $orders = $response;

            //  Action was executed successfully
            return oq_api_notify($orders, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($order_id)
    {
        //  Order Instance
        $data = ( new Order() )->initiateShow($order_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the order was found successfully
        if ($success) {
            //  If this is a success then we have the order
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the order
        $data = ( new Order() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the order was created successfully
        if ($success) {
            //  If this is a success then we have a order returned
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($order_id)
    {
        //  Order Instance
        $data = ( new Order() )->initiateUpdate($order_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the order was updated successfully
        if ($success) {
            //  If this is a success then we have a order returned
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function saveProofOfPayment(Order $order)
    {
        //  Order Instance
        $data = $order->setLifecycleToVerifyPaymentStatus();
        $success = $data['success'];
        $response = $data['response'];

        //  If the order proof of payment was updated successfully
        if ($success) {
            //  If this is a success then we have a order returned
            $order = $response;

            //  Action was executed successfully
            return oq_api_notify($order, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getDocuments(Request $request, $order_id)
    {
        try {
            
            $type = request('type');      // e.g) payment proof

            //  Get the associated company
            $orderDocuments = Order::find($order_id)->documents();

            if( isset($type) && !empty($type) ){
               //$orderDocuments = $orderDocuments->where('type', $type);
            }

            $orderDocuments = $orderDocuments->get();

            //  Action was executed successfully
            return oq_api_notify($orderDocuments, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

}
