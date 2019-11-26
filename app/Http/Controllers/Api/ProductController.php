<?php

namespace App\Http\Controllers\Api;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getProducts()
    {
        //  Check if the user is authourized to view all products
        if ($this->user->can('viewAll', Product::class)) {
        
            //  Get the products
            $products = Product::paginate();

            //  Check if the products exist
            if ($products) {

                //  Return an API Readable Format of the Product Instance
                return ( new \App\Product )->convertToApiFormat($products);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getProduct( $product_id )
    {
        //  Get the product
        $product = Product::where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the product
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Product Instance
                return $product->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }
    
    public function createProduct( Request $request )
    {
        //  Check if the user is authourized to create a product
        if ($this->user->can('create', Product::class)) {

            //  Create the product
            $product = ( new \App\Product )->initiateCreate( $productInfo = $request->all() );
            
            //  Return an API Readable Format of the Product Instance
            return $product->convertToApiFormat();

        } else {

            //  Not Authourized
            return oq_api_not_authorized();
        }
    }

    public function updateProduct( Request $request, $product_id )
    {
        //  Get the product
        $product = Product::where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to update the product
            if ($this->user->can('update', $product)) {

                //  Update the product
                $updatedProduct = $product->initiateUpdate($productInfo = $request->all());

                //  Return an API Readable Format of the Product Instance
                return $updatedProduct->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /**************************************
     *  VARIABLE RELATED RESOURCES        *
    **************************************/

    public function getProductVariables( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product variables
        $variables = $product->variables()->paginate() ?? null;

        //  Check if the variables exist
        if ($variables) {

            //  Check if the user is authourized to view the product variables
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Variable Instance
                return ( new \App\Variable )->convertToApiFormat($variables);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductVariable( $product_id, $variable_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product variable
        $variable = $product->variables()->where('variables.id', $variable_id)->first() ?? null;

        //  Check if the variable exists
        if ($variable) {

            //  Check if the user is authourized to view the product variable
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Variable Instance
                return $variable->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }
    
    public function updateProductVariables( Request $request, $product_id )
    {
        //  Get the product
        $product = Product::where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to update the product variables
            if ($this->user->can('update', $product)) {

                //  Update the product
                $updatedProduct = $product->initiateUpdateVariables($variableInfo = $request->all());

                //  Return an API Readable Format of the Product Instance
                return $updatedProduct->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function deleteProduct( Request $request, $product_id )
    {
        //  Get the product
        $product = Product::where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to delete the product
            if ($this->user->can('delete', $product)) {

                //  Delete the product
                $deletedProduct = $product->initiatedelete();
                
                //  Return status 200
                return oq_api_notify(null, 200);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  OWNER RELATED RESOURCES      *
    *********************************/

    public function getProductOwner( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product owner
        $owner = $product->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the product owner
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the User Instance
                return $owner->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DOCUMENT RELATED RESOURCES   *
    *********************************/

    public function getProductPicture( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product primary image
        $primary_image = $product->primary_image ?? null;

        //  Check if the primary_image exists
        if ($primary_image) {

            //  Check if the user is authourized to view the product primary image
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Document Instance
                return $primary_image->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductGallery( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product gallery images
        $gallery_images = $product->gallery()->paginate() ?? null;

        //  Check if the gallery images exist
        if ($gallery_images) {

            //  Check if the user is authourized to view the gallery images
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document )->convertToApiFormat($gallery_images);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductDownloads( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product downloads
        $downloads = $product->downloads()->paginate() ?? null;

        //  Check if the downloads exist
        if ($downloads) {

            //  Check if the user is authourized to view the downloads
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document )->convertToApiFormat($downloads);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductDocuments( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product documents
        $documents = $product->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the product documents
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document )->convertToApiFormat($documents);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductDocument( $product_id, $document_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product document
        $document = $product->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the product document
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Document Instance
                return $document->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }


    
    public function getProductVariations( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product variations
        $variations = $product->variations()->paginate() ?? null;

        //  Check if the variations exist
        if ($variations) {

            //  Check if the user is authourized to view the product variations
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Product Instance
                return ( new \App\Product )->convertToApiFormat($variations);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }  

    public function createProductVariations(Request $request, $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Check if the user is authourized to update the product variations
        if ($this->user->can('update', $product)) {

            //  Get the request data
            $requestData = $request->all();

            //  Generate the  product variations
            $product->initiateCreateVariations( $variantAttributeInfo = $requestData );

            //  Get the product variations
            $variations = $product->variations()->paginate() ?? null;

            //  Return an API Readable Format of the Product Instance
            return ( new \App\Product )->convertToApiFormat($variations);

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    /*********************************
     *  ORDER RELATED RESOURCES      *
    *********************************/
/*
    public function getProductOrders( $product_id )
    {
        {
            //  Get the product
            $product = Product::findOrFail($product_id);
    
            //  Get the product orders
            $orders = $product->orders()->paginate() ?? null;
    
            //  Check if the orders exist
            if ($orders) {
    
                //  Check if the user is authourized to view the product orders
                if ($this->user->can('view', $product)) {
    
                    //  Return an API Readable Format of the Order Instance
                    return ( new \App\Order )->convertToApiFormat($orders);
    
                } else {
    
                    //  Not Authourized
                    return oq_api_not_authorized();
    
                }
            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();
    
            }
        }
    }

    public function getProductOrder( $product_id, $order_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product order
        $order = $product->orders()->where('orders.id', $order_id)->first() ?? null;

        //  Check if the order exists
        if ($order) {

            //  Check if the user is authourized to view the product order
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Order Instance
                return $order->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }
*/
    /*********************************
     *  TAX RELATED RESOURCES        *
    *********************************/

    public function getProductTaxes( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product taxes
        $taxes = $product->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the product taxes
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Tax Instance
                return ( new \App\Tax )->convertToApiFormat($taxes);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductTax( $product_id, $tax_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product tax
        $tax = $product->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the product tax
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Tax Instance
                return $tax->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DISCOUNT RELATED RESOURCES   *
    *********************************/

    public function getProductDiscounts( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the store discounts
        $discounts = $product->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the product discounts
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Discount Instance
                return ( new \App\Discount )->convertToApiFormat($discounts);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductDiscount( $product_id, $discount_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product discount
        $discount = $product->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the product discount
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Discount Instance
                return $discount->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  COUPONS RELATED RESOURCES    *
    *********************************/

    public function getProductCoupons( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product coupons
        $coupons = $product->coupon()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the product coupons
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Coupon Instance
                return ( new \App\Coupon )->convertToApiFormat($coupons);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductCoupon( $product_id, $coupon_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product coupons
        $coupons = $product->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the product coupons
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Coupon Instance
                return $coupons->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  REVIEW RELATED RESOURCES     *
    *********************************/

    public function getProductReviews( $product_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product reviews
        $reviews = $product->reviews()->paginate() ?? null;

        //  Check if the reviews exist
        if ($reviews) {

            //  Check if the user is authourized to view the product reviews
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Review Instance
                return ( new \App\Review )->convertToApiFormat($reviews);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getProductReview( $product_id, $review_id )
    {
        //  Get the product
        $product = Product::findOrFail($product_id);

        //  Get the product review
        $review = $product->reviews()->where('reviews.id', $review_id)->first() ?? null;

        //  Check if the review exists
        if ($review) {

            //  Check if the user is authourized to view the product review
            if ($this->user->can('view', $product)) {

                //  Return an API Readable Format of the Review Instance
                return $review->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*
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
    */
}
