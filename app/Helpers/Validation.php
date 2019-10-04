<?php

/**************************************************************
***************************************************************
***************************************************************
    USER RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

/*  Validation for when creating a user.
 */
function oq_user_create_v_rules()
{
    return $rules = array_merge([
        //  General Rules
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ],
        // Rules for Images/Files
        oq_document_create_v_rules());
}

function oq_user_create_v_msgs()
{
    return $messages = array_merge([
        //  General rule messages
        'first_name.required' => 'Enter first name',
        'first_name.string' => 'First name must only be letters, numbers or symbols',
        'first_name.max' => 'First name must not be more than 255 characters',

        'last_name.required' => 'Enter last name',
        'last_name.string' => 'Last name must only be letters, numbers or symbols',
        'last_name.max' => 'Last name must not be more than 255 characters',

        'username.required' => 'Enter username',
        'username.string' => 'Username must only be letters, numbers or symbols',
        'username.max' => 'Username must not be more than 255 characters',
        'username.unique' => 'Username already taken',

        'email.required' => 'Enter email address',
        'email.string' => 'Email must only be letters, numbers or symbols',
        'email.email' => 'Provide a valid email address',
        'email.max' => 'Email must not be more than 255 characters',
        'email.unique' => 'Email already taken',

        'password.required' => 'Enter password',
        'password.string' => 'Password must only be letters, numbers or symbols',
        'password.min' => 'Password must be atleast 6 more or characters',
        'password.confirmed' => 'Password does not match confirmation password',
      ],
        //  Rule messages for Images/Files
        oq_document_create_v_msgs());
}

/**************************************************************
***************************************************************
***************************************************************
    COMPANY RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

/*  Validation for when creating a company.
 */
function oq_company_create_v_rules()
{
    return $rules = array_merge([
        //  General Rules
        'company_name' => 'required',
        'company_phone_ext' => 'max:3',
        'company_phone_num' => 'max:13',
    ],
        // Rules for Images/Files
        oq_document_create_v_rules());
}

function oq_company_create_v_msgs()
{
    return $messages = array_merge([
        //  General rule messages
        'company_name.required' => 'Enter company name',
        'company_name.max' => 'Company name cannot be more than 255 characters',
        'company_name.min' => 'Company name must be atleast 3 characters',
        'company_email.unique' => 'This company email is already being used',
        'company_phone_ext.max' => 'Company phone number extension cannot be more than 3 characters',
        'company_phone_num.max' => 'Company phone number cannot be more than 13 characters',
      ],
        //  Rule messages for Images/Files
        oq_document_create_v_msgs());
}

/**************************************************************
***************************************************************
***************************************************************
    DOCUMENT RELATED VALIDATION [Images/Files]
***************************************************************
***************************************************************
**************************************************************/

function oq_document_create_v_rules()
{
    return $rules = [
        // Rules for Images/Files
        'files.*' => 'required|mimes:jpeg,jpg,png,gif|max:2000',
        'company_logo' => 'sometimes|mimes:jpeg,jpg,png,gif|max:2000',  // max 2000Kb/2Mb
        'company_quote' => 'sometimes|mimes:jpeg,jpg,png,pdf|max:2000', // max 2000Kb/2Mb
        'new_jobcard_image' => 'sometimes|mimes:jpeg,jpg,png,gif|max:2000', // max 2000Kb/2Mb
        'document_name' => 'sometimes|required',
    ];
}

function oq_document_create_v_msgs()
{
    return $messages = [
        // Rules for Images/Files
        'files.*.required' => 'Please upload file',
        'files.*.mimes' => 'Uploads must be a valid format e.g) jpeg,jpg,png,gif',
        'files.*.max' => 'Uploads should not be more than 2MB in size',
        'company_logo.mimes' => 'Company logo must be a valid image format e.g) jpeg,jpg,png,gif',
        'company_logo.max' => 'Company logo should not be more than 2MB in size',
        'company_quote.mimes' => 'Company quotation must be a valid file format e.g) jpeg,jpg,png,pdf',
        'company_quote.max' => 'Company quotation should not be more than 2MB in size',
        'new_jobcard_image.mimes' => 'Jobcard image must be a valid image format e.g) jpeg,jpg,png,gif',
        'new_jobcard_image.max' => 'Jobcard image should not be more than 2MB in size',
        'document_name.required' => 'Enter document name',
    ];
}

/**************************************************************
***************************************************************
***************************************************************
    JOBCARD RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

/*  Validation for when creating a jobcard.
 */
function oq_jobcard_create_v_rules($user = null)
{
    $company_id = null;

    if (!empty($user)) {
        if (!empty($user->companyBranch)) {
            if (!empty($user->companyBranch->company)) {
                $company_id = $user->companyBranch->company->id;
            }
        }
    }

    return $rules = array_merge([
        //  General Rules
        'jobcard_title' => 'required|max:255|min:3',
        'jobcard_start_date' => 'sometimes|date_format:"Y-m-d H:i:s"|required',
        'jobcard_end_date' => 'sometimes|date_format:"Y-m-d H:i:s"|required|after:today',

        /*  Advanced Validation
        *
        *  Note that when creating new priorities, cost centers, categories and branches,
        *  we want to make sure we do not make any repeated values since they will confuse
        *  users e.g) having priority Low, Medium, Medium, High. The "medium" values is duplicated
        *  and undesirable outcome. To prevent this we must first check and confirm that for the
        *  current company the user does not already have records using the same names.
        */

        //  The priority (name & company_id) must be unique per row
        'priority_id' => 'sometimes|integer|unique:priorities,name,null,who_created_id,priority_id,'.$company_id.',priority_type,company',
        //  The cost center (name & company_id) must be unique per row
        'cost_center_id' => 'sometimes|integer|unique:costcenters,name,null,who_created_id,cost_center_id,'.$company_id.',costcenter_type,company',
        //  The category (name & company_id) must be unique per row
        'category_id' => 'sometimes|integer|unique:categories,name,null,who_created_id,category_id,'.$company_id.',category_type,company',
        //  The branch (name & company_id) must be unique per row
        'company_branch_id' => 'sometimes|integer|unique:company_branches,destination,null,who_created_id,company_id,'.$company_id,
    ],
        /*  Rules for creating documents, priorities, costcenters,
         *  categories and branches while creating a jobcard
         */
        oq_document_create_v_rules(),
        oq_priority_create_v_rules(),
        oq_cost_center_create_v_rules(),
        oq_category_create_v_rules(),
        oq_branch_create_v_rules()
    );
}

function oq_jobcard_create_v_msgs($request)
{
    return $messages = array_merge([
        //  General rule messages
        'jobcard_title.required' => 'Enter jobcard title',
        'jobcard_title.max' => 'Title name cannot be more than 255 characters',
        'jobcard_title.min' => 'Title name must be atleast 3 characters',
        'jobcard_start_date.required' => 'Enter job start date',
        'jobcard_start_date.date' => 'Enter a valid start date',
        'jobcard_end_date.required' => 'Enter job end date',
        'jobcard_end_date.date' => 'Enter a valid end date',
        'jobcard_end_date.after' => 'End date must be a future date',
        'priority.required' => 'Select or create a new priority level',
        'priority.integer' => 'The priority id must reference an integer value',
        'priority.unique' => 'The new priority ('.$request->input('priority').') you tried to create already exists',
        'cost_center.required' => 'Select or create a new cost center',
        'cost_center.integer' => 'The cost center id must reference an integer value',
        'cost_center.unique' => 'The new cost center ('.$request->input('cost_center').') you tried to create already exists',
        'category.required' => 'Select or create a new category',
        'category.integer' => 'The category id must reference an integer value',
        'category.unique' => 'The new category ('.$request->input('category').') you tried to create already exists',
        'branch.required' => 'Select or create a new company branch',
        'branch.integer' => 'The company branch id must reference an integer value',
        'branch.unique' => 'The new company branch ('.$request->input('branch').') you tried to create already exists',
    ],
        //  Rule messages for Images/Files
        oq_document_create_v_msgs());
}

/**************************************************************
***************************************************************
***************************************************************
    PRIORITY RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

function oq_priority_create_v_rules()
{
    return $rules = [
        //  General rules
        'priority_name' => 'sometimes|required',
    ];
}

function oq_priority_create_v_msgs()
{
    return $messages = [
        'priority_name.required' => 'Enter priority name',
    ];
}

/**************************************************************
***************************************************************
***************************************************************
    COST CENTER RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

function oq_cost_center_create_v_rules()
{
    return $rules = [
        //  General rules
        'cost_center_name' => 'sometimes|required',
    ];
}

function oq_cost_center_create_v_msgs()
{
    return $messages = [
        'cost_center_name.required' => 'Enter cost center name',
    ];
}

/**************************************************************
***************************************************************
***************************************************************
    CATEGORY RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

function oq_category_create_v_rules()
{
    return $rules = [
        'category_name' => 'sometimes|required',
    ];
}

function oq_category_create_v_msgs()
{
    return $messages = [
        'category_name.required' => 'Enter category name',
    ];
}

/**************************************************************
***************************************************************
***************************************************************
    COMPANY BRANCH RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

function oq_branch_create_v_rules()
{
    return $rules = [
        'branch_name' => 'sometimes|required',
    ];
}

function oq_branch_create_v_msgs()
{
    return $messages = [
        'branch_name.required' => 'Enter company branch name',
    ];
}

/**************************************************************
***************************************************************
***************************************************************
    QUOTATION RELATED VALIDATION
***************************************************************
***************************************************************
**************************************************************/

function oq_quotation_create_v_rules()
{
    return $rules = [
        //  General rules
        'details' => 'required',
    ];
}

function oq_quotation_create_v_msgs()
{
    return $messages = [
        'details.required' => 'Quotation details have not been provided',
    ];
}
