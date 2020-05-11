<?php

namespace App\Http\Controllers\Api;

use DB;
use App\MobileStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileStoreController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getMobileStore( $mobileStore_id )
    {
        //  Get the mobile store
        $mobileStore = MobileStore::where('id', $mobileStore_id)->first() ?? null;

        //  Check if the mobile store exists
        if ($mobileStore) {

            //  Check if the user is authourized to view the mobile store
            if ($this->user->can('view', $mobileStore)) {

                //  Return an API Readable Format of the MobileStore Instance
                return $mobileStore->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function updateMobileStore( Request $request, $mobileStore_id )
    {

        //  Get the mobile store
        $mobileStore = MobileStore::where('id', $mobileStore_id)->first() ?? null;

        //  Check if the mobile store exists
        if ($mobileStore) {

            //  Check if the user is authourized to update the mobile store
            if ($this->user->can('update', $mobileStore)) {

                //  Update the mobile store
                $updated = $mobileStore->update( $request->all() );

                //  If the update was successful
                if( $updated ){

                    //  Return an API Readable Format of the MobileStore Instance
                    return $mobileStore->fresh()->convertToApiFormat();

                }

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
     *  PRODUCT RELATED RESOURCES    *
    *********************************/

    public function getMobileStoreProducts($mobileStore_id)
    {
        //  Get the mobile store
        $mobileStore = MobileStore::findOrFail($mobileStore_id);
        
        //  Get the mobile store products
        $products = $mobileStore->products()->paginate() ?? null;

        //  Check if the products exist
        if ($products) {

            //  Check if the user is authourized to view the mobile store products
            if ($this->user->can('view', $mobileStore)) {

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

    public function updateMobileStoreProducts(Request $request, $mobileStore_id)
    {
        //  Get the mobile store
        $mobileStore = MobileStore::findOrFail($mobileStore_id);

        //  Check if the products exist
        if ($mobileStore) {

            //  Check if the user is authourized to view the mobile store products
            if ($this->user->can('updateProducts', $mobileStore)) {

                /*  Add the products to the mobile store The request data will be an array in
                 *  the form of:
                 * 
                 *  [
                 *      ["id"=>10, "arrangement"=> 1]
                 *      ["id"=>11, "arrangement"=> 2]
                 *      ["id"=>15, "arrangement"=> 3]
                 *      ...
                 *  ] 
                 * 
                 *  The "id" represents the product to add to this mobile store. The "arrangement"
                 *  is pivot data that will populate the  pivot table record to indicate the 
                 *  arrangement order of the product
                 * 
                 * 
                 *  We need to change the current structure. This is so that the saveMany() can 
                 *  also save the pivot data properly.
                 * 
                 *   Example of how "save" and "saveMany" work:
                 * 
                 *   $model->relationship()->save(new or existing model, array of pivot data, touch parent = true) (used on existing model)
                 *   $model->relationship()->saveMany(array of new or existing models, array of arrays with pivot data)
                 * 
                 */
                
                //  Get the request data
                $requestData = $request->all();

                //  Get the ids of the products we want to add
                $productIds = collect( $requestData )->pluck('id');

                //  If we have the product ids
                if( count($productIds) ){

                    //  Get the products matching the product ids
                    $productsToAdd = $mobileStore->owner->notVariationProducts()->get();
                    
                }

                //  Array to store each product model
                $productData = [];

                //  Array to store each product model pivot data
                $productPivotData = [];

                //  We use the unique() just so that we make sure that we remove any duplicate products
                //  e.g 2 products with "id" = 1
                 
                foreach (collect($productsToAdd)->unique('id') as $product) {

                    //  Get the matching product data from the request data
                    $relatedProductData = collect($requestData)->where('id', $product->id)->first();

                    //  Get the product id
                    array_push( $productData, $product );

                    //  Get the product pivot data
                    array_push( $productPivotData, ['arrangement' => ($relatedProductData['arrangement'] ?? null)] );

                }

                if( count($productData) ){

                    //  Remove all previous products
                    $pivotIdsToRemove = collect( $mobileStore->products()->withPivot('id')->get() )->pluck('pivot.id');

                    //  Add the products to the current mobile store and save the pivot data
                    $updated = $mobileStore->products()->saveMany($productData, $productPivotData);

                    if( $updated ){

                        $pivotDataToRemove = DB::table('product_allocations')->whereIn('id', $pivotIdsToRemove)->delete();

                    }

                }

                //  Get the new products
                $products = $mobileStore->products()->paginate();

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

    public function getMobileStoreProduct($mobileStore_id, $product_id)
    {
        //  Get the store
        $mobileStore = MobileStore::findOrFail($mobileStore_id);

        //  Get the store product
        $product = $mobileStore->products()->where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the store product
            if ($this->user->can('view', $mobileStore)) {

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

}
