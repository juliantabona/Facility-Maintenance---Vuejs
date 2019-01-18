<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Invoice;
use App\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no quotations
        $quotations = [];

        //  Query data
        $type = request('model', 'branch');      //  e.g) company, branch
        $model_id = request('modelId');          //  The id of the client/supplier for getting related quotations

        /*  First thing is first, we need to understand one of 9 scenerios, Either we want:
         *
         *  1) Only quotations for a related COMPANY of the authenticated user (NO STEPS)
         *  2) Only quotations for a related BRANCH of the authenticated user (NO STEPS)
         *  3) Only quotations for a related CLIENT of the authenticated user (NO STEPS)
         *  4) Only quotations for a related CONTRACTOR of the authenticated user (NO STEPS)
         *  5) Only quotations in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given COMPANY of the authenticated user
         *  6) Only quotations in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given BRANCH of the authenticated user
         *  7) Only quotations in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given CLIENT of the authenticated user
         *  8) Only quotations in their respective steps e.g) Open, Pending, Closed, e.t.c...
         *     for a given CONTRACTOR of the authenticated user
         *  9) All quotations in the system e.g) If SuperAdmin needs access to all data
         *
         *  Once we have those quotations we will determine whether we want any of the following
         *
         *  1) All quotations aswell as the trashed ones
         *  2) Only quotations that are trashed
         *  3) Only quotations that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific quotations
         *  If the user indicated that they want quotations related to their company,
         *  then get the quotations related to the authenticated users company.
         *  They must indicate using the query "model" set to "company".
         */
        if ($type == 'company') {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW COMPANY QUOTATIONS    *
            /**************************************************************/

            $quotations = $user->companyBranch->company->quotations();

        /*  User Branch specific quotations
         *  If the user indicated that they want quotations related to their branch,
         *  then get the quotations related to the authenticated users branch.
         *  They must indicate using the query "model" set to "branch".
         */
        } elseif ($type == 'branch') {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW BRANCH QUOTATIONS    *
            /**************************************************************/

            $quotations = $user->companyBranch->quotations();

        /*  Client specific quotations
         *  If the user indicated that they want quotations related to a specific client,
         *  then get the quotations related to that client. They must indicate using the
         *  query "model" set to "client" and "model_id" to the company unique id.
         */
        } elseif ($type == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL QUOTATIONS    *
            /**********************************************************/

            /*  ALL QUOTATIONS
            *  If the user wants all the quotations in the system, they must indicate
            *  using the query "all" set to "1". This is normaly used by authorized
            *  superadmins to access all quotation resources in the system.
            */

            /*   Create a new quotation instance that can be used to retrieve all quotations
             */
            $quotations = new Quotation();
        }

        /*  To avoid sql order_by error for ambigious fields e.g) created_at
         *  we must specify the order_join.
         *
         *  Order joins help us when using the "advancedFilter()" method. Usually
         *  we need to specify the joining table so that the system is not confused
         *  by similar column names that exist on joining tables. E.g) the column
         *  "created_at" can exist in multiple table and the system might not know
         *  whether the "order_by" is for table_1 created_at or table 2 created_at.
         *  By specifying this we end up with "table_1.created_at"
         *
         *  If we don't have any special order_joins, lets default it to nothing
         */

        $order_join = 'quotations';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $quotations = $quotations->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $quotations = $quotations->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query
                $quotations = $quotations->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any quotations so far
            if (count($quotations)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $quotations->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($quotations, 200);
    }

    public function show($quotation_id)
    {
        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $quotation = Quotation::withTrashed()->where('id', $quotation_id)->first();
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $quotation = Quotation::onlyTrashed()->where('id', $quotation_id)->first();
            //  Get all except trashed
            } else {
                //  Run query
                $quotation = Quotation::where('id', $quotation_id)->first();
            }

            //  If we have any quotation so far
            if (count($quotation)) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $quotation->load(oq_url_to_array(request('connections')));
                }

                return $quotation;
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

    public function update(Request $request, $quotation_id)
    {
        $quotation = request('quotation');

        if (!empty($quotation)) {
            try {
                //  Run query
                $quotation = Quotation::where('id', $quotation_id)->update([
                    'status' => $quotation['status'],
                    'heading' => $quotation['heading'],
                    'reference_no_title' => $quotation['reference_no_title'],
                    'reference_no_value' => $quotation['reference_no_value'],
                    'created_date_title' => $quotation['created_date_title'],
                    'created_date_value' => $quotation['created_date_value'],
                    'expiry_date_title' => $quotation['expiry_date_title'],
                    'expiry_date_value' => $quotation['expiry_date_value'],
                    'sub_total_title' => $quotation['sub_total_title'],
                    'sub_total_value' => $quotation['sub_total_value'],
                    'grand_total_title' => $quotation['grand_total_title'],
                    'grand_total_value' => $quotation['grand_total_value'],
                    'currency_type' => json_encode($quotation['currency_type'], JSON_FORCE_OBJECT),
                    'calculated_taxes' => json_encode($quotation['calculated_taxes'], JSON_FORCE_OBJECT),
                    'quote_to_title' => $quotation['quote_to_title'],
                    'customized_company_details' => json_encode($quotation['customized_company_details'], JSON_FORCE_OBJECT),
                    'customized_client_details' => json_encode($quotation['customized_client_details'], JSON_FORCE_OBJECT),
                    'client_id' => $quotation['client_id'],
                    'table_columns' => json_encode($quotation['table_columns'], JSON_FORCE_OBJECT),
                    'items' => json_encode($quotation['items'], JSON_FORCE_OBJECT),
                    'notes' => json_encode($quotation['notes'], JSON_FORCE_OBJECT),
                    'colors' => json_encode($quotation['colors'], JSON_FORCE_OBJECT),
                    'footer' => $quotation['footer'],
                    'trackable_id' => $quotation['trackable_id'],
                    'trackable_type' => $quotation['trackable_type'],
                    'company_branch_id' => $quotation['company_branch_id'],
                    'company_id' => $quotation['company_id'],
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
        $model_Type = request('model');                      //  Associated model e.g) quotation
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

        //  Update the reference no
        $quotationNumber = $quotation->count();
        $quotation->update(['reference_no_value' => '0' + $quotationNumber]);

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

    public function convertToInvoice(Request $request, $quotation_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        $quotation = Quotation::find($quotation_id)->first();
        $template = json_decode(json_encode($user->companyBranch->company->settings->details['invoiceTemplate']), false);

        if (!empty($quotation)) {
            try {
                //  Run query
                $invoice = Invoice::create([
                    'status' => $template->status,
                    'heading' => $template->heading,
                    'reference_no_title' => $template->reference_no_title,
                    'reference_no_value' => $quotation['reference_no_value'],
                    'created_date_title' => $template->created_date_title,
                    'created_date_value' => $quotation['created_date_value'],
                    'expiry_date_title' => $template->expiry_date_title,
                    'expiry_date_value' => $quotation['expiry_date_value'],
                    'sub_total_title' => $template->sub_total_title,
                    'sub_total_value' => $quotation['sub_total_value'],
                    'grand_total_title' => $template->grand_total_title,
                    'grand_total_value' => $quotation['grand_total_value'],
                    'currency_type' => $template->currency_type,
                    'calculated_taxes' => $quotation['calculated_taxes'],
                    'invoice_to_title' => $template->invoice_to_title,
                    'customized_company_details' => $quotation['customized_company_details'],
                    'customized_client_details' => $quotation['customized_client_details'],
                    'client_id' => $quotation['client_id'],
                    'table_columns' => $template->table_columns,
                    'items' => $quotation['items'],
                    'notes' => $template->notes,
                    'colors' => $template->colors,
                    'footer' => $template->footer,
                    'quotation_id' => $quotation->id,
                    'trackable_id' => $quotation['trackable_id'],
                    'trackable_type' => $quotation['trackable_type'],
                    'company_branch_id' => $user->companyBranch->id,
                    'company_id' => $user->companyBranch->company->id,
                ]);

                //  Update the reference no
                $invoiceNumber = $invoice->count();
                $invoice->update(['reference_no_value' => '0' + $invoiceNumber]);

                //  If the lifecycle was updated successfully
                if ($invoice) {
                    //  refetch the updated quotation
                    $invoice = $invoice->fresh();

                    //  Action was executed successfully
                    return oq_api_notify($invoice, 200);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }
}
