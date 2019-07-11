<?php

namespace App\Http\Controllers\Api;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        //  Company Instance
        $data = ( new Company() )->initiateGetStaff();
        $success = $data['success'];
        $response = $data['response'];

        //  If the staff were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of staff
            $staff = $response;

            //  Action was executed successfully
            return oq_api_notify($staff, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

}
