<?php

namespace App\Http\Controllers\Api;

use App\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function upload(Request $request){
   
        //  Document Instance
        $data = ( new Document() )->saveDocument($request);
        $success = $data['success'];
        $response = $data['response'];

        //  If the upload was successful
        if ($success) {
            //  If this is a success then we have the uploaded document
            $upload = $response;

            //  Action was executed successfully
            return oq_api_notify($upload, 200);
        }

        //  If the data was not a success then return the response
        return $response;
   
    }
}
