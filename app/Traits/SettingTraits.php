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

    /*  initiateCreate() method:
     *
     *  This method is used to create a new settings.
     *  The $settingsInfo variable represents the  
     *  settings dataset provided
     */
    public function initiateCreate( $settingsInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the
         *  settings data required to create a new resource.
         */
        $template = [
            'details' => $settingsInfo['details'] ?? null
        ];

        try {

            /*
             *  Create new settings, then retrieve a fresh instance
             */
            $this->settings = $this->create($template);

            /*  If the settings were created successfully  */
            if( $this->settings ){   

                /*  Return a fresh instance of the settings  */
                return $this->settings->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }
}