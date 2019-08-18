<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index()
    {
        //  Comment Instance
        $data = ( new Comment() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the comments were found successfully
        if ($success) {
            //  If this is a success then we have the comments
            $comments = $response;

            //  Action was executed successfully
            return oq_api_notify($comments, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($comment_id)
    {
        //  Comment Instance
        $data = ( new Comment() )->initiateShow($comment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the comment was found successfully
        if ($success) {
            //  If this is a success then we have the comment
            $comment = $response;

            //  Action was executed successfully
            return oq_api_notify($comment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the comment
        $data = ( new Comment() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the comment was created successfully
        if ($success) {
            //  If this is a success then we have a comment returned
            $comment = $response;

            //  Action was executed successfully
            return oq_api_notify($comment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($comment_id)
    {
        //  Comment Instance
        $data = ( new Comment() )->initiateUpdate($comment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the comment was updated successfully
        if ($success) {
            //  If this is a success then we have a comment returned
            $comment = $response;

            //  Action was executed successfully
            return oq_api_notify($comment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

}
