<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return auth()->user();
    }

    public function getUserSettings(Request $request)
    {
        return oq_api_notify(auth()->user()->settings, 200);
    }

    public function updateUserSettings(Request $request)
    {
        $resourceType = request('resourceType');

        if (!empty($resourceType)) {
            $updated = auth()->user()->forceFill([
                'settings->resourceType' => $resourceType,
            ])->save();

            if ($updated) {
                $updatedSettings = auth()->user()->fresh()->settings;

                return oq_api_notify($updatedSettings, 200);
            }
        } else {
            return oq_api_notify_error('include resourceType', null, 404);
        }

        return oq_api_notify_error('Update Error', null, 404);
    }
}
