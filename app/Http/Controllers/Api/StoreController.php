<?php

namespace App\Http\Controllers\Api;

use App\Store;
use App\Company;
use App\CompanyBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function index()
    {
        //  Store Instance
        $data = ( new Store() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the stores were found successfully
        if ($success) {
            //  If this is a success then we have the stores
            $stores = $response;

            //  Action was executed successfully
            return oq_api_notify($stores, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($store_id)
    {
        //  Store Instance
        $data = ( new Store() )->initiateShow($store_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the store was found successfully
        if ($success) {
            //  If this is a success then we have the store
            $store = $response;

            //  Action was executed successfully
            return oq_api_notify($store, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the store
        $data = ( new Store() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the store was created successfully
        if ($success) {
            //  If this is a success then we have a store returned
            $store = $response;

            //  Action was executed successfully
            return oq_api_notify($store, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($store_id)
    {
        //  Store Instance
        $data = ( new Store() )->initiateUpdate($store_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the store was updated successfully
        if ($success) {
            //  If this is a success then we have a store returned
            $store = $response;

            //  Action was executed successfully
            return oq_api_notify($store, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getImage(Request $request, $store_id)
    {
        try {
            //  Get the associated store
            $store = Store::where('id', $store_id)->first();
            $storeImage = $store->primaryImage;

            //  Action was executed successfully
            return oq_api_notify($storeImage, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

}
