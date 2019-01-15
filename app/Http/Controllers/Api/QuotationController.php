<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuotationController extends Controller
{
    public function update(Request $request, $quotation_id)
    {
        $updatedQuotation = request('quotation');

        if (!empty($updatedQuotation)) {
            try {
                //  Run query
                $quotation = Quotation::where('id', $quotation_id)->update([
                    'details' => json_encode($updatedQuotation['details'], JSON_FORCE_OBJECT),
                ]);

                //  If the lifecycle was updated successfully
                if ($quotation) {
                    //  refetch the updated quotation
                    $quotation = Quotation::find($quotation_id);

                    //  Action was executed successfully
                    return oq_api_notify($quotation, 200);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }

    public function store(Request $request)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $model_Type = request('model');                      //  Associated model e.g) jobcard
        $modelId = request('modelId');                      //  The id of the associated model
        $newQuotationDetails = request('details');

        /*  Validate and Create the new quotation associated with the users company and branch
         *  Update recent activities
         *
         */

        //  Get the rules for validating a quotation on creation
        $rules = oq_quotation_create_v_rules();

        //  Customized error messages for validating a quotation on creation
        $messages = oq_quotation_create_v_msgs();

        // Now pass the input and rules into the validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check to see if validation fails or passes
        if ($validator->fails()) {
            //  Notify the user that validation failed
            oq_notify('Couldn\'t update quotation, check your information!', 'danger');
            //  Return back with errors and old inputs
            return oq_api_notify_error('Failed validation', ['failed_validation' => true, 'validator' => $validator->errors()], 404);
        }

        //  Create the quotation
        $quotation = \App\Quotation::create([
            'details' => $newQuotationDetails,
            'trackable_type' => $model_Type,
            'trackable_id' => $modelId,
            'company_branch_id' => $user->companyBranch->id,
            'company_id' => $user->companyBranch->company->id,
        ]);

        $status = 'created';

        //  If the quotation was created/updated successfully
        if ($quotation) {
            //  re-retrieve the instance to get all of the fields in the table.
            $quotation = $quotation->fresh();

            //  Record activity of a quotation created
            $quotationCreatedActivity = oq_saveActivity($quotation, $status, $user, ['type' => 'created']);

            //  Record activity of a quotation authourized
            $quotationAuthourizedActivity = oq_saveActivity($quotation, $status, $user, ['type' => 'authourized']);

            //  Notify the user that the quotation creation was successful
            oq_notify('Quotation '.$status.' successfully!', 'success');
        } else {
            //  Record activity of a failed quotation during creation
            $failType = ($status == 'created') ? 'create' : 'update';
            $quotationCreatedActivity = oq_saveActivity(null, 'quotation '.$failType.' failed', $user);

            //  Notify the user that the quotation creation was unsuccessful
            oq_notify('Something went wrong '.$status.' the quotation. Please try again', 'warning');
        }

        //  return created quotation
        return oq_api_notify($quotation, 201);
    }
}
