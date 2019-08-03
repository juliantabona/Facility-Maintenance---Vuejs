<?php

namespace App\Traits;

use Storage;
use Validator;
use App\Document;
use Illuminate\Support\Facades\Input;

trait UploadTraits
{

    /*  Save the document to Amazon S3 and update the database with a reference to the file
    *  as well as record the document properties such as [size, mime, type, e.t.c]
    *
    *  @param $request - The current request
    *  @param $model - The model that the document will belong to e.g) User, Company, Jobcard, e.t.c
    *  @param $document - The actual file we want to save to Amazon s3
    *  @param $location - The location path we want to save the file
    *  @param $type - The type helps to identify the document e.g) logo, quotation, e.t.c
    *  @param $user - The user uploading the document
    *
    *  @return $document  for upload success
    *  @return false for upload failed
    */
    function saveDocument($request, $modelId=null, $modelType=null, $document=null, $location=null, $type=null, $replaceable=false, $user = null)
    {
        //  Current authenticated user
        $auth_user = $user ? $user : auth('api')->user();

        //  Query data
        $document = $document ? $document : Input::file('file');
        $modelId = $modelId ? $modelId : request('modelId');                                //  Associated model id
        $modelType = $modelType ? $modelType : request('modelType');                        //  Associated model e.g) user, company, jobcard
        $location = ($location ? $location : request('location')) ?? 'other';               //  Storage location
        $type = ($type ? $type : request('type')) ?? 'other';                               //  Storage type
        $replaceable = request('replaceable') == 'true' ? true : $replaceable;              //  If we can delete a related document e.g) If its a logo

        if(!$request){

            //  request not specified - Log the error
            $response = oq_api_notify_error('POST Request details not provided.', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];

        } elseif(!$document){
            //  document not specified - Log the error
            $response = oq_api_notify_error('Include document to upload.', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } elseif(!$modelType) {
            //  Model type not specified - Log the error
            $response = oq_api_notify_error('Include model type e.g) user, company, e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } elseif (!$modelId) {
            //  Model id not specified - Log the error
            $response = oq_api_notify_error('Include associated model id e.g) 1, 2, 3 e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } else {

            //  Get the rules for validating a document on upload
            $rules = oq_document_create_v_rules();

            //  Customized error messages for validating a document on creation
            $messages = oq_document_create_v_msgs();

            // Now pass the input and rules into the validator
            $validator = Validator::make($request->all(), $rules, $messages);

            // Check to see if validation fails or passes
            if ($validator->fails()) {
                //  Return validation errors
                $response = oq_api_notify_error($validator->errors(), null, 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }

            //  Create the dynamic model
            $dynamicModel = $this->generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Find the associated record by model id
                try {
                    $dynamicModel = $dynamicModel::find($modelId);

                    if($replaceable == true){
                        
                        $relatedDocs = $dynamicModel->documents()->where('type', $type)->get() ?? [];

                        if( count( $relatedDocs ) ){

                            foreach( $relatedDocs as $key => $relatedDoc){

                                $related_doc_path = str_replace( config('app.AWS_URL') , '', $relatedDoc->url);

                                if (!empty($related_doc_path)) {
                                    //  Permanently delete document from S3 Bucket
                                    Storage::disk('s3')->delete($related_doc_path);
                                }
        
                                //  Permanently delete document from the database
                                $relatedDoc->forceDelete();

                            }

                        }
                    }

                    //  Get the document
                    $doc_file = $document;

                    //  Store the document file to Amazon s3 and retrieve the new document name
                    $doc_file_name = Storage::disk('s3')->putFile($location, $doc_file, 'public');

                    //  Construct the URL to the new uploaded file
                    $doc_url = config('app.AWS_URL').$doc_file_name;

                    //  Record the uploaded doc
                    $document = $dynamicModel->documents()->create([
                        'type' => $type,                                                    //  Used to identify from other documents
                        'name' => $doc_file->getClientOriginalName(),                       //  e.g) aircon picture
                        'mime' => oq_get_mime_type($doc_file->getClientOriginalName()),     //  e.g) "mime": "image/jpeg"
                        'size' => $doc_file->getClientSize(),                               //  e.g) 101936
                        'url' => $doc_url,
                        'who_created_id' => !empty($auth_user) ? $auth_user->id : null,
                    ]);

                    //  If the document was uploaded successfully
                    if ($document) {
                        //  Record activity of invoice approved
                        $status = 'uploaded';
                        $documentUploadedActivity = oq_saveActivity($document, $auth_user, $status, ['document' => $document->summarize()]);

                        //  Action was executed successfully
                        return ['success' => true, 'response' => $document];
                    } else {
                        //  Record activity of failed upload
                        $status = 'fail';
                        $documentUploadedActivity = oq_saveActivity(null, $auth_user, $status);

                        //  Return fail response
                        $response = oq_api_notify_error('Something went wrong during the upload process.', null, 404);

                        //  Return the error response
                        return ['success' => false, 'response' => $response];
                    }
                } catch (\Exception $e) {
                    //  Log the error
                    $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                    //  Return the error response
                    return ['success' => false, 'response' => $response];
                }
            } else {
                //  Log the error
                $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }
        }

    }

    function deleteDocument($doc_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        //  Find the associated record by model id
        try {

            $document = Document::find( $doc_id );

            $related_doc_path = str_replace( config('app.AWS_URL') , '', $document->url);

            if (!empty($related_doc_path)) {
                //  Permanently delete document from S3 Bucket
                Storage::disk('s3')->delete($related_doc_path);
            }

            //  Permanently delete document from the database
            $deleted = $document->forceDelete();

            //  If the document was deleted successfully
            if ($deleted) {
                //  Record activity of invoice approved
                $status = 'deleted';
                $documentDeletedActivity = oq_saveActivity($document, $auth_user, $status, ['document' => $document->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $document];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }

    }

    public function generateDynamicModel($modelType)
    {
        //  Create the dynamic model
        $dynamicModel = ('\App\\'.ucfirst($modelType));  //  \App\User

        //  Check if this is a valid dynamic class
        if (class_exists($dynamicModel)) {
            //  Model class does exist

            return $dynamicModel;
        } else {
            return false;
        }
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific
     *  columns that can then be used for storage. We may only want to summarize
     *  the data to very important information, rather than storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want (we access the fillable columns of the model), while also
     *  removing any custom attributes we do not want to store
     *  (we access the appends columns of the model),
     */
    public function summarize()
    {
        //  Collect and select table columns
        return collect($this->fillable)
                //  Remove all custom attributes since the are all based on recent activities
                ->forget($this->appends);
    }

}
