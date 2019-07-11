<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\CompanyBranch;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        //  Product Instance
        $data = ( new Product() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the products were found successfully
        if ($success) {
            //  If this is a success then we have the products
            $products = $response;

            //  Action was executed successfully
            return oq_api_notify($products, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($product_id)
    {
        //  Product Instance
        $data = ( new Product() )->initiateShow($product_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the product was found successfully
        if ($success) {
            //  If this is a success then we have the product
            $product = $response;

            //  Action was executed successfully
            return oq_api_notify($product, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the product
        $data = ( new Product() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the product was created successfully
        if ($success) {
            //  If this is a success then we have a product returned
            $product = $response;

            //  Action was executed successfully
            return oq_api_notify($product, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($product_id)
    {
        //  Product Instance
        $data = ( new Product() )->initiateUpdate($product_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the product was updated successfully
        if ($success) {
            //  If this is a success then we have a product returned
            $product = $response;

            //  Action was executed successfully
            return oq_api_notify($product, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getImage(Request $request, $product_id)
    {
        try {
            //  Get the associated product
            $product = Product::where('id', $product_id)->first();
            $productImage = $product->primaryImage;

            //  Action was executed successfully
            return oq_api_notify($productImage, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

}
