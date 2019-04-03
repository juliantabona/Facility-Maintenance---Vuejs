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
    public function getEstimatedStats()
    {
        //  Start getting the jobcard stats
        $data = ( new Jobcard() )->getStatistics();
        $success = $data['success'];
        $response = $data['response'];

        //  If the jobcard statistics were found successfully
        if ($success) {
            //  If this is a success then we have the statistics returned
            $stats = $response;

            //  Action was executed successfully
            return oq_api_notify($stats, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

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

    public function store()
    {
        //  Jobcard Instance
        $data = ( new Jobcard() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the jobcard was created successfully
        if ($success) {
            //  If this is a success then we have the jobcard
            $jobcard = $response;

            //  Action was executed successfully
            return oq_api_notify($jobcard, 200);
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

    public function updateLifecycleProgress($jobcard_id)
    {
        //  Jobcard Instance
        $data = ( new Jobcard() )->initiateUpdateLifecycleProgress($jobcard_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the jobcard was recorded successfully
        if ($success) {
            //  If this is a success then we have the jobcard
            $jobcard = $response;

            //  Action was executed successfully
            return oq_api_notify($jobcard, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function undoLifecycleProgress($jobcard_id)
    {
        //  Jobcard Instance
        $data = ( new Jobcard() )->initiateUndoLifecycleProgress($jobcard_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the jobcard was recorded successfully
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
