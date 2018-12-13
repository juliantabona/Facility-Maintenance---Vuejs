<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use App\Jobcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no jobcards
        $jobcards = [];

        //  Which jobcards do we want to retrieve ???

        if (!empty(request('step'))) {
            try {
                $jobcardTemplateId = $user->companyBranch->company->formTemplate
                                          ->where('type', 'jobcard')
                                          ->where('selected', 1)
                                          ->first()
                                          ->id;

                $jobcards = new Jobcard();

                $jobcards = $jobcards->whereHas('statusLifecycle', function ($query) use ($jobcardTemplateId) {
                    $query->where('step', request('step'))
                          ->where('form_template_id', $jobcardTemplateId);
                });
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            /*  COMPANY JOBCARDS
            *  Get the company related jobcards if the user indicated
            *  This is normaly used by authenticated managers to access all
            *  jobcard resources in their respective company
            */
            if (!empty(request('company')) && request('company') == 1) {
                $order_join = 'jobcards';
                $jobcards = $user->companyBranch->company->jobcards();

            /*  BRANCH JOBCARDS
            *  Get the branch related jobcards by default
            *  This is normaly used by authenticated staff to access all
            *  jobcard resources in their respective company branch
            */
            } else {
                $jobcards = $user->companyBranch->jobcards();
            }
        }

        /*  ALL JOBCARDS
         *  We need to check if the user is authorized to view all jobcards
         *  This is normaly used by authorized superadmins to access all
         *  jobcard resources in the system.
         */
        if (!empty(request('all')) && request('all') == 1) {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL JOBCARDS    *
            /**********************************************************/

            //  New jobcard instance so that we can get all jobcards
            $jobcards = new Jobcard();
        }

        //  If we don't have any special order_joins, lets default it to nothing
        $order_join = isset($order_join) ? $order_join : '';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query, dont paginate to return relationship instance
                $jobcards = $jobcards->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query, dont paginate to return relationship instance
                $jobcards = $jobcards->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query, dont paginate to return relationship instance
                $jobcards = $jobcards->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any jobcards so far
            if (count($jobcards)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $jobcards->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($jobcards, 200);
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

            //  If we have any lifecycle so far
            if (count($lifecycle)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $lifecycle->load(oq_url_to_array(request('connections')));
                }
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

    public function updateLifecycle(Request $request, $jobcard_id)
    {
        $user = auth('api')->user();

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
        //  Get one, even if trashed
        if (request('withtrashed') == 1) {
            try {
                //  Run query
                $jobcard = Jobcard::withTrashed()->where('id', $jobcard_id)->first();
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
            //  Get only if not trashed
        } else {
            try {
                //  Run query
                $jobcard = Jobcard::where('id', $jobcard_id)->first();
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        }

        if (count($jobcard)) {
            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $jobcard->load(oq_url_to_array(request('connections')));
            }

            //  Action was executed successfully
            return oq_api_notify($jobcard, 200);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function contractors($jobcard_id)
    {
        $user = auth('api')->user();

        //  We start with no contractors
        $contractors = [];

        //  Check if the jobcard exists
        $jobcard = Jobcard::find($jobcard_id);

        if (!count($jobcard)) {
            //  API Response
            if (oq_viaAPI($request)) {
                //  No resource found
                oq_api_notify_no_resource();
            }
        }

        /*  We need to check if the user is authorized to view the jobcard contractors
         */

        //  if () {
        /***********************************************************
        *  CHECK IF THE USER IS AUTHORIZED TO VIEW CONTRACTORS     *
        /**********************************************************/
        //  }

        //  If we don't have any special order_joins, lets default it to nothing
        $order_join = 'jobcard_contractors';

        try {
            //  Run query, dont paginate to return relationship instance
            $contractors = $jobcard->contractorsList()->advancedFilter(['order_join' => $order_join]);

            //  If we have any jobcards so far
            if (count($contractors)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $contractors->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($contractors, 200);
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
        $response = oq_createOrUpdateJobcard($request, null, $user);

        //  If the validation did not pass
        if (oq_failed_validation($response)) {
            //  Return validation errors with an alert or json response if API request
            return oq_failed_validation_return($request, $response);
        }

        //  return created jobcard
        return oq_api_notify($response, 201);
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

    public function removeContractor($jobcard_id, $contractor_id)
    {
        try {
            //  Run query
            $jobcard = Jobcard::find($jobcard_id);
            $jobcard->contractorsList()->detach($contractor_id);
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
