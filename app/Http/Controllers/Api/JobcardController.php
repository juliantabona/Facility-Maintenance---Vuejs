<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use Validator;
use App\Jobcard;
use App\FormTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobcardController extends Controller
{
    public function index()
    {
        //  Jobcard Instance
        $data = ( new Jobcard() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the jobcards were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of jobcards
            $jobcards = $response;

            //  Action was executed successfully
            return oq_api_notify($jobcards, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function approve($jobcard_id)
    {
        //  Jobcard Instance
        $data = ( new Jobcard() )->initiateApprove($jobcard_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the jobcard was approved successfully
        if ($success) {
            //  If this is a success then we have the jobcard
            $jobcard = $response;

            //  Action was executed successfully
            return oq_api_notify($jobcard, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getLifecycleTemplates(Request $request)
    {
        $user = auth('api')->user();

        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW LIFECYCLE       *
        /**********************************************************/

        $companyId = auth('api')->user()->companyBranch->company->id;

        try {
            //  Run query
            $lifecycleTemplates = FormTemplate::where('company_id', $companyId)->get();
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        if (count($lifecycleTemplates)) {
            //  return lifecycle
            return oq_api_notify($lifecycleTemplates, 200);
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }

    public function addLifecycle(Request $request, $jobcard_id)
    {
        $user = auth('api')->user();

        /*********************************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO ADD LIFECYCLE TO JOBCARD       *
        /********************************************************************/

        if (!$user->can(['add-jobcard-lifecycle'])) {
            //  Not authourized
            return oq_api_notify('You don\'t have permission to add a jobcard lifecycle', 403);
        }

        $selectedTemplateId = request('selectedTemplateId');

        $jobcardTemplate = FormTemplate::where('id', $selectedTemplateId)->first();

        //  Check if the jobcard exists
        $jobcard = Jobcard::find($jobcard_id);

        if ($jobcard) {
            try {
                //  Run query
                $statusLifecycle = $jobcard->statusLifecycle()->create([
                    'template' => $jobcardTemplate->form_template,
                    'form_template_id' => $jobcardTemplate->id,
                ]);

                if (count($statusLifecycle)) {
                    //  return lifecycle
                    return oq_api_notify($statusLifecycle, 203);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            return oq_api_notify_error('Jobcard not found', null, 404);
        }

        return oq_api_notify_error('Update Error', null, 404);
    }

    public function getLifecycleStages(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no allocations
        $allocations = [];

        /*  We need to check if the user is authorized to retrieve jobcard allocations
        */

        //  if () {
        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO GET ALLOCATIONS      *
        /**********************************************************/
        //  }

        /*  COMPANY JOBCARD ALLOCATIONS
         *  Get the company form template currently in use
         */
        $formTemplate = $user->companyBranch->company->formTemplate->where('selected', 1)->first();

        try {
            // Run query
            $allocations = $formTemplate->formAllocations()
                ->select(DB::raw('COUNT(step) count, step'))
                ->groupBy('step')
                ->get();

            $allocations = [
                'template' => $formTemplate->form_template,
                'allocations' => $allocations,
            ];
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($allocations, 200);
    }

    public function getLifecycle(Request $request, $jobcard_id)
    {
        $user = auth('api')->user();

        //  Check if the jobcard exists
        $jobcard = Jobcard::find($jobcard_id);

        if (!count($jobcard)) {
            //  API Response
            if (oq_viaAPI($request)) {
                //  No resource found
                oq_api_notify_no_resource();
            }
        }

        /*  We need to check if the user is authorized to view the jobcard lifecycle
         */

        //  if () {
        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW LIFECYCLE       *
        /**********************************************************/
        //  }

        try {
            //  Run query
            $lifecycle = $jobcard->statusLifecycle->first();
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        if (count($lifecycle)) {
            //  return lifecycle
            return oq_api_notify($lifecycle, 200);
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }

    public function updateLifecycle(Request $request, $jobcard_id)
    {
        $user = auth('api')->user();

        /****************************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO UPDATE JOBCARD LIFECYCLE  *
        /***************************************************************/

        if (!$user->can(['update-jobcard-lifecycle'])) {
            //  Not authourized
            return oq_api_notify('You don\'t have permission to update the jobcard status', 403);
        }

        //  Check if the jobcard exists
        $jobcard = Jobcard::find($jobcard_id);

        //  Check if the updated lifecycle template was provided
        if (empty(request('template'))) {
            //  API Response
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Include the updated lifecycle',
                ], 422);
            }
        }

        //  Check if the next step was provided
        if (empty(request('nextStep'))) {
            //  API Response
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Include the next lifecycle step',
                ], 422);
            }
        }

        if (!count($jobcard)) {
            //  API Response
            if (oq_viaAPI($request)) {
                //  No resource found
                oq_api_notify_no_resource();
            }
        }

        /*  We need to check if the user is authorized to update the jobcard lifecycle
         */

        //  if () {
        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO UPDATE LIFECYCLE     *
        /**********************************************************/
        //  }

        try {
            //  Run query
            $lifecycle = $jobcard->statusLifecycle()->update([
                'template' => json_encode(request('template')),
                'step' => request('nextStep'),
            ]);

            //  If the lifecycle was updated successfully
            if ($lifecycle) {
                $updatedLifecycle = $jobcard->statusLifecycle;

                //  Action was executed successfully
                return oq_api_notify($updatedLifecycle, 200);
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($lifecycle, 200);

        if (count($lifecycle)) {
            //  return lifecycle
            return oq_api_notify($lifecycle, 200);
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }

    public function show($jobcard_id)
    {
        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $jobcard = Jobcard::withTrashed()->where('id', $jobcard_id)->first();
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $jobcard = Jobcard::onlyTrashed()->where('id', $jobcard_id)->first();
            //  Get all except trashed
            } else {
                //  Run query
                $jobcard = Jobcard::where('id', $jobcard_id)->first();
            }

            //  If we have any jobcard so far
            if (count($jobcard)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $jobcard->load(oq_url_to_array(request('connections')));
                }

                return $jobcard;
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function suppliers($jobcard_id)
    {
        $user = auth('api')->user();

        //  We start with no suppliers
        $suppliers = [];

        //  Check if the jobcard exists
        $jobcard = Jobcard::find($jobcard_id);

        if (!count($jobcard)) {
            //  API Response
            if (oq_viaAPI($request)) {
                //  No resource found
                oq_api_notify_no_resource();
            }
        }

        /*  We need to check if the user is authorized to view the jobcard suppliers
         */

        //  if () {
        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW CONTRACTORS     *
        /**********************************************************/
        //  }

        //  If we don't have any special order_joins, lets default it to nothing
        $order_join = 'jobcard_suppliers';

        try {
            //  Run query, dont paginate to return relationship instance
            $suppliers = $jobcard->suppliersList()->advancedFilter(['order_join' => $order_join]);

            //  If we have any jobcards so far
            if (count($suppliers)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $suppliers->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($suppliers, 200);
    }

    public function store(Request $request)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*  Validate and Create the new jobcard and associated branch and upload related documents
         *  [e.g logo, jobcard profile, other documents]. Update recent activities
         *
         *  @param $request - The request parameters used to create a new jobcard
         *  @param $user - The user creating the jobcard
         *
         *  @return Validator - If validation failed
         *  @return jobcard - If successful
         */

        //  Get the rules for validating a jobcard on creation
        $rules = oq_jobcard_create_v_rules($user);

        //  Customized error messages for validating a jobcard on creation
        $messages = oq_jobcard_create_v_msgs($request);

        // Now pass the input and rules into the validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check to see if validation fails or passes
        if ($validator->fails()) {
            //  Notify the user that validation failed
            oq_notify('Couldn\'t update jobcard, check your information!', 'danger');
            //  Return back with errors and old inputs
            return  ['failed_validation' => true, 'validator' => $validator->errors()];
        }

        foreach ($request->all() as $key => $value) {
            $request[str_replace('jobcard_', '', $key)] = $value;
            unset($request[$key]);
        }

        //  Create the jobcard
        $jobcard = \App\Jobcard::create(
            array_merge($request->all(), ['company_branch_id' => $user->company_branch_id])
        );

        //  Add the priority to the jobcard
        if (!empty(request('priority'))) {
            $jobcard->priority()->sync(request('priority'));
        }

        //  Add the categories to the jobcard
        if (!empty(request('categories'))) {
            $jobcard->categories()->sync(request('categories'));
        }

        //  Add the costcenters to the jobcard
        if (!empty(request('costcenters'))) {
            $jobcard->costCenters()->sync(request('costcenters'));
        }

        $status = 'created';

        //  If the jobcard was created/updated successfully
        if ($jobcard) {
            //  re-retrieve the instance to get all of the fields in the table.
            $jobcard = $jobcard->fresh();

            //  Record activity of a jobcard created
            $jobcardCreatedActivity = oq_saveActivity($jobcard, $status, $user, ['type' => 'created']);

            //  Record activity of a jobcard authourized
            $jobcardAuthourizedActivity = oq_saveActivity($jobcard, $status, $user, ['type' => 'authourized']);

            //  Allocate the process form for tracking status
            $companyId = $user->companyBranch->company->id;
            if ($companyId) {
                //  Get the Jobcard Form Template
                $jobcardTemplate = FormTemplate::where('type', 'jobcard')->where('selected', '1')->where('company_id', $companyId)->first();

                //  If we have a Form Template
                if (count($jobcardTemplate)) {
                    //  Allocate it to the jobcard to serve as the jobcard lifecycle
                    $statusLifecycle = $jobcard->statusLifecycle()->create([
                        'template' => $jobcardTemplate->form_template,
                        'form_template_id' => $jobcardTemplate->id,
                    ]);
                }
            }

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

        //  return created jobcard
        return oq_api_notify($jobcard, 201);
    }

    public function update(Request $request, $jobcard_id)
    {
        //  Get the jobcard, even if trashed
        $jobcard = Jobcard::withTrashed()->where('id', $jobcard_id)->first();

        //  Check if we don't have a resource
        if (!count($jobcard)) {
            //  No resource found
            return oq_api_notify_no_resource();
        }

        /*  Validate and Update the jobcard and upload related documents
         *  [e.g jobcard images or files]. Update recent activities
         *
         *  @param $request - The request with all the parameters to update
         *  @param $jobcard - The jobcard we are updating
         *  @param $user - The user updating the jobcard
         *
         *  @return Validator - If validation failed
         *  @return jobcard - If successful
         */
        //  Current authenticated user
        $user = auth('api')->user();

        $response = oq_createOrUpdateJobcard($request, $jobcard, $user);

        //  If the validation did not pass
        if (oq_failed_validation($response)) {
            //  Return validation errors with an alert or json response if API request
            return oq_failed_validation_return($request, $response);
        }

        //  return updated jobcard
        return oq_api_notify($response, 200);
    }

    public function removeClient($jobcard_id)
    {
        try {
            //  Run query
            $jobcard = Jobcard::find($jobcard_id);
            $jobcard->client_id = null;
            $jobcard->save();
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        if ($jobcard) {
            //  Action was executed successfully
            return response()->json(null, 204);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function removeSupplier($jobcard_id, $supplier_id)
    {
        try {
            //  Run query
            $jobcard = Jobcard::find($jobcard_id);
            $jobcard->suppliersList()->detach($supplier_id);
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        if ($jobcard) {
            //  Action was executed successfully
            return response()->json(null, 204);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function delete($jobcard_id)
    {
        //  Get the jobcard, even if trashed
        $jobcard = Jobcard::withTrashed()->find($jobcard_id);

        //  Check if we have a resource
        if (!count($jobcard)) {
            //  No resource found
            return oq_api_notify_no_resource();
        }

        //  If the param "permanent" is set to true
        if (request('permanent') == 1) {
            //  Permanently delete jobcard
            $jobcard->forceDelete();
        } else {
            //  Soft delete (trash) the jobcard
            $jobcard->delete();
        }

        return response()->json(null, 204);
    }
}
