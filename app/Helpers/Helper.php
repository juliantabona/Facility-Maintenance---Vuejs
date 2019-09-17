<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

function oq_generateDynamicModel($modelType)
{
    //  Create the dynamic model
    $dynamicModel = ('\App\\'.ucfirst( Str::camel($modelType) ));  //  \App\User

    //  Check if this is a valid dynamic class
    if (class_exists($dynamicModel)) {
        //  Model class does exist

        return $dynamicModel;
    } else {
        return false;
    }
}

/**************************************************************
***************************************************************
***************************************************************
    COMPANY CONTROLLER HELPER FUNCTIONS
***************************************************************
***************************************************************
**************************************************************/

/*  Checks if the client already exists in the company directory
 *  by matching existing ID or Name. If exists, return true,
 *  otherwise false.
 *
 * @param $user - The user we need to get the company directory
 * @param $company_name_or_id - The company ID thats being checked for existence
 *
 * @return true  for company exists!
 * @return false for company does not exist
 */
function oq_checkClientExists($user, $company_name_or_id)
{
    $result = $user->companyBranch->company->clients()
                    ->where('companies.id', $company_name_or_id)
                    ->orWhere('name', $company_name_or_id)->first();

    return (!empty($result)) ? true : false;
}

/*  Checks if the supplier already exists in the company directory
 *  by matching existing ID or Name. If exists, return true,
 *  otherwise false.
 *
 * @param $user - The user we need to get the company directory
 * @param $company_name_or_id - The company ID thats being checked for existence
 *
 * @return true  for company exists!
 * @return false for company does not exist
 */
function oq_checkSupplierExists($user, $company_name_or_id)
{
    $result = $user->companyBranch->company->suppliers()
                    ->where('companies.id', $company_name_or_id)
                    ->orWhere('name', $company_name_or_id)->first();

    return (!empty($result)) ? true : false;
}

/*  Returns back with the old inputs and custom errors
 *  Error Format ['field_name', 'Associated Error Message']
 *  e.g) ['company_name' => 'A company with that name already exists.']
 *
 * @param $errors - The custom error
 *
 * @return Redirect
 */
function oq_backWithCustomErrors($errors)
{
    // Return error with old input fields
    return Redirect::back()->withInput()->withErrors($errors);
}

/*  Creates a flash session for alerting the user
 *
 * @param $msg - The custom messsage
 * @param $type - The type of alert
 *
 * @return void
 */
function oq_notify($msg, $type = 'default')
{
    if ($type == 'success') {
        $icon = 'icon-check icons';
    } elseif ($type == 'danger') {
        $icon = 'icon-exclamation icons';
    } elseif ($type == 'warning') {
        $icon = 'icon-exclamation icons';
    } else {
        $icon = '';
    }

    Session::forget('alert');
    Session::flash('alert', array($msg, $icon, $type));
}

/*  Return a response back as an API response
 *  Useful when we want to return data to the
 *  user when they make an API call
 *  @param $request - The API request sent
 *  @param $msg - The message/data we want to return
 *  @param $status - The status of the response (e.g 200, 201, 404)
 *
 * @return json
 */
function oq_api_notify($data, $status)
{
    return response()->json($data, $status);
}

function oq_api_notify_error($msg = '', $error = [], $status = 404)
{
    return oq_api_notify([
        //  Return the usual laravel error format
        'message' => $msg,
        'errors' => $error,
    ], $status);
}

/*  Check if the request is an API request
 *  @param $request - The API request sent
 *
 * @return boolean
 */
function oq_viaAPI($request)
{
    if ($request->expectsJson()) {
        return true;
    } else {
        return false;
    }
}

function oq_api_notify_no_resource()
{
    return oq_api_notify_error('Record not found.', null, 404);
}

function oq_api_notify_no_page()
{
    return oq_api_notify_error('Route not found. Make sure you are using the correct url', null, 404);
}

function oq_api_not_authorized()
{
    return oq_api_notify_error('Not authorized', null, 403);
}

function oq_api_notify_to_many_attempts()
{
    return oq_api_notify_error('Too many attempts. Please wait some time and try again', null, 404);
}

function oq_url_to_array($url, $dimension = 0)
{
    return explode(',', $url);
}

/**
 * mb_stripos all occurences
 * based on http://www.php.net/manual/en/function.strpos.php#87061.
 *
 * Find all occurrences of a needle in a haystack (case-insensitive, UTF8)
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return array or false
 */
function oq_stripos_all($haystack, $needle)
{
    $s = 0;
    $i = 0;

    while (is_integer($i)) {
        $i = mb_stripos($haystack, $needle, $s);

        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i + mb_strlen($needle);
        }
    }

    if (isset($aStrPos)) {
        return $aStrPos;
    } else {
        return false;
    }
}

/*  Creates a recent activity for the associated model
 *  $model is the associated model that can generate recentActivity() such as User, Company, e.t.c.
 *  $type is the type of activity such as 'creating', 'updating', 'deleting', 'uploading', e.t.c.
 *  $user is the reference to the actual user creating the activity
 *
 *  @param $model - Is the associated model that can generate recentActivity() such as User, Company, e.t.c.
 *  @param $type - Is the type of activity such as 'creating', 'updating', 'deleting', 'uploading', e.t.c.
 *  @param $user - Is the reference to the actual user creating the activity
 *
 *  @return true  for update success
 *  @return false for update failed
 */
//function oq_saveActivity($model, $type, $user, $customDetails = null)
function oq_saveActivity($model=null, $user, $status, $customDetails = false)
{
    if ($model != null) {
        $model_1 = is_array($model) ? $model[0] : $model;
        $model_2 = is_array($model) ? $model[1] : $model;

        //  If $customDetails = true, then use the same model for starage
        if ($customDetails === false) {
            $details = [
                strtolower(snake_case(class_basename($model_2))) => $model_2,    //  'document' => [Document Object]
            ];
        //  Otherwise if its a different value, then use that value instead
        } elseif ($customDetails != null) {
            $details = $customDetails;
        //  Otherwise default to nothing
        } else {
            $details = null;
        }

        //return auth('api')->user()->recentActivities()->all();
        //return $user->recentActivities;

        $update = $model_1->recentActivities()->create([
            'type' => $status,
            'activity' => $details,
            'created_by' => !empty($user) ? $user->id : null,
            'company_branch_id' => !empty($user) ? $user->company_branch_id : null,
            'company_id' => !empty($user) ? $user->company_id : null,
        ]);
    } else {
        $update = \App\RecentActivity::create([
            'type' => $status,
            'activity' => '',
            'created_by' => !empty($user) ? $user->id : null,
            'company_branch_id' => !empty($user) ? $user->company_branch_id : null,
            'company_id' => !empty($user) ? $user->company_id : null,
        ]);
    }

    if ($update) {
        return true;
    }

    return false;
}

/*  Save the document to Amazon S3 and update the database with a reference to the file
 *  as well as record the document properties such as [size, mime, type, e.t.c]
 *
 *  @param $model - Is the model that the document will belong to e.g) User, Jobcard, e.t.c
 *  @param $document - The actual file we want to save to Amazon s3
 *  @param $location - The location path we want to save the file
 *  @param $type - The type helps to identify the document e.g) logo, quotation, e.t.c
 *  @param $user - The user uploading the document
 *
 *  @return $document  for upload success
 *  @return false for upload failed
 */
function oq_saveDocument($request, $model, $document, $location, $type, $user = null)
{
    //  Get the rules for validating a document on upload
    $rules = oq_document_create_v_rules();

    //  Customized error messages for validating a document on creation
    $messages = oq_document_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($request->all(), $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t upload image/document!', 'danger');
        //  Return validation errors with an alert or json response if API request
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Get the document
    $doc_file = $document;

    //  Store the document file to Amazon s3 and retrieve the new document name
    $doc_file_name = Storage::disk('s3')->putFile($location, $doc_file, 'public');

    if (!empty($doc_file_name)) {
        //  Construct the URL to the new uploaded file
        $doc_url = env('AWS_URL').$doc_file_name;

        //  Record the uploaded doc
        $document = $model->documents()->create([
            'type' => $type,                                                    //  Used to identify from other documents
            'name' => $doc_file->getClientOriginalName(),                       //  e.g) aircon picture
            'mime' => oq_get_mime_type($doc_file->getClientOriginalName()),     //  e.g) "mime": "image/jpeg"
            'size' => $doc_file->getClientSize(),                               //  e.g) 101936
            'url' => $doc_url,
            'who_created_id' => !empty($user) ? $user->id : null,
        ]);

        //  If the document was uploaded successfully
        if ($document) {
            //  Record activity of success document upload
            $documentUploadedActivity = oq_saveActivity($document, $type.' uploaded', $user);
            //  Return the document instance
            return $document;
        } else {
            //  Record activity of failed document upload
            $documentUploadedActivity = oq_saveActivity(null, $type.' failed', $user);
        }
    }

    return false;
}

function oq_updateDocument($request, $currDocument, $user = null)
{
    //  Get the rules for validating a document on upload
    $rules = oq_document_create_v_rules();

    //  Customized error messages for validating a document on creation
    $messages = oq_document_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($request->all(), $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t upload image/document!', 'danger');
        //  Return validation errors with an alert or json response if API request
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    foreach ($request->all() as $key => $value) {
        $request[str_replace('document_', '', $key)] = $value;
        unset($request[$key]);
    }

    //  Update the existing document
    $document = $currDocument->update($request->all());
    $status = 'updated';

    //  If the document was updated successfully
    if ($document) {
        $document = $currDocument->fresh();
        //  Record activity of success document updated
        $documentUpdatedActivity = oq_saveActivity($document, $status, $user);
        //  Return the document instance
        return $document;
    } else {
        //  Record activity of failed document update
        $failType = 'update';
        $documentUpdatedActivity = oq_saveActivity(null, $failType, $user);
    }

    return false;
}

function oq_get_mime_type($filename)
{
    $idx = explode('.', $filename);
    $count_explode = count($idx);
    $idx = strtolower($idx[$count_explode - 1]);

    $mimet = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    if (isset($mimet[$idx])) {
        return $mimet[$idx];
    } else {
        return 'application/octet-stream';
    }
}

/**
 *  Gets the file from the request.
 *
 * @param $file - Request Input of the file e.g) $request->only('company_logo')
 *
 * @return file for complete success, everything worked!
 */
function oq_getFile($file)
{
    //  return the file if it exists, otherwise return nothing
    return !empty($file) ? $file[array_keys($file)[0]] : [];
}

/**
 *  Create a completely new company.
 *
 * @param $request - The request with parameters for creating company
 * @param $company - The company we want to add to the directory
 * @param $user - The user running this activity
 *
 * @return $company for complete success, everything worked!
 * @return false    for complete/partial fail during execution
 */
function oq_createOrUpdateCompany($request, $currCompany, $user)
{
    //  Lets get the image file if it exists
    if (!empty($request->only('company_logo'))) {
        //  Grab image file
        $imageFile = oq_getFile($request->only('company_logo'));
        //  Add all uploads for validation
        $fileArray = array_merge(array('company_logo' => $imageFile), $request->all());
    } else {
        $fileArray = $request->all();
    }

    //  Get the rules for validating a company on creation
    $rules = oq_company_create_v_rules();

    //  Customized error messages for validating a company on creation
    $messages = oq_company_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($fileArray, $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t create '.$request->input('company_type').', check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Lets get all instanes of the branch inputs and rename approprietly
    $tempRequest = oq_replaceRequestInputNames($request, 'company_');

    try {
        if ($currCompany == null) {
            //  Create the company
            $company = \App\Company::create($tempRequest->all());
            $status = 'created';
        } else {
            //  Update the existing company
            $company = $currCompany->update($tempRequest->all());
            $status = 'updated';
        }
    } catch (\Exception $e) {
        //  Return back with query errors
        return  ['query_error' => $e->getMessage()];
    }

    //  If the company was created successfully
    if ($company) {
        //  re-retrieve the instance to get all of the fields in the table.
        if ($currCompany == null) {
            $company = \App\Company::where('id', $company->id)->with('logo')->first();
        } else {
            $company = \App\Company::where('id', $currCompany->id)->with('logo')->first();
        }

        //  Record activity of a new company created
        $companyCreatedActivity = oq_saveActivity($company, $status, $user);

        //  Create the branch
        $branch = oq_createOrUpdatebranch($request, null, $company, $user);

        //  If we have the logo and has been approved, then save it to Amazon S3 bucket
        if ($request->hasFile('company_logo')) {
            $document = oq_saveDocument($request, $company, Input::file('company_logo'), 'company_logos', 'logo', $user);
        }

        return $company;
    }

    return false;
}

/**
 *  Add the company to company directory.
 *
 * @param $owningCompany_id - The ID of the owning company
 * @param $company - The company we want to add to the directory
 * @param $user - The user we will use to extract the owning company's directory to add the listing company
 * @param $type - The type of company being added e.g) client, supplier, e.t.c
 *
 * @return true  for complete success, everything worked!
 * @return false for complete/partial fail during execution
 */
function oq_addCompanyToDirectory($company, $type, $user)
{
    $owningBranch_id = $user->companyBranch->id;
    $owningCompany_id = $user->company_id;

    //  Let us add the company to the owning company as type of client/supplier
    $addedToDirectory = $company->companyDirectory()->attach($owningCompany_id, ['type' => $type, 'owning_branch_id' => $owningBranch_id]);

    if ($addedToDirectory) {
        return true;
    }

    return false;
}

/**
 *  Add the company to the a jobcard as a client for that jobcard.
 *
 * @param $jobcard_id - The jobcard id
 * @param $company - The company we want to add
 * @param $user - The user running this activity
 *
 * @return true  for complete success, everything worked!
 * @return false for complete/partial fail during execution
 */
function oq_addClientToJobcard($jobcard_id, $company, $user)
{
    //  Get the associated jobca
    $jobcard = \App\Jobcard::find($jobcard_id);

    if ($jobcard) {
        //  Save the company to the jobcard as the current client
        $jobcard->client_id = $company->id;
        $jobcard->save();

        //  Record activity of success client added
        $jobcardActivity = oq_saveActivity([$jobcard, $company], 'client added', $user);

        //  If the activity was saved successfully
        if ($jobcardActivity) {
            return true;
        }
    }

    return false;
}

/**
 *  Add the company to the a jobcard as a potential supplier for that jobcard.
 *  Also save the quotation file and amount for the jobcard.
 *
 * @param $jobcard_id - The jobcard id
 * @param $company - The company we want to add
 * @param $user - The user running this activity
 * @param $request - The request with all the details about the supplier, quotation and amount
 * @param $type - a string to indicate whether to create or update
 *
 * @return true  for complete success, everything worked!
 * @return false for complete/partial fail during execution
 */
function oq_addOrUpdateSupplierToJobcard($jobcard_id, $company, $user, $request, $type)
{
    //  Lets get the file if it exists
    if (!empty($request->only('company_quote'))) {
        //  Grab file
        $imageFile = oq_getFile($request->only('company_quote'));
        //  Add all uploads for validation
        $fileArray = array_merge(array('company_quote' => $imageFile), $request->all());
    } else {
        $fileArray = $request->all();
    }

    //  Get the rules for validating a document on creation
    $rules = oq_document_create_v_rules();

    //  Customized error messages for validating a document on creation
    $messages = oq_document_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($fileArray, $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t create '.$request->input('company_relation').', check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Get the associated jobcard
    $jobcard = \App\Jobcard::find($jobcard_id);

    if ($jobcard) {
        //  If we have the quotation and has been approved, then save it to Amazon S3 bucket
        if ($request->hasFile('company_quote')) {
            $document = oq_saveDocument($request, $company, Input::file('company_quote'), 'company_quotes', 'quotation', $user);
        } else {
            $document = null;
        }

        $doc_id = !empty($document) ? $document->id : null;

        //  Remove all characters except numbers and the period (.) to save proper money
        $amount = preg_replace('/[^0-9.]/', '', $request->input('company_total_price'));

        if ($type == 'create') {
            try {
                //  Add the company as part of the jobcards list of potential suppliers
                $addedToSuppliers = $jobcard->suppliersList()->attach([$company->id => [
                    'amount' => $amount,
                    'quotation_doc_id' => $doc_id,
                ]]);
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            try {
                //  Add the company as part of the jobcards list of potential suppliers
                $addedToSuppliers = $jobcard->suppliersList()->updateExistingPivot($company->id, [
                    'amount' => $amount,
                    'quotation_doc_id' => $doc_id,
                ]);
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        }

        if ($addedToSuppliers) {
            //  Record activity of success client client/supplier added
            $jobcardActivity = oq_saveActivity([$jobcard, $company], 'supplier added', $user);
        }

        return true;
    }

    return false;
}

/**
 *  Check if the validation has passed or failed.
 *
 * @param array $data - An array of the validation response
 *                    - ['failed_validation' => Redirect::back()->withErrors($validator)->withInput()];
 *
 * @return true  for failed validation
 * @return false for passed validation
 */
function oq_failed_validation($data)
{
    //  If the data has a failed_validation key then the validation did not pass
    if (is_array($data) && $data['failed_validation'] == true) {
        return true;
    } else {
        return false;
    }
}

function oq_failed_validation_return($request, $response, $currentData = null)
{
    $validator = $response['validator'];

    if (!empty($validator)) {
        if (oq_viaAPI($request)) {
            return oq_api_notify([
                //  Return the usual laravel error format
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
                'data' => $currentData,
            ], 400);
        } else {
            //  Return back with the alert and failed validation rules
            oq_notify('Failed validation.', 'warning');

            return Redirect::back()->withErrors($validator)->withInput();
        }
    }
}

/**
 *  Check if the SQL execution has passed or failed.
 *
 * @param array $data - An array of the SQL response
 *                    - ['query_error' => Sql Query Error Message];
 */
function oq_failed_sql($data)
{
    //  If the data has a query_error key then the sql did not pass
    if (is_array($data) && !empty($data['query_error'])) {
        return true;
    } else {
        return false;
    }
}

function oq_failed_sql_return($request, $data)
{
    if (is_array($data)) {
        $sql_error = $data['query_error'];
        if (oq_viaAPI($request)) {
            return oq_api_notify([
                //  Return the sql error
                'message' => 'Sql error.',
                'errors' => $data['query_error'],
            ], 400);
        } else {
            //  Return back with the alert and failed sql error
            oq_notify('Something went wrong creating the company. Please try again', 'warning');

            return oq_backWithCustomErrors(['sql_error' => $data['query_error']]);
        }
    }
}

/**************************************************************
***************************************************************
***************************************************************
    JOBCARD CONTROLLER HELPER FUNCTIONS
***************************************************************
***************************************************************
**************************************************************/
/**
 *  Get the pagination of the company contacts.
 *
 * @param $company - The company we want th contacts from
 *
 * @return $contacts for complete success, everything worked!
 */
function oq_paginateClientContacts($company)
{
    if (!empty($company)) {
        $contacts = $company->contactDirectory()->paginate(5, ['*'], 'contacts');
    } else {
        $contacts = null;
    }

    return $contacts;
}

/**
 *  Calculate how many days until deadline and get the deadline in days
 *  Get the deadline time in seconds then minus current time in seconds and
 *  Divide by 24hrs for days left till deadline.
 *
 * @param $model - The model we want the deadlines from
 *
 * @return true  for complete success, everything worked!
 * @return false for complete/partial fail during execution
 */
/*  Calculate how many days until deadline
*   Get the deadline time in seconds then
*   Minus current time in seconds and
*   Divide by 24hrs for days left till deadline
*/
function oq_jobcardDeadlineArray($model)
{
    if ($model->end_date != null) {
        $deadline = \Carbon\Carbon::parse($model->end_date);
        $now = \Carbon\Carbon::parse(\Carbon\Carbon::now());
        $diff = $deadline->diff($now);

        /*  The invert helps us know if the difference is positive or negative
        *  If the invert is == 1 then it is positive, meaning deadline not reached.
        *  If the invert is == 0 then it is negative, meaning deadline passed.
        */
        $invert = $diff->invert;

        if ($diff->y != 0) {
            return $diff->y == 1 ? [$diff->y, 'year', $invert] : [$diff->y, 'years', $invert];
        } elseif ($diff->m != 0) {
            return $diff->m == 1 ? [$diff->m, 'month', $invert] : [$diff->m, 'months', $invert];
        } elseif ($diff->d != 0) {
            return $diff->d == 1 ? [$diff->d, 'day', $invert] : [$diff->d, 'days', $invert];
        } elseif ($diff->h != 0) {
            return $diff->h == 1 ? [$diff->h, 'hour', $invert] : [$diff->h, 'hours', $invert];
        } elseif ($diff->i != 0) {
            return $diff->i == 1 ? [$diff->i, 'minute', $invert] : [$diff->i, 'minutes', $invert];
        } elseif ($diff->s != 0) {
            return $diff->s == 1 ? [$diff->s, 'second', $invert] : [$diff->s, 'seconds', $invert];
        }
    }

    return null;
}

function oq_jobcardDeadlineWords($deadline)
{
    /*  $deadline[0] = number e.g) 7
     *  $deadline[1] = type e.g) minutes
     *  $deadline[2] = invert e.g) 0 or 1
     * The invert helps us know if the difference is positive or negative
     *  If the invert is == 1 then it is positive, meaning deadline not reached.
     *  If the invert is == 0 then it is negative, meaning deadline passed.
     */
    if ($deadline != null) {
        if ($deadline[2] == 1) {
            return $deadline[0].' '.$deadline[1].' until deadline';
        } elseif ($deadline[2] == 0) {
            return 'Deadline passed '.$deadline[0].' '.$deadline[1].' ago';
        }
    }

    return null;
}

/**
 *  Add a new view for a given model.
 *
 * @param $model - The entity being viewed e.g) jobcard, user, e.t.c
 * @param $user - The user being recorded as a viewer
 *
 * @return $activity for complete success, everything worked!
 * @return false     for complete/partial fail during execution
 */
function oq_addView($model, $user)
{
    // re-retrieve the instance to get all of the fields in the table.
    $model = $model->fresh();

    $recentViews = $model->recentActivities->filter(function ($activity) use ($user) {
        //  If this is a viewing activity
        if ($activity->type == 'viewing') {
            //  And the view belongs to the current authenticated user
            if ($activity->detail['viewer'] == $user->id) {
                return $activity;
            }
        }
    });

    if ($recentViews->count()) {
        collect([$recentViews->first()])->map(function ($activity) use ($model, $user) {
            /*  Calculate how long ago they viewed from now
            *  Get the current time in seconds and
            *  Divide by 60 to give number of minutes since last view
            */
            $last_viewed = (strtotime(\Carbon\Carbon::now()->toDateTimeString()
                                    ) - strtotime($activity->created_at))
                                    / 60;

            // If the user last viewed this atleast 1minute ago then record their new view
            if ($last_viewed > 1) {
                //  Record activity of a new view
                return $viewedActivity = oq_saveActivity($model, 'viewing', $user, ['viewer' => $user->id]);
            }
        });
    } else {
        //  Record activity of a new view
        return $viewedActivity = oq_saveActivity($model, 'viewing', $user, ['viewer' => $user->id]);
    }

    return false;
}

function oq_getArray($data)
{
    //  If we json_decode the $data and return a valid array then we can proceed
    if (is_array(json_decode($data, true)[0])) {
        return json_decode($data, true)[0];
    }
    //  Otherwise return the original $data unchanged
    return $data;
}

function oq_createOrUpdateJobcard($request, $currJobcard = null, $user)
{
    /*  Lets get the priority, cost centers, categories and branches
     *  for this new jobcard. At this point we don't know if the user
     *  is using stored entries from the database or creating new
     *  values. So for now we will store these values in the
     *  following variables for later checking
     */
    $priorityArray = oq_getArray($request->input('priority'));
    $cost_centerArray = oq_getArray($request->input('cost_center'));
    $categoryArray = oq_getArray($request->input('category'));
    $branchArray = oq_getArray($request->input('branch'));

    /*  Lets check if the priority, cost centers, categories and branches
     *  values are custom values.This means they were created using a modal.
     *  values created using a modal contain "_&_" as a separator.
     *  Since strpos may return TRUE (substring present), then we
     *  can check and then extract those values.
     */

    if (is_array($priorityArray)) {
        //  Extract the new priority name and replace the current request value
        $request->merge(['priority_name' => $priorityArray['name']]);
    }

    if (is_array($cost_centerArray)) {
        //  Extract the cost center name and replace the current request value
        $request->merge(['cost_center_name' => $cost_centerArray['name']]);
    }

    if (is_array($categoryArray)) {
        //  Extract the new category name and replace the current request value
        $request->merge(['category_name' => $categoryArray['name']]);
    }

    if (is_array($branchArray)) {
        //  Extract the new company branch name and replace the current request value
        $request->merge(['branch_name' => $branchArray['name']]);
    }

    //  Lets get the image file if it exists
    if (!empty($request->only('new_jobcard_image'))) {
        //  Grab image file
        $imageFile = oq_getFile($request->only('new_jobcard_image'));
        //  Add all uploads for validation
        $fileArray = array_merge(array('new_jobcard_image' => $imageFile), $request->all());
    } else {
        $fileArray = $request->all();
    }

    //  Get the rules for validating a jobcard on creation
    $rules = oq_jobcard_create_v_rules($user);

    //  Customized error messages for validating a jobcard on creation
    $messages = oq_jobcard_create_v_msgs($request);

    // Now pass the input and rules into the validator
    $validator = Validator::make($fileArray, $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t update jobcard, check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    if (is_array($priorityArray)) {
        $request->merge(['priority_name' => $priorityArray['name']]);
        //  Save the new priority and assign it back to the request input value
        $priorityCreated = oq_createOrUpdatePriority($request, null, $user->companyBranch->company, $user);
        //  Update request parameter with the priority id
        $request->merge(['priority_id' => $priorityCreated->id]);
    }

    if (is_array($cost_centerArray)) {
        $request->merge(['cost_center_name' => $cost_centerArray['name']]);
        //  Save the new cost center and assign it back to the request input value
        $costcenterCreated = oq_createOrUpdateCostCenter($request, null, $user->companyBranch->company, $user);
        //  Update request parameter with the cost center id
        $request->merge(['cost_center_id' => $costcenterCreated->id]);
    }

    if (is_array($categoryArray)) {
        $request->merge(['category_name' => $categoryArray['name']]);
        //  Save the new priority and assign it back to the request input value
        $categoryCreated = oq_createOrUpdateCategory($request, null, $user->companyBranch->company, $user);
        //  Update request parameter with the category id
        $request->merge(['category_id' => $categoryCreated->id]);
    }

    if (is_array($branchArray)) {
        $request->merge(['branch_name' => $branchArray['name']]);
        //  Save the new branch and assign it back to the request input value
        $branchCreated = oq_createOrUpdatebranch($request, null, $user->companyBranch->company, $user);
        //  Extract the new company branch name and replace the current request value
        $request->merge(['company_branch_id' => $branchCreated->id]);
    }

    foreach ($request->all() as $key => $value) {
        $request[str_replace('jobcard_', '', $key)] = $value;
        unset($request[$key]);
    }

    if ($currJobcard == null) {
        //  Create the jobcard
        $jobcard = \App\Jobcard::create($request->all());
        $status = 'created';
    } else {
        //  Update the existing jobcard
        $jobcard = $currJobcard->update($request->all());
        $status = 'updated';
    }

    //  If the jobcard was created/updated successfully
    if ($jobcard) {
        //  re-retrieve the instance to get all of the fields in the table.
        if ($currJobcard == null) {
            $jobcard = $jobcard->fresh();
        } else {
            $jobcard = $currJobcard->fresh();
        }

        //  Record activity of a jobcard created
        $jobcardCreatedActivity = oq_saveActivity($jobcard, $status, $user);

        /*  Allocate the process form for tracking status
         *
            $process = $jobcard->processInstructions()->create([
                'process_form' => Auth::user()->companyBranch->company->processForms()->where('selected', 1)->first()->instructions,
            ]);
         */

        //  If we have the jobcard image and has been approved, then save it to Amazon S3 bucket
        if ($request->hasFile('new_jobcard_image')) {
            $document = oq_saveDocument($request, $jobcard, Input::file('new_jobcard_image'), 'jobcard_images', 'jobcard', $user);
        }

        //  Notify the user that the jobcard creation was successful
        oq_notify('Jobcard '.$status.' successfully!', 'success');
    } else {
        //  Record activity of a failed jobcard during creation
        $failType = ($status == 'created') ? 'create' : 'update';
        $jobcardCreatedActivity = oq_saveActivity(null, 'jobcard '.$failType.' failed', $user);

        //  Notify the user that the jobcard creation was unsuccessful
        oq_notify('Something went wrong '.$status.' the jobcard. Please try again', 'warning');
    }

    return $jobcard;
}

function oq_createOrUpdatePriority($request, $currPriority = null, $company, $user)
{
    //  Get the rules for validating a priority on creation
    $rules = oq_priority_create_v_rules();

    //  Customized error messages for validating a priority on creation
    $messages = oq_priority_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($request->all(), $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t create priority "'.$request->input('priority_name').'", check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Lets get all instanes of the priority inputs and rename approprietly
    $tempRequest = oq_replaceRequestInputNames($request, 'priority_');

    if ($currPriority == null) {
        //  Create the priority
        $priority = $company->priorities()->create($tempRequest->all());
        $status = 'created';
    } else {
        //  Update the existing priority
        $priority = $currPriority->update($tempRequest->all());
        $status = 'updated';
    }

    //  If the priority was created successfully
    if ($priority) {
        //  re-retrieve the instance to get all of the fields in the table.
        if ($currPriority == null) {
            $priority = $priority->fresh();
        } else {
            $priority = $currPriority->fresh();
        }

        //  Record activity of a new priority created
        $priorityActivity = oq_saveActivity($priority, $status, $user);
    } else {
        //  Record activity of a failed priority during creation
        $failType = ($status == 'created') ? 'create' : 'update';
        $priorityActivity = oq_saveActivity(null, 'priority '.$failType.' failed', $user);
    }

    return $priority;
}

function oq_createOrUpdateCostCenter($request, $currCostCenter = null, $company, $user)
{
    //  Get the rules for validating a cost center on creation
    $rules = oq_cost_center_create_v_rules();

    //  Customized error messages for validating a cost center on creation
    $messages = oq_cost_center_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($request->all(), $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t create cost center "'.$request->input('cost_center_name').'", check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Lets get all instanes of the cost center inputs and rename approprietly
    $tempRequest = oq_replaceRequestInputNames($request, 'cost_center_');

    if ($currCostCenter == null) {
        //  Create the cost center
        $cost_center = $company->costcenters()->create($tempRequest->all());
        $status = 'created';
    } else {
        //  Update the existing cost center
        $cost_center = $currCostCenter->update($tempRequest->all());
        $status = 'updated';
    }

    //  If the cost center was created successfully
    if ($cost_center) {
        //  re-retrieve the instance to get all of the fields in the table.
        if ($currCostCenter == null) {
            $cost_center = $cost_center->fresh();
        } else {
            $cost_center = $currCostCenter->fresh();
        }

        //  Record activity of a new cost center created
        $cost_centerActivity = oq_saveActivity($cost_center, $status, $user);
    } else {
        //  Record activity of a failed cost center during creation
        $failType = ($status == 'created') ? 'create' : 'update';
        $cost_centerActivity = oq_saveActivity(null, 'cost center '.$failType.' failed', $user);
    }

    return $cost_center;
}

function oq_createOrUpdateCategory($request, $currCategory = null, $company, $user)
{
    //  Get the rules for validating a category on creation
    $rules = oq_category_create_v_rules();

    //  Customized error messages for validating a category on creation
    $messages = oq_category_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($request->all(), $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t create category '.$request->input('category_name').', check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Lets get all instanes of the category inputs and rename approprietly
    $tempRequest = oq_replaceRequestInputNames($request, 'category_');

    if ($currCategory == null) {
        //  Create the category
        $category = $company->categories()->create($tempRequest->all());
        $status = 'created';
    } else {
        //  Update the existing category
        $category = $currCategory->update($tempRequest->all());
        $status = 'updated';
    }

    //  If the category was created successfully
    if ($category) {
        //  re-retrieve the instance to get all of the fields in the table.
        if ($currCategory == null) {
            $category = $category->fresh();
        } else {
            $category = $currCategory->fresh();
        }

        //  Record activity of a new category created
        $categoryActivity = oq_saveActivity($category, $status, $user);
    } else {
        //  Record activity of a failed category during creation
        $failType = ($status == 'created') ? 'create' : 'update';
        $categoryActivity = oq_saveActivity(null, 'category '.$failType.' failed', $user);
    }

    return $category;
}

function oq_createOrUpdatebranch($request, $currBranch = null, $company, $user)
{
    //  Get the rules for validating a branch on creation
    $rules = oq_branch_create_v_rules();

    //  Customized error messages for validating a branch on creation
    $messages = oq_branch_create_v_msgs();

    // Now pass the input and rules into the validator
    $validator = Validator::make($request->all(), $rules, $messages);

    // Check to see if validation fails or passes
    if ($validator->fails()) {
        //  Notify the user that validation failed
        oq_notify('Couldn\'t create branch '.$request->input('branch_name').', check your information!', 'danger');
        //  Return back with errors and old inputs
        return  ['failed_validation' => true, 'validator' => $validator];
    }

    //  Lets get all instanes of the branch inputs and rename approprietly
    $tempRequest = oq_replaceRequestInputNames($request, 'branch_');
    $tempRequest->merge(['company_id' => $company->id]);

    if ($currBranch == null) {
        //  Create the branch
        $branch = \App\CompanyBranch::create($tempRequest->all());
        $status = 'created';
    } else {
        //  Update the existing branch
        $branch = $currBranch->update($tempRequest->all());
        $status = 'updated';
    }

    //  If the branch was created successfully
    if ($branch) {
        //  re-retrieve the instance to get all of the fields in the table.
        if ($currBranch == null) {
            $branch = $branch->fresh();
        } else {
            $branch = $currBranch->fresh();
        }

        //  Record activity of a new branch created
        $branchActivity = oq_saveActivity($branch, $status, $user);
    } else {
        //  Record activity of a failed branch during creation
        $failType = ($status == 'created') ? 'create' : 'update';
        $branchActivity = oq_saveActivity(null, 'branch '.$failType.' failed', $user);
    }

    return $branch;
}

function oq_createdBy($model)
{
    //  Create the branch
    $createdBy = $model->recentActivities->where('type', 'created')->first()->createdBy;

    return $createdBy;
}

function oq_formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2).' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2).' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2).' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes.' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes.' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function oq_replaceRequestInputNames($request, $name)
{
    $tempRequest = new \Illuminate\Http\Request();

    //  Lets get all instanes of the branch inputs and rename approprietly
    foreach ($request->all() as $key => $value) {
        $tempRequest[str_replace($name, '', $key)] = $value;
    }

    return $tempRequest;
}
