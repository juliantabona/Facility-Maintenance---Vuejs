<?php

namespace App\Http\Controllers\Api;

use DB;
use App\UssdInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdInterfaceController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getUssdInterface( $ussdInterface_id )
    {
        //  Get the ussd interface
        $ussdInterface = UssdInterface::where('id', $ussdInterface_id)->first() ?? null;

        //  Check if the ussd interface exists
        if ($ussdInterface) {

            //  Check if the user is authourized to view the ussd interface
            if ($this->user->can('view', $ussdInterface)) {

                //  Return an API Readable Format of the UssdInterface Instance
                return $ussdInterface->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function updateUssdInterface( Request $request, $ussdInterface_id )
    {

        //  Get the ussd interface
        $ussdInterface = UssdInterface::where('id', $ussdInterface_id)->first() ?? null;

        //  Check if the ussd interface exists
        if ($ussdInterface) {

            //  Check if the user is authourized to update the ussd interface
            if ($this->user->can('update', $ussdInterface)) {

                //  Update the ussd interface
                $updated = $ussdInterface->update( $request->all() );

                //  If the update was successful
                if( $updated ){

                    //  Return an API Readable Format of the UssdInterface Instance
                    return $ussdInterface->fresh()->convertToApiFormat();

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

    public function getUssdInterfaceProducts($ussdInterface_id)
    {
        //  Get the ussd interface
        $ussdInterface = UssdInterface::findOrFail($ussdInterface_id);
        
        //  Get the ussd interface products
        $products = $ussdInterface->products()->paginate() ?? null;

        //  Check if the products exist
        if ($products) {

            //  Check if the user is authourized to view the ussd interface products
            if ($this->user->can('view', $ussdInterface)) {

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

    public function updateUssdInterfaceProducts(Request $request, $ussdInterface_id)
    {
        //  Get the ussd interface
        $ussdInterface = UssdInterface::findOrFail($ussdInterface_id);

        //  Check if the products exist
        if ($ussdInterface) {

            //  Check if the user is authourized to view the ussd interface products
            if ($this->user->can('updateProducts', $ussdInterface)) {

                /*  Add the products to the ussd interface The request data will be an array in
                 *  the form of:
                 * 
                 *  [
                 *      ["id"=>10, "arrangement"=> 1]
                 *      ["id"=>11, "arrangement"=> 2]
                 *      ["id"=>15, "arrangement"=> 3]
                 *      ...
                 *  ] 
                 * 
                 *  The "id" represents the product to add to this store interface. The "arrangement"
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
                    $productsToAdd = $ussdInterface->owner->notVariationProducts()->get();
                    
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
                    $pivotIdsToRemove = collect( $ussdInterface->products()->withPivot('id')->get() )->pluck('pivot.id');

                    //  Add the products to the current ussd interface and save the pivot data
                    $updated = $ussdInterface->products()->saveMany($productData, $productPivotData);

                    if( $updated ){

                        $pivotDataToRemove = DB::table('product_allocations')->whereIn('id', $pivotIdsToRemove)->delete();

                    }

                }

                //  Get the new products
                $products = $ussdInterface->products()->paginate();

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

    public function getUssdInterfaceProduct($ussdInterface_id, $product_id)
    {
        //  Get the store
        $ussdInterface = UssdInterface::findOrFail($ussdInterface_id);

        //  Get the store product
        $product = $ussdInterface->products()->where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the store product
            if ($this->user->can('view', $ussdInterface)) {

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
