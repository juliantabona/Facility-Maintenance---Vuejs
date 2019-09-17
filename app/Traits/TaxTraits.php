<?php

namespace App\Traits;

use App\Http\Resources\Tax as TaxResource;
use App\Http\Resources\Taxes as TaxesResource;

trait TaxTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($taxes = null)
    {

        try {

            if( $taxes ){

                //  Transform the taxes
                return new TaxesResource($taxes);

            }else{

                //  Transform the tax
                return new TaxResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}
