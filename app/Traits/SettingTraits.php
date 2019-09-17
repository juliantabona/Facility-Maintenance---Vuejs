<?php

namespace App\Traits;

use App\User;
use App\Company;
use App\Http\Resources\Setting as SettingResource;

trait SettingTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat()
    {

        try {

            //  Transform the settings
            return new SettingResource($this);

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}