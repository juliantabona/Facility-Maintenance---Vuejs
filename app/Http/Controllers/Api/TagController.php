<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tag;

class TagController extends Controller
{
    public function index()
    {
        //  Tag Instance
        $data = ( new Tag() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the tags were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of tags
            $tags = $response;

            //  Action was executed successfully
            return oq_api_notify($tags, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store()
    {
        //  Tag Instance
        $data = ( new Tag() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the tag was created successfully
        if ($success) {
            //  If this is a success then we have the tag
            $tag = $response;

            //  Action was executed successfully
            return oq_api_notify($tag, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

}
