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

    public function delete(Request $request, $doc_id){
   
        //  Document Instance
        $data = ( new Document() )->deleteDocument($doc_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the delete was successful
        if ($success) {
            //  Action was executed successfully
            return oq_api_notify(null, 200);
        }

        //  If the data was not a success then return the response
        return $response;
   
    }
}
