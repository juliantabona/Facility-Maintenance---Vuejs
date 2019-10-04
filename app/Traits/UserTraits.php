<?php

namespace App\Traits;

use DB;
use Mail;
use App\User;
use App\Phone;
use App\Document;
use App\VerifyUser;
use App\PasswordResetTokens;
use App\Mail\ActivateAccount;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\URL;
use App\Notifications\UserCreated;
use App\Notifications\UserUpdated;
use Illuminate\Support\Facades\Hash;

//  Resources
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Users as UsersResource;

trait UserTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($users = null)
    {

        try {

            if( $users ){
                
                //  Transform the users
                return new UsersResource($users);

            }else{
                
                //  Transform the company
                return new UserResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }


    /*  getUsers() method:
     *
     *  This is used to return users
     *
     */
    public function getUsers( $options = [] )
    {
        /************************************
        *  CHECK IF THE USER IS AUTHORIZED  *
        /************************************/

        try {

            //  Get all the users
            $users = $this->all();

            if( $users ){

                //  Transform the users
                return new UsersResource($users);

            }else{

                //  Otherwise we don't have a resource to return
                return oq_api_notify_no_resource();
            
            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  getUser() method:
     *
     *  This is used to return the specified or currently authenticated user
     *
     */
    public function getUser( $user_id = null )
    {
        /************************************
        *  CHECK IF THE USER IS AUTHORIZED  *
        /************************************/

        try {

            //  Get the specified user or authenticated user
            $user = $user_id ? User::find($user_id) : auth('api')->user();

            if( $user ){

                //  Transform the authenticated user
                return new UserResource($user);

            }else{

                //  Otherwise we don't have a resource to return
                return oq_api_notify_no_resource();
            
            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }
















    

    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of company results.
     *
     */
    public function initiateGetAll($options = array())
    {
        //  Default settings
        $defaults = array(
            'paginate' => true,
        );

        //  Replace defaults with any provided options
        $config = array_merge($defaults, $options);

        //  If we overide using the request
        $requestPagination = request('paginate');
        if (isset($requestPagination) && ($requestPagination == 0 || $requestPagination == 1)) {
            $config['paginate'] = $requestPagination == 1 ? true : false;
        }

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  $allocation = all, company, branch
        /*
         *  The $allocation variable is used to determine where the data is being
         *  pulled from. The user may request data from three possible sources.
         *  1) Data may come from the associated authenticated user branch
         *  2) Data may come from the associated authenticated user company
         *  3) Data may come from the whole bucket meaning outside the scope of the
         *     authenticated user. This means we can access all possible records
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = strtolower(request('allocation'));

        /*
         *  $type = customer, supplier, staff
        /*
         *  The $type variable is used to determine which type of company to pull.
         *  The user may request data of type.
         *  1) customer: A company that is listed as a customer/customer
         *  2) supplier: A company that is listed as a supplier/vendor/hawker
         */
        $type = strtolower(request('type'));

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL USERS         *
            /********************************************************/

            //  Get the current company instance
            $model = $this;
        } elseif ($allocation == 'branch') {
            /**********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH USERS    *
            /**********************************************************/

            // Only get users associated to the company branch
            $model = $auth_user->companyBranch;
        } else {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY USERS    *
            /***********************************************************/

            //  Only get users associated to the company
            $model = $auth_user->company;
        }

        /*  If user indicated to only return customer dierctories
        */
        if ($type == 'customer') {
            $auth_users = $model->userCustomers();

        /*  If user indicated to only return supplier dierctories
        */
        } elseif ($type == 'supplier') {
            $auth_users = $model->userSuppliers();

        /*  If user indicated to only return staff dierctories
        */
        } elseif ($type == 'staff') {
            $auth_users = $model->userStaff();

        /*  If user did not indicate any specific group
        */
        } else {
            //  If the $type is a list e.g) customer,supplier
            $type = explode(',', $type);

            if (count($type)) {
                $auth_users = $model->userDirectory()->whereIn('user_directory.type', $type);
            } else {
                $auth_users = $model->userDirectory();
            }
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

        $order_join = 'users';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $auth_users = $auth_users->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $auth_users = $auth_users->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $auth_users = $auth_users->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $auth_users = $auth_users->get();
            }

            //  If we have any users so far
            if ($auth_users) {
                //  Eager load other relationships wanted if specified
                if (strtolower(request('connections'))) {
                    $auth_users->load(oq_url_to_array(strtolower(request('connections'))));
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $auth_users];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    public function initiateRegistration()
    {
        //  Start registration process
        $data = $this->initiateCreate(true);
        $success = $data['success'];
        $response = $data['response'];

        //  If the user was created successfully
        if ($success) {
            //  If this is a success then we have the user
            $user = $response;

            //  Create and send an account activation token and email
            $user->initiateSendAccountActivationMail();

            //  Action was executed successfully
            return $user;
        }

        return false;
    }

    public function initiateCreate($selfRegistration = false)
    {
        //  If the user is not registering then get the current authenticated user
        $auth_user = $selfRegistration ? null : auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE A USER     *
         ******************************************************/

        /*********************************************
         *   VALIDATE USER INFORMATION              *
         ********************************************/

        try {
            $template = [
                /*  Basic Info  */
                'first_name' => request('first_name') ?? null,
                'last_name' => request('last_name') ?? null,
                'date_of_birth' => request('date_of_birth') ?? null,
                'gender' => request('gender') ?? null,
                'bio' => request('bio') ?? null,

                /*  Address Info  */
                'address_1' => request('address_1') ?? null,
                'address_2' => request('address_2') ?? null,
                'country' => request('country') ?? null,
                'province' => request('province') ?? null,
                'city' => request('city') ?? null,
                'postal_or_zipcode' => request('postal_or_zipcode') ?? null,

                /*  Account Info  */
                'email' => request('email') ?? null,
                'additional_email' => request('additional_email') ?? null,
                'username' => request('username') ?? null,
                'password' => Hash::make(request('password')) ?? null,
                'verified' => 0,
                'setup' => 0,

                /*  Social Info  */
                'facebook_link' => request('facebook_link') ?? null,
                'twitter_link' => request('twitter_link') ?? null,
                'linkedin_link' => request('linkedin_link') ?? null,
                'instagram_link' => request('instagram_link') ?? null,
                'youtube_link' => request('youtube_link') ?? null,
            ];

            //  Create the user
            $user = $this->create($template)->fresh();

            //  If the user was created successfully
            if ($user) {
                //  If th user is registering (Creating an account for themselves)
                if ($selfRegistration) {
                    //  Set the auth_user as the current created user
                    $auth_user = $user;
                }

                //  Check whether or not the auth company has a relationship with the created user e.g) customer/supplier
                $this->checkAndCreateRelationship($user);

                //  Check if the user has any phones to add and replace
                $this->checkAndUpdatePhones($user);

                //  Check whether or not the user has any image to upload
                $this->checkAndUploadImage($user);

                //  refetch the updated user
                $user = $user->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new UserCreated($user));

                //  Record activity of a user created
                $status = 'created';

                $userCreatedActivity = oq_saveActivity($user, $auth_user, $status, $template);

                //  Action was executed successfully
                return ['success' => true, 'response' => $user];
            }
        } catch (\Exception $e) {
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing company. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the company.
     *
     */
    public function initiateUpdate($user_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE A USER     *
         ******************************************************/

        /*********************************************
         *   VALIDATE USER INFORMATION              *
         ********************************************/

        $template = [
            /*  Basic Info  */
            'first_name' => request('first_name') ?? null,
            'last_name' => request('last_name') ?? null,
            'date_of_birth' => request('date_of_birth') ?? null,
            'gender' => request('gender') ?? null,
            'bio' => request('bio') ?? null,

            /*  Address Info  */
            'address_1' => request('address_1') ?? null,
            'address_2' => request('address_2') ?? null,
            'country' => request('country') ?? null,
            'province' => request('province') ?? null,
            'city' => request('city') ?? null,
            'postal_or_zipcode' => request('postal_or_zipcode') ?? null,

            /*  Account Info  */
            'email' => request('email') ?? null,
            'additional_email' => request('additional_email') ?? null,
            'username' => request('username') ?? null,
            'password' => Hash::make(request('password')) ?? null,
            'verified' => 0,
            'setup' => 0,

            /*  Social Info  */
            'facebook_link' => request('facebook_link') ?? null,
            'twitter_link' => request('twitter_link') ?? null,
            'linkedin_link' => request('linkedin_link') ?? null,
            'instagram_link' => request('instagram_link') ?? null,
            'youtube_link' => request('youtube_link') ?? null,
        ];

        //  Foreach of the template fields
        foreach ($template as $key => $value) {
            //  Get all the available request parameters provided by the user
            $requestParams = collect( request()->all() )->keys()->toArray() ?? [];

            //  If the user did not specify a field in the template then remove that field from the template
            if (!in_array($key, $requestParams)) {
                //  Remove the field
                unset($template[$key]);
            }
        }

        try {
            //  Update the user
            $user = $this->where('id', $user_id)->first()->update($template);

            //  If the user was updated successfully
            if ($user) {

                $user = User::find($user_id);

                //  Check whether or not the auth company has a relationship with the created user e.g) customer/supplier
                $this->checkAndCreateRelationship($user);

                //  Check if the user has any phones to add and replace
                $this->checkAndUpdatePhones($user);

                //  Check whether or not the product has any image to upload
                $this->checkAndUploadImage($user);

                //  refetch the updated user
                $user = $user->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                //  $auth_user->notify(new UserUpdated($user));

                //  Record activity of a user created
                $status = 'updated';

                $userUpdatedActivity = oq_saveActivity($user, $auth_user, $status, $template);

                //  Action was executed successfully
                return ['success' => true, 'response' => $user];
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_no_resource()];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    public function checkAndCreateRelationship($user)
    {
        $auth_user = auth('api')->user();

        /*  relationship:
         *  This is a variable used to determine if the current user being created has
         *  a relationship as a customer/supplier to the auth users main company. Sometimes
         *  when creating a new user, we may want to assign that user as either a
         *  customer/supplier to the user directory. We can do this if the relationship
         *  variable has been set with the appropriate type (customer/supplier)
         */
        $relationship = request('relationship') ?? null;

        if (isset($relationship) && !empty($relationship)) {
            //  Delete any previous relationship
            DB::table('user_directory')->where([
                ['user_id', $user->id],                           //  id of the current user
                ['owning_company_id', $auth_user->user_id],        //  id of the owning company
            ])->delete();

            //  Add to user directory
            DB::table('user_directory')->insert([
                'user_id' => $user->id,                                 //  id of the current user
                'owning_branch_id' => $auth_user->company_branch_id,    //  id of the owning company branch
                'owning_company_id' => $auth_user->company_id,          //  id of the owning company
                'type' => request('relationship'),                      //  relationship type e.g customer/supplier
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ]);
        }
    }

    public function checkAndUploadImage($auth_user)
    {
        /*  profile_image:
         *  This is a variable used to determine if the current user being created has
         *  an image file to upload. Sometimes when creating a new user, we may want to
         *  also upload the user image at the same time. We can do this if the
         *  profile_image variable has been set with the image file (type=binary)
         */
        $File = request('profile_image');

        if (isset($File) && !empty($File) && request()->hasFile('profile_image')) {
            //  Start upload process of files
            $data = ( new Document() )->saveDocument(request(), $auth_user->id, 'user', $File, 'profile_images', 'profile_image', true);
        }
    }

    public function checkAndUpdatePhones($auth_user)
    {
        /*  phones:
         *  This is a variable used to determine if the current user being created has
         *  any phones to be added. Sometimes when creating a new user, we may want to
         *  add and replace existing phones to that user. We can do this if the phones
         *  variable has been set with an array list of phone numbers.
         */
        //  Get any associated phones if any
        $phones = json_decode(request('phones'), true);

        if (isset($phones) && !empty($phones)) {
            //  Add new phone numbers
            $phoneInstance = new Phone();

            $data = $phoneInstance->initiateCreate($auth_user->id, 'user', $phones, $replace = true);
            $success = $data['success'];
            $phones = $data['response'];

            if ($success) {
                return $phones;
            }
        }

        //  Phones not added
        return false;
    }

    /*  getStatistics() method:
     *
    /*  This method is used to get the overall statistics of the users,
     *  showing information of users in their respective states such as
     *  1) Name of status
     *  2) Total number of users in each respective status
     *  3) Total sum of the grand totals in each respective status
     *  4) The base currency used by the associated company
     *
     *  Example of returned output:
        {
            "stats": [
                {
                    "grand_total": null,
                    "total_count": 0,
                    "name": "Draft"
                },
                {
                    "grand_total": 23450,
                    "total_count": 6,
                    "name": "Approved"
                },
                {
                    "grand_total": 45240,
                    "total_count": 2,
                    "name": "Sent"
                },
                {
                    "grand_total": 1250,
                    "total_count": 1,
                    "name": "Cancelled"
                },
                {
                    "grand_total": 18560,
                    "total_count": 5,
                    "name": "Expired"
                },
                {
                    "grand_total": 75880,
                    "total_count": 12,
                    "name": "Paid"
                }
            ],
            "base_currency": {
                "country": "Botswana",
                "currency": {
                    "iso": {
                        "code": "BWP",
                        "number": "072"
                    },
                    "name": "Pula",
                    "symbol": "P"
                }
            }
        }
     *
     */

    public function getStatistics()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        //  Start getting the users
        $data = $this->initiateGetAll(['paginate' => false]);
        $success = $data['success'];
        $response = $data['response'];

        if ($success) {
            try {
                //  Get all the available users so far
                $auth_users = $data['response'];

                //  From the list of users we will group them by their directory_type e.g) customer, supplier, e.t.c
                //  After this we will map through each group (customer, supplier, e.t.c) and get the status name, total sum of
                //  the grand totals as well as the total count of grouped users of that activity.
                /*
                 *  Example of returned output:
                 *
                    {
                        "Paid": {
                            "name": "Paid",
                            "grand_total": 44520,
                            "total_count": 5
                        },
                        "Sent": {
                            "name": "Sent",
                            "grand_total": 14000,
                            "total_count": 1
                        }
                    }
                 *
                 */

                $availableStats = collect($auth_users)->groupBy('directory_type')->map(function ($companyGroup, $key) {
                    return [
                        'name' => ucwords($key),                                //  e.g) Client, Supplier, Staff e.t.c
                        'total_count' => collect($companyGroup)->count(),       //  12
                    ];
                });

                //  This is a list of all the statistics we want returned in their respective order
                $expectedStats = ['Supplier', 'Client', 'Staff'];

                //  From the list of expected stats, we will map through and inspect if the expected stat
                //  exists in the available stats we have collected. If it does then return back the existing
                //  stat, otherwise we will create a new array that will hold the expected stat name that does
                //  not exist, as well as put a grand total sum of zero and a total count of zero
                /*
                 *  Example of returned output:
                 *
                    [
                        {
                            "name": "Draft",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Approved",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Sent",
                            "grand_total": 14000,
                            "total_count": 1
                        },
                        {
                            "name": "Cancelled",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Expired",
                            "grand_total": 0,
                            "total_count": 0
                        },
                        {
                            "name": "Paid",
                            "grand_total": 44520,
                            "total_count": 5
                        }
                    ]
                 *
                 */
                $stats = collect($expectedStats)->map(function ($stat_name) use ($availableStats) {
                    if (collect($availableStats)->has(strtolower($stat_name))) {
                        return $availableStats[strtolower($stat_name)];
                    } else {
                        return [
                                    'name' => $stat_name,         //  e.g) Supplier, Client, Staff
                                    'total_count' => 0,
                                ];
                    }
                });

                //  Calculate the overall stats e.g) Total Count
                $totalCount = ['name' => 'Total Individuals', 'total_count' => 0];

                foreach ($stats as $stat) {
                    $totalCount['total_count'] += $stat['total_count'];
                }

                //  Merge the overview stats, stats and base currency into one collection
                $data = [
                        'overview_stats' => [$totalCount],
                        'stats' => $stats,
                    ];

                //  Action was executed successfully
                return ['success' => true, 'response' => $data];
            } catch (\Exception $e) {
                //  Log the error
                $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }
        } else {
            return $response;
        }
    }

    /**
     * Generate a new passport token used for authentication
     * during API calls to retrieve or modify records.
     */
    public function generateToken($request)
    {
        $http = new \GuzzleHttp\Client();
        $response = $http->post(URL::to('/').'/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'customer_id' => '2',
                'customer_secret' => '8BMbdQUBaVMbspLnmUhE0XVjbv4QDlyGJyTsNW4s',
                'username' => $this->email,
                'password' => $request->input('password'),
                'scope' => '',
            ],
        ]);
        //  Lets get an array instead of a stdObject so that we can return without errors
        $response = json_decode($response->getBody(), true);

        return oq_api_notify([
                    'auth' => $response,                                        //  API ACCESS TOKEN
                    'user' => $this->load(['settings'])->toArray(),
                ], 201);
    }

    /*  initiateSendAccountActivationMail() method:
     *
     *  This is used to send the user an account verification email.
     *  The verificaton link will be used to ensure that this is a
     *  valid user who created an account. If an account is left
     *  unverified for a time, it will be subject for termination
     *  by permanent deleting.
     *
     */
    public function initiateSendAccountActivationMail($auth_userId = null)
    {
        //  Use the current user's model id otherwise use the provided id
        $auth_userId = $this->id ?? $auth_userId;

        //  Check if the account id was provided
        if (empty($auth_userId)) {
            //  If this is an API request
            if (oq_viaAPI($request)) {
                return ['success' => false, 'response' => oq_api_notify_error('Account could not be identified', 'no_user_id', 404)];
            }
        } else {
            //  Get the associated user
            $auth_user = User::find($auth_userId);

            //  If the user does not exist
            if ($auth_user) {
                //  Check if the user is already verified
                if ($auth_user->verified) {
                    return ['success' => true, 'response' => $auth_user];
                }

                //  Delete any possible existing tokens
                VerifyUser::where('user_id', $auth_user->id)->delete();

                //  Create a new account verification token
                $verification = VerifyUser::create([
                    'user_id' => $auth_user->id,
                    'token' => sha1(time()),
                ]);

                //  Send verification email
                Mail::to($auth_user->email)->send(new ActivateAccount($auth_user));

                //  If verification email sent successfully
                if (!Mail::failures()) {
                    //  Update the status
                    $status = 'veriication mail sent';
                    $verificationMailSentActivity = oq_saveActivity($auth_user, $auth_user, $status, null);

                    return ['success' => true, 'response' => $auth_user];

                //  If verification email sending failed
                } else {
                    //  Update the status
                    $status = 'veriication mail failed';
                    $verificationMailFailedActivity = oq_saveActivity($auth_user, $auth_user, $status, null);

                    return ['success' => false, 'response' => oq_api_notify_error('Something went wrong trying to send the verification email. Please try again', 'failed_sending_email', 404)];
                }
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_error('Account does not exist', 'no_account_found', 404)];
            }
        }
    }

    public function initiateSendForgotPasswordEmail()
    {
        //  Use the current user's model id otherwise use the provided id
        $user_email = request('email');

        //  Get the redirect route for when the user clicks the 
        $redirectTo = request('redirectTo') ?? '';

        //  Check if the account id was provided
        if (empty($user_email)) {
            //  If this is an API request
            if (oq_viaAPI($request)) {
                return ['success' => false, 'response' => oq_api_notify_error('Provide your email', 'no_email', 404)];
            }
        } else {
            //  Get the associated user
            $user = User::where('email', $user_email)->whereNotNull('password')->first();

            //  If the user does not exist
            if ( !is_null($user) ) {

                //  Delete any possible past existing tokens
                DB::table('password_resets')->where('email', $user_email)->delete();

                //  Create a new password reset token
                $verification = PasswordResetTokens::create([
                                    'email' => $user_email,
                                    'token' => sha1(time()),
                                ]);

                //  Send reset password email
                Mail::to($user->email)->send(new ResetPassword($user, $redirectTo));

                //  If reset email sent successfully
                if (!Mail::failures()) {
                    //  Update the status
                    $status = 'password reset mail sent';
                    $verificationMailSentActivity = oq_saveActivity($user, $user, $status, null);

                    return ['success' => true, 'response' => $user];

                //  If verification email sending failed
                } else {
                    //  Update the status
                    $status = 'password reset mail failed';
                    $passwordResetEmailSentActivity = oq_saveActivity($user, $user, $status, null);

                    return ['success' => false, 'response' => oq_api_notify_error('Something went wrong trying to send the password reset email. Please try again', 'failed_sending_password_reset_email', 404)];
                }
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_error('Account does not exist', 'no_account_found', 404)];
            }
        }
    }

    public function initiateResetPassword()
    {
        $token = request('token') ?? null;
        $email = request('email') ?? null;
        $password = request('password') ?? null;

        //  Check if the mail token exists
        if (empty($token)) {
            //  Token not provided
            return ['success' => false, 'response' => oq_api_notify_error(null, ['password' => ['Reset token not provided. Try sending a new pasword reset email']], 404)];
        }else if (empty($email)) {
            //  Email not provided
            return ['success' => false, 'response' => oq_api_notify_error(null, ['password' => ['Account email was not provided']], 404)];
        }else if (empty($password)) {
            //  Password not provided
            return ['success' => false, 'response' => oq_api_notify_error(null, ['password' => ['Password not provided']], 404)];
        }

        $resetToken = PasswordResetTokens::where('token', $token)->where('email', $email)->first();

        //  Check if the token exists
        if ( !is_null( $resetToken ) ) {

            //  Get the associated user
            $user = User::where('email', $email)->whereNotNull('password')->first();

            if ( !is_null( $user ) ) {

                //  Verify the account
                $user->verified = 1;

                //  Change the password
                $user->password = Hash::make($password);

                //  Save changes
                $user->save();

                //  Update the status
                $status = 'password reset';
                $passwordSavedActivity = oq_saveActivity($user, $user, $status, null);

                return ['success' => true, 'response' => $user];

            }else{
                //  User does not exist
                return ['success' => false, 'response' => oq_api_notify_error(null, ['password' => ['Account using email "'.$email.'" could not be found.']], 404)];
            }

        } else {
            //  Invalid token provided
            return ['success' => false, 'response' => oq_api_notify_error(null, ['password' => ['Invalid token. Token might have expired or been used']], 404)];
        }

    }

    public function getBasicDetails()
    {
        //  Filter the collection to only the following details
        return $this->only([

                /*  Profile Image  */
                'profileImage',

                /*  Basic Info  */
                'first_name', 'last_name', 'gender', 'date_of_birth', 'bio', 
                
                /*  Address Info  */
                'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 
                
                /*  Contact Info  */
                'email', 'additional_email', 'phones'
            ]);
    }

}
