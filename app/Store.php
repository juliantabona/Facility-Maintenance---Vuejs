<?php

namespace App;

use DB;
use App\Traits\StoreTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'account' => 'App\Account',
]);

class Store extends Model
{
    use Dataviewer;
    use StoreTraits;

    /*  Custom variables
     *  The variables below are custom variables not related to Laravel
     *  but are still referenced by our application
     */

    protected $default_currency = 'BWP';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'currency' => 'array',
    ];

    protected $with = ['phones', 'emails', 'addresses'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'name', 'abbreviation', 'description', 'type', 'industry',

        /*  Account Info  */
        'setup',

        /*  Social Info  */
        'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link',

        /*  Currency Info  */
        'currency',

        /*  Stock Info  */
        'minimum_stock_quantity',

        /*  Ownership Info  */
        'owner_id', 'owner_type',
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /*
     *  Scope:
     *  Return stores that support USSD access (Accessible by 2G Devices via USSD)
     */
    public function scopeSupportUssd($query)
    {
        return $query->whereHas('ussdInterface', function (Builder $query) {
            $query->where('live_mode', 1);
        });
    }

    /*
     *  Scope:
     *  Return stores that don't support USSD access (Accessible by 2G Devices via USSD)
     */
    public function scopeDontSupportUssd($query)
    {
        return $query->whereHas('ussdInterface', function (Builder $query) {
            $query->where('live_mode', 0);
        });
    }

    /*
     *  Scope:
     *  Return stores in order of popularity. For now store popularity is
     *  determined by how many orders they receive
     */
    public function scopePopular($query)
    {
        return $query->withCount('orders')->orderByDesc('orders_count');
    }

    /*
     *  Scope:
     *  Return stores that match a given name or contain similar tags
     */
    public function scopeSearch($query, $name)
    {
        return $query->orWhere('name', $name)->orWhere('abbreviation', $name);
    }

    /*
     *  Returns the owner of the store. In this case it returns the
     *  Account that owns this store
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns documents associated with this store. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to
     *  this store is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'owner');
    }

    /*
     *  Returns documents categorized as files
     */
    public function files()
    {
        return $this->documents()->whereType('file');
    }

    /*
     *  Returns phones associated with this store. This includes all
     *  types of phones such as telephones, mobiles and fax numbers.
     *  We can then filter our results to be more specific (using a scope)
     *  e.g) Get only mobile phones
     */
    public function phones()
    {
        return $this->morphMany('App\Phone', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns phones categorized as mobile phones
     */
    public function mobiles()
    {
        return $this->phones()->whereType('mobile');
    }

    /*
     *  Returns phones categorized as telephones
     */
    public function telephones()
    {
        return $this->phones()->whereType('tel');
    }

    /*
     *  Returns phones categorized as fax numbers
     */
    public function fax()
    {
        return $this->phones()->whereType('fax');
    }

    /*
     *  Returns addresses associated with this store
     */
    public function addresses()
    {
        return $this->morphMany('App\Address', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns emails associated with this store
     */
    public function emails()
    {
        return $this->morphMany('App\Email', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns all the contacts that are associated with this store. We can filter our
     *  results to be more specific (using a scope) e.g) Get all contacts that are
     *  customers, vendors or that use a particular number or email.
     */
    public function contacts()
    {
        return $this->morphToMany('App\Contact', 'owner', 'contact_allocations')->withTimestamps()->orderBy('created_at', 'desc');
    }

    public function contactsWithMobilePhone($mobile = null)
    {
        return $this->contacts()->withMobilePhone($mobile);
    }

    public function customerContacts()
    {
        return $this->contacts()->where('is_customer', 1);
    }

    public function customerContactsWithMobilePhone($mobile = null)
    {
        return $this->customerContacts()->withMobilePhone($mobile);
    }

    public function vendorContacts()
    {
        return $this->contacts()->where('is_vendor', 1);
    }

    public function vendorContactsWithMobilePhone($mobile = null)
    {
        return $this->vendorContacts()->withMobilePhone($mobile);
    }

    /*
     *  Returns all the users that are associated with this store. This includes associations
     *  were the user as admin, staff, customer, vendor e.t.c. Any association to this store
     *  will pass as a valid user to retrieve on this relationship. We can then filter our
     *  results to be more specific (using a scope) e.g) Get all users where the user
     *  is an admin.
     */
    public function users()
    {
        return $this->morphToMany('App\User', 'owner', 'user_allocations')->withTimestamps();
    }

    /*
     *  Scope the users by type
     */
    public function scopeWhereUserType($query, $type)
    {
        //  If multiple type provided
        if (is_array($type)) {
            return $query->whereIn('user_allocations.type', $type);

        //  If single type provided
        } else {
            return $query->where('user_allocations.type', $type);
        }

        //  Otherwise return query as is
        return $query;
    }

    /*
     *  Scope the users by id
     */
    public function scopeWhereUserId($query, $id)
    {
        return $query->where('user_allocations.user_id', '=', $id);
    }

    /*
     *  Returns users where the user is an admin
     */
    public function admins()
    {
        return $this->users()->whereUserType('admin');
    }

    /*
     *  Returns users where the user is a staff member
     */
    public function staff()
    {
        return $this->users()->whereUserType('staff');
    }

    /*
     *  Checks if a given user is an admin to the store
     */
    public function isAdmin($user_id)
    {
        return ($this->admins()->whereUserId($user_id)->count()) ? true : false;
    }

    /*
     *  Checks if a given user is a staff member to the store
     */
    public function isStaff($user_id)
    {
        return ($this->staff()->whereUserId($user_id)->count()) ? true : false;
    }

    /*
     *  Checks if a given user is an admin or staff member to the store
     */
    public function isAdminOrStaff($user_id)
    {
        return $this->isAdmin($user_id) || $this->isStaff($user_id);
    }

    /*
     *  Returns the USSD Interface owned by this store
     */
    public function ussdInterface()
    {
        return $this->morphOne('App\UssdInterface', 'owner');
    }

    /*
     *  Returns products owned by this store. This includes both
     *  products available and not available for stores that
     *  support USSD accessibility. Basically this will
     *  return ALL the products linked to this store
     */
    public function products()
    {
        return $this->morphMany('App\Product', 'owner');
    }

    /*
     *  Returns products that are not variations of another product.
     *  Variations are different versions of this product such as
     *  when this product is available in different sizes, colors
     *  or materials, then it will have products with different
     *  variables.
     */
    public function notVariationProducts()
    {
        return $this->products()->isNotVariation();
    }

    /*
     *  Returns orders owned by this store
     */
    public function orders()
    {
        return $this->morphMany('App\Order', 'merchant')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns orders owned by this store that belong
     *  to a specific contact id
     */
    public function contactOrders($contact_id = null)
    {
        return $this->orders()->where(function ($query) use ($contact_id) {
            $query->orWhere('customer_id', $contact_id)
                  ->orWhere('reference_id', $contact_id);
        });
    }

    /*
     *  Returns taxes owned by this store
     */
    public function taxes()
    {
        return $this->morphMany('App\Tax', 'owner');
    }

    /*
     *  Returns discounts owned by this store
     */
    public function discounts()
    {
        return $this->morphMany('App\Discount', 'owner');
    }

    /*
     *  Returns coupons owned by this store
     */
    public function coupons()
    {
        return $this->morphMany('App\Coupon', 'owner');
    }

    /*
     *  Returns messages sent to this store
     */
    public function messages()
    {
        return $this->morphMany('App\Message', 'owner')->latest();
    }

    /*
     *  Returns reviews sent to this store
     */
    public function reviews()
    {
        return $this->morphMany('App\Review', 'owner')->latest();
    }

    /*
     *  Returns ussd sessions made to this store
     */
    public function ussd_sessions()
    {
        return $this->morphMany('App\UssdSession', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns the store settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /*************************************/
    /*  BILLING RELATED RELATIONSHIPS    */
    /*************************************/

    /*
     *  Returns invoices owned by this store
     */
    public function invoices()
    {
        return $this->morphMany('App\Invoice', 'owner');
    }

    /*
     *  Returns the transactions of invoices owned by this order
     */
    public function transactions()
    {
        /* Polymorphic hasManyThrough relationships are the same as any others, but
         *  with an added constraint on the owner_type, which can be retrieved from
         *  the Relation::morphMap() array, or by using the class name directly.
         *
         *  Refer to this stackoverflow quetion:
         *  https://stackoverflow.com/questions/43285779/laravel-polymorphic-relations-has-many-through
         *
         *  The array_search() function will search an array for a value and returns the key.
         *
         *  static::class = App\Order
         *
         *  Relation::morphMap() = [
         *      "order"  => "App\Order"
         *      "store"  => "App\Store"
         *      "user"   => App\User"
         *      "account => App\Account"
         *      "contact => App\Contact"
         *  ]
         *
         *  Therefore array_search(static::class, Relation::morphMap()) will return "store"
         */
        return $this->hasManyThrough(
                    'App\Transaction',                      //  What we want (transations)
                    'App\Order',                            //  The relationship we have (orders)
                    'merchant_id',                          //  Foreign key on orders table
                    'owner_id'                              //  Foreign key on transactions table
                )->where('orders.merchant_type', array_search(static::class, Relation::morphMap()) ?: static::class)
                //  Select all the transation details and make sure the owner id reflects the (invoice id) not the (order id)
                ->select(
                    'transactions.type', 'transactions.status', 'transactions.automatic', 'transactions.payment_type',
                    'transactions.payment_amount', 'transactions.meta', //'invoices.owner_id as order_id', 'invoices.id as invoice_id',
                    'transactions.created_at', 'transactions.updated_at'
                );
    }

    /*************************************/
    /*  MISCELLANEOUS RELATIONSHIPS      */
    /*************************************/

    /*
     *  Returns lifecycles owned by this store
     */
    public function availableLifecycles()
    {
        return $this->morphMany('App\Lifecycle', 'owner');
    }

    /*
     *  Returns lifecycles owned by this store for managing orders
     */
    public function orderLifecycles()
    {
        return $this->availableLifecycles()->where('type', 'order');
    }

    /*
     *  Returns categories owned by this store.
     *  Examples are "Electrical", "Mechanical", "Construction", "Renovation"
     *  Note: Categories can be used by multiple resources and are categorized
     *  using the type attribute to identify and distinguish the relevant resources.
     */
    public function availableCategories()
    {
        return $this->morphMany('App\Category', 'owner')->whereNull('parent_category_id');
    }

    /*
     *  Returns categories owned by this store for managing products
     */
    public function productCategories()
    {
        return $this->availableCategories()->where('type', 'product');
    }

    /*
     *  Returns recent activities owned by this store
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns store creation activity
     */
    public function createdActivities()
    {
        return $this->recentActivities()->whereType('created');
    }

    /*
     *  Returns store approval activity
     */
    public function approvedActivities()
    {
        return $this->recentActivities()->whereType('approved');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'logo',  'is_verified', 'is_email_verified', 'is_mobile_verified', 'customer_access_code',
        'team_access_code', 'phone_list', 'default_mobile', 'default_email', 'default_address',
        'average_rating', 'resource_type', 'phone_list', 'last_approved_activity', 'is_approved',
        'current_activity_status', 'activity_count',

        'statistics',
    ];

    /*
     *  Returns the store statistics
     */
    public function getStatisticsAttribute()
    {
        /*
        $intervals = $this->orders()->get()->groupBy(function($order) {
                        return \Carbon\Carbon::parse($order->created_at)->format('y-m-d h');
                    });
        */

        return [
            'orders' => [
                'general' => [
                    'name' => 'Orders',
                    'count' => $this->orders()->count(),
                ],
                'financial' => [
                    'total_gross_revenue' => [
                        'name' => 'Gross Revenue',
                        'amount' => $this->total_gross_revenue,
                    ],
                    'total_refunds' => [
                        'name' => 'Refunds',
                        'amount' => $this->total_refunds,
                    ],
                    'total_discounts' => [
                        'name' => 'Discounts',
                        'amount' => $this->total_discounts,
                    ],
                    'total_taxes' => [
                        'name' => 'Taxes',
                        'amount' => $this->total_taxes,
                    ],
                    'total_delivery' => [
                        'name' => 'Delivery',
                        'amount' => 0,
                    ],
                    'total_net_revenue' => [
                        'name' => 'Net Revenue',
                        'amount' => $this->total_net_revenue,
                    ],
                ],
                'orders_over_time' => $this->orders_over_time_stats
            ],
            'customers' => [
                'general' => [
                    'name' => 'Customers',
                    'count' => $this->customerContacts()->count(),
                ],
                'returning_customer_rate' => $this->returning_customer_rate_stats
            ],
            'transactions' => [
                'general' => [
                    'name' => 'Transactions',
                    'count' => null,
                ],
                'sale_transactions' => $this->sale_transaction_stats,
                'refund_transactions' => $this->refund_transaction_stats,
                'popular_payment_methods' => $this->popular_payment_method_stats
            ],
            'mobile_store' => $this->mobile_store_stats,
        ];
    }

    public function getReturningCustomerRateStatsAttribute()
    {
        $start_time = (\Carbon\Carbon::now())->subMonth()->format('Y-m-d H:i:s');
        $end_time = (\Carbon\Carbon::now())->format('Y-m-d H:i:s');

        $sessions = $this->ussd_sessions()->get();

        //  Count the number of new customer sessions
        $sessionCount = count($sessions);
        
        //  Get the dates between the start and end time
        $datesBetween = collect(\Carbon\CarbonPeriod::create($start_time, $end_time)->toArray())->map(function ($date, $key) {
            
            //  Foreach date return th datetime and set the count to zero (0)
            return [
                'date' => $date->toDateTimeString(),
                'count' => 0
            ];

        });

        //  Get the new cutomer sessions
        $newCustomerSessions = $sessions->filter(function ($session, $key) {

            //  Return the session if the metadata shows that the customer is a new customer
            return $session['metadata']['new_customer'] === true;

        }) ?? [];

        //  Count the number of new customer sessions
        $newCustomerSessionsCount = count($newCustomerSessions);

        //  Get the return customer sessions
        $returnCustomerSessions = $sessions->filter(function ($session, $key) {

            //  Return the session if the metadata shows that the customer is a return customer
            return $session['metadata']['new_customer'] !== true;

        }) ?? [];

        //  Count the number of return customer sessions
        $returnCustomerSessionsCount = count($returnCustomerSessions);

        /** New Customer Data Intervals
         * 
         *  Calculate the date and count intervals of new customer sessions
         *
         */
        $newCustomerDataIntervals = collect($newCustomerSessions)->groupBy(function ($sessions, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($sessions['created_at'], 0, 10);

        })->map(function ($group, $key) {
            
            //  Foreach session group return the datetime and total count of the sessions in that group
            return [

                //  Get the date of the first session in the group
                'date' => \Carbon\Carbon::parse( $group[0]['created_at'] )->toDateTimeString(),

                //  Count the total number of sessions
                'count' => count($group)

            ];

        //  Merge with the dates in between for a complete set of dates
        })->merge($datesBetween)

        //  Group by dates to sort the dates between with the session dates
        ->groupBy(function ($item, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($item['date'], 0, 10);

        })->map(function($dates){

            $collection = collect($dates);

            return [

                //  Get the smallest date e.g "2020-01-04 00:00:00" is smaller than "2020-01-04 01:00:00"
                'date' => $collection->min('date'),

                //  Calculate the sum of all the dates that have been grouped together
                'count' => $collection->sum('count')

            ];

        })->sortBy('date')->values()->all();

        /** Return Customer Data Intervals
         * 
         *  Calculate the date and count intervals of return customer sessions
         *
         */
        $returnCustomerDataIntervals = collect($returnCustomerSessions)->groupBy(function ($sessions, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($sessions['created_at'], 0, 10);

        })->map(function ($group, $key) {
            
            //  Foreach session group return the datetime and total count of the sessions in that group
            return [

                //  Get the date of the first session in the group
                'date' => \Carbon\Carbon::parse( $group[0]['created_at'] )->toDateTimeString(),

                //  Count the total number of sessions
                'count' => count($group)

            ];

        //  Merge with the dates in between for a complete set of dates
        })->merge($datesBetween)

        //  Group by dates to sort the dates between with the session dates
        ->groupBy(function ($item, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($item['date'], 0, 10);

        })->map(function($dates){

            $collection = collect($dates);

            return [

                //  Get the smallest date e.g "2020-01-04 00:00:00" is smaller than "2020-01-04 01:00:00"
                'date' => $collection->min('date'),

                //  Calculate the sum of all the dates that have been grouped together
                'count' => $collection->sum('count')

            ];

        })->sortBy('date')->values()->all();

        //  Return the data
        return [
            'name' => 'Returning Customer Rate',
            'total_sessions' => $sessionCount,
            'new_customer_data_intervals' => [
                'count' => $newCustomerSessionsCount,
                'rate' => $sessionCount ? round(($newCustomerSessionsCount / $sessionCount) * 100) : 0,
                'data_intervals' => $newCustomerDataIntervals
            ],
            'return_customer_data_intervals' => [
                'count' => $returnCustomerSessionsCount,
                'rate' => $sessionCount ? round(($returnCustomerSessionsCount / $sessionCount) * 100) : 0,
                'data_intervals' => $returnCustomerDataIntervals
            ]
        ];

    }

    public function getPopularPaymentMethodStatsAttribute()
    {
        $start_time = (\Carbon\Carbon::now())->subMonth()->format('Y-m-d H:i:s');
        $end_time = (\Carbon\Carbon::now())->format('Y-m-d H:i:s');

        $paymentMethods = $this->transactions()->successful()->payments()
                        //->where('created_at', '>=', ( \Carbon\Carbon::now() )->subDays(7))
                        ->groupBy('payment_type')
                        ->select(DB::raw('payment_type, count(*) as count'))
                        ->get();

        $paymentMethods = collect($paymentMethods)->map(function ($payment_method, $key) {
            //  Foreach date return only the payment method type and total count
            return [
                'payment_method' => $payment_method['payment_type'],
                'count' => $payment_method['count'],
            ];
        });

        //  Return the data
        return [
            'name' => 'Popular Payment Methods',
            'data' => $paymentMethods,
        ];
    }

    public function getOrdersOverTimeStatsAttribute()
    {
        $start_time = (\Carbon\Carbon::now())->subMonth()->format('Y-m-d H:i:s');
        $end_time = (\Carbon\Carbon::now())->format('Y-m-d H:i:s');

        $orders = $this->orders()->withPayments()
                        //->where('created_at', '>=', ( \Carbon\Carbon::now() )->subDays(7))
                        ->groupBy(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'))
                        ->select(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y %H:%i:%s") as date, count(*) as count, sum(grand_total) as total_amount'))
                        ->get();

        $intervals = collect($orders)->map(function ($order, $key) {
            return [
                //  Get the grouped order date value
                'date' => \Carbon\Carbon::parse($order['date'])->toDateTimeString(),

                //  Get the total amount of the grouped records
                'amount' => $order['total_amount'],

                //  Get the number of grouped orders
                'count' => $order['count'],
            ];
        });

        //  Get the dates between the start and end time
        $datesBetween = collect(\Carbon\CarbonPeriod::create($start_time, $end_time)->toArray())->map(function ($date, $key) {
            //  Foreach date return th datetime and set the count to zero (0)
            return [
                'date' => $date->toDateTimeString(),
                'total_amount' => 0,
                'average_amount' => 0,
                'count' => 0,
            ];
        });

        //  Merge the dates datesBetween with the current intervals and order by the date
        //    $updatedIntervals = $datesBetween->merge($intervals)->sortBy('date')->values()->all();

        $updatedIntervals = $datesBetween->merge($intervals)->groupBy(function ($item, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($item['date'], 0, 10);

        })->map(function($dates){

            $collection = collect($dates);

            //  Get the smallest date e.g "2020-01-04 00:00:00" is smaller than "2020-01-04 01:00:00"
            $date = $collection->min('date');

            //  Calculate the sum of all the dates that have been grouped together
            $count = $collection->sum('count');
                
            //  Calculate the sum of all the amounts that have been grouped together
            $total_amount = $collection->sum('amount');
                
            //  Calculate the sum of all the amounts that have been grouped together
            $average_amount = $total_amount / $count;

            return [

                'date' => $date,
                
                'total_amount' => $total_amount,

                'average_amount' => $average_amount,

                'count' => $count
                
            ];

        })->sortBy('date')->values()->all();

        //  Return the data
        return [
            'name' => 'Orders Over Time',
            'data_intervals' => $updatedIntervals,
        ];
    }

    public function getSaleTransactionStatsAttribute()
    {
        $start_time = (\Carbon\Carbon::now())->subMonth()->format('Y-m-d H:i:s');
        $end_time = (\Carbon\Carbon::now())->format('Y-m-d H:i:s');

        $records = $this->transactions()->successful()->payments()
                        //->where('created_at', '>=', ( \Carbon\Carbon::now() )->subDays(7))
                        ->groupBy(DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y")'))
                        ->select(DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as date, count(*) as count, sum(payment_amount) as total_amount'))
                        ->get();

        $intervals = collect($records)->map(function ($record, $key) {
            return [
                //  Get the grouped record date value
                'date' => \Carbon\Carbon::parse($record['date'])->toDateTimeString(),

                //  Get the total amount of the grouped records
                'amount' => $record['total_amount'],

                //  Get the number of grouped records
                'count' => $record['count'],
            ];
        });

        //  Get the dates between the start and end time
        $datesBetween = collect(\Carbon\CarbonPeriod::create($start_time, $end_time)->toArray())->map(function ($date, $key) {
            //  Foreach date return th datetime and set the count to zero (0)
            return [
                'date' => $date->toDateTimeString(),
                'amount' => 0,
                'count' => 0,
            ];
        });

        //  Merge the dates datesBetween with the current intervals and order by the date
        //    $updatedIntervals = $datesBetween->merge($intervals)->sortBy('date')->values()->all();

        $updatedIntervals = $datesBetween->merge($intervals)->groupBy(function ($item, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($item['date'], 0, 10);

        })->map(function($dates){

            $collection = collect($dates);

            return [

                //  Get the smallest date e.g "2020-01-04 00:00:00" is smaller than "2020-01-04 01:00:00"
                'date' => $collection->min('date'),
                
                //  Calculate the sum of all the amounts that have been grouped together
                'amount' => $collection->sum('amount'),

                //  Calculate the sum of all the dates that have been grouped together
                'count' => $collection->sum('count')

            ];

        })->sortBy('date')->values()->all();

        //  Return the data
        return [
            'name' => 'Sales',
            'data_intervals' => $updatedIntervals,
        ];
    }

    public function getRefundTransactionStatsAttribute()
    {
        $start_time = (\Carbon\Carbon::now())->subMonth()->format('Y-m-d H:i:s');
        $end_time = (\Carbon\Carbon::now())->format('Y-m-d H:i:s');

        $records = $this->transactions()->successful()->refunds()
                        //->where('created_at', '>=', ( \Carbon\Carbon::now() )->subDays(7))
                        ->groupBy(DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y")'))
                        ->select(DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as date, count(*) as count, sum(payment_amount) as total_amount'))
                        ->get();

        $intervals = collect($records)->map(function ($record, $key) {
            return [
                //  Get the grouped record date value
                'date' => \Carbon\Carbon::parse($record['date'])->toDateTimeString(),

                //  Get the total amount of the grouped records
                'amount' => $record['total_amount'],

                //  Get the number of grouped records
                'count' => $record['count'],
            ];
        });

        //  Get the dates between the start and end time
        $datesBetween = collect(\Carbon\CarbonPeriod::create($start_time, $end_time)->toArray())->map(function ($date, $key) {
            //  Foreach date return th datetime and set the count to zero (0)
            return [
                'date' => $date->toDateTimeString(),
                'amount' => 0,
                'count' => 0,
            ];
        });

        //  Merge the dates datesBetween with the current intervals and order by the date
        //    $updatedIntervals = $datesBetween->merge($intervals)->sortBy('date')->values()->all();

        $updatedIntervals = $datesBetween->merge($intervals)->groupBy(function ($item, $key) {
            
            //  Group by Year - Month - Day e.g 2020-01-04
            return substr($item['date'], 0, 10);

        })->map(function($dates){

            $collection = collect($dates);

            return [

                //  Get the smallest date e.g "2020-01-04 00:00:00" is smaller than "2020-01-04 01:00:00"
                'date' => $collection->min('date'),
                
                //  Calculate the sum of all the amounts that have been grouped together
                'amount' => $collection->sum('amount'),

                //  Calculate the sum of all the dates that have been grouped together
                'count' => $collection->sum('count')

            ];

        })->sortBy('date')->values()->all();

        //  Return the data
        return [
            'name' => 'Refunds',
            'data_intervals' => $updatedIntervals,
        ];
    }

    /*
     *  Returns the mobile store statistics
     */
    public function getMobileStoreStatsAttribute()
    {
        //  Get all the store sessions
        $sessions = collect($this->ussd_sessions);

        //  Get the sessions where customers were shopping
        $shopping_sessions = $sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer started shopping
            return $session['metadata']['started_shopping'] === true;
        });

        //  Get the sessions where customers were viewing their past orders (My Orders)
        $viewing_my_orders_count_sessions = $sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer was viewing their past orders (My Orders)
            return $session['metadata']['viewed_my_orders'] === true;
        });

        //  Get the sessions where customers were viewing the store Contact Us
        $viewing_contact_us_count_sessions = $sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer was viewing the store Contact Us
            return $session['metadata']['viewed_contact_us'] === true;
        });

        //  Get the sessions where customers were viewing the store About Us
        $viewing_about_us_count_sessions = $sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer was viewing the store About Us
            return $session['metadata']['viewed_about_us'] === true;
        });

        //  Get the shopping sessions where customers selected a product
        $selected_product_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer had selected a product
            return $session['metadata']['selected_product'] === true;
        });

        //  Get the shopping sessions where customers selected only one product
        $selected_one_product_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer had selected only one product
            return $session['metadata']['selected_one_product'] === true;
        });

        //  Get the shopping sessions where customers selected more that one product
        $selected_more_products_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer had selected more than one product
            return $session['metadata']['selected_more_products'] === true;
        });

        //  Get the shopping sessions where customers selected a payment method
        $selected_payment_method_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer selected a payment method
            return $session['metadata']['selected_payment_method'] === true;
        });

        //  Get the shopping sessions where customers abandoned their cart
        $abandoned_cart_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer payment passed
            return $session['metadata']['payment_success'] === null;
        });

        //  Get the abandoned cart sessions where customers abandoned their cart before selecting a product
        $abandoned_cart_before_product_selection_sessions = $abandoned_cart_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer had abandoned their cart before selecting a product
            return $session['metadata']['selected_product'] === false;
        });

        //  Get the abandoned cart sessions where customers abandoned their cart before selecting a payment method
        $abandoned_cart_before_payment_method_selection_sessions = $abandoned_cart_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer had abandoned their cart before selecting a payment method
            return $session['metadata']['selected_product'] === true &&
                   $session['metadata']['selected_payment_method'] === false;
        });

        //  Get the abandoned cart sessions where customers abandoned their cart after selecting a payment method
        $abandoned_cart_after_payment_method_selection_sessions = $abandoned_cart_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer had abandoned their cart after selecting a payment method
            return $session['metadata']['selected_product'] === true &&
                   $session['metadata']['selected_payment_method'] === true;
        });

        //  Count the shopping sessions where customers paid successfully
        $payment_success_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer payment passed
            return $session['metadata']['payment_success'] === true;
        });

        //  Count the shopping sessions where customers paid unsuccessfully
        $payment_failed_sessions = $shopping_sessions->filter(function ($session, $key) {
            //  Return the session if the metadata shows that the customer payment failed
            return $session['metadata']['payment_success'] === false;
        });

        //  Count the total sessions
        $total_sessions_count = $sessions->count();

        //  Count the sessions where customers were shopping
        $started_shopping_count = $shopping_sessions->count();

        //  Count the sessions where customers were viewing their past orders (My Orders)
        $viewing_my_orders_count = $viewing_my_orders_count_sessions->count();

        //  Count the sessions where customers were viewing the store Contact Us
        $viewing_contact_us_count = $viewing_contact_us_count_sessions->count();

        //  Count the sessions where customers were viewing the store About Us
        $viewing_about_us_count = $viewing_about_us_count_sessions->count();

        //  Count the sessions where customers only visited without selecting any store options
        $only_visiting_count = $total_sessions_count - $started_shopping_count - $viewing_my_orders_count - $viewing_contact_us_count - $viewing_about_us_count;

        //  Count the shopping sessions where customers selected a product
        $selected_product_count = $selected_product_sessions->count();

        //  Count the shopping sessions where customers selected only one product
        $selected_one_product_count = $selected_one_product_sessions->count();

        //  Count the shopping sessions where customers selected more than one product
        $selected_more_products_count = $selected_more_products_sessions->count();

        //  Count the shopping sessions where customers selected a payment method
        $selected_payment_method_count = $selected_payment_method_sessions->count();

        //  Count the shopping sessions where customers abandoned their cart
        $abandoned_cart_count = $abandoned_cart_sessions->count();

        //  Count the abandoned cart sessions where customers abandoned their cart before selecting a product
        $abandoned_cart_before_product_selection_count = $abandoned_cart_before_product_selection_sessions->count();

        //  Count the abandoned cart sessions where customers abandoned their cart before selecting a payment method
        $abandoned_cart_before_payment_method_selection_count = $abandoned_cart_before_payment_method_selection_sessions->count();

        //  Count the abandoned cart sessions where customers abandoned their cart after selecting a payment method
        $abandoned_cart_after_payment_method_selection_count = $abandoned_cart_after_payment_method_selection_sessions->count();

        //  Count the successful payment sessions
        $payment_success_count = $payment_success_sessions->count();

        //  Count the failed payment sessions
        $payment_failed_count = $payment_failed_sessions->count();

        //  Get the popular payment methods used during shopping sessions
        $popular_payment_method_details = $shopping_sessions->groupBy('metadata.payment_method')->map(function ($session, $key) use ($selected_payment_method_count) {
            //  Count the number of sessions using the current payment method e.g Airtime = 3 or Mobile Money = 10
            $count = collect($session)->count();

            return [
                'count' => $count ?? 0,
                'percentage' => $selected_payment_method_count != 0 ? round((($count / $selected_payment_method_count) * 100)) : 0,
            ];
        })->filter(function ($session, $key) {
            //  Return only data with actual statistics
            return !empty($key);
        });

        //  Get the popular payment methods used during successful payments
        $popular_successful_payment_methods_details = $payment_success_sessions->groupBy('metadata.payment_method')->map(function ($session, $key) use ($payment_success_count) {
            //  Count the number of sessions using the current payment method e.g Airtime = 3 or Mobile Money = 10
            $count = collect($session)->count();

            return [
                'count' => $count ?? 0,
                'percentage' => $payment_success_count != 0 ? round((($count / $payment_success_count) * 100)) : 0,
            ];
        })->filter(function ($session, $key) {
            //  Return only data with actual statistics
            return !empty($key);
        });

        //  Get the popular payment methods used during unsuccessful payments
        $popular_failed_payment_method_details = $payment_failed_sessions->groupBy('metadata.payment_method')->map(function ($session, $key) use ($payment_failed_count) {
            //  Count the number of sessions using the current payment method e.g Airtime = 3 or Mobile Money = 10
            $count = collect($session)->count();

            return [
                'count' => $count ?? 0,
                'percentage' => $payment_failed_count != 0 ? round((($count / $payment_failed_count) * 100)) : 0,
            ];
        })->filter(function ($session, $key) {
            //  Return only data with actual statistics
            return !empty($key);
        });

        //  Find the average session time
        $session_times = $sessions->map(function ($session, $key) {
            //  Get the session start time as a timestamp
            $startTimestamp = (new \Carbon\Carbon($session['metadata']['start_datetime']) )->getTimestamp();

            //  Get the session end time as a timestamp
            $endTimestamp = (new \Carbon\Carbon($session['metadata']['end_datetime']) )->getTimestamp();

            //  Return the session time difference in seconds
            return $endTimestamp - $startTimestamp;
        });

        //  Find the average session time in seconds
        $average_session_time = $session_times->avg();

        //  Find the minimum session time in seconds
        $min_session_time = $session_times->min();

        //  Find the maximum session time in seconds
        $max_session_time = $session_times->max();

        return [
            'total_sessions' => [
                'name' => 'Store Visitations',
                'count' => $total_sessions_count ?? 0,
                'details' => [
                    'shopping' => [
                        'name' => 'Customers Shopping',
                        'count' => $started_shopping_count ?? 0,
                        'percentage' => $total_sessions_count != 0 ? round((($started_shopping_count / $total_sessions_count) * 100)) : 0,
                    ],

                    'viewing_my_orders' => [
                        'name' => 'Viewing My Orders',
                        'count' => $viewing_my_orders_count ?? 0,
                        'percentage' => $total_sessions_count != 0 ? round((($viewing_my_orders_count / $total_sessions_count) * 100)) : 0,
                    ],

                    'viewing_contact_us' => [
                        'name' => 'Viewing Contact Us',
                        'count' => $viewing_contact_us_count ?? 0,
                        'percentage' => $total_sessions_count != 0 ? round((($viewing_contact_us_count / $total_sessions_count) * 100)) : 0,
                    ],

                    'viewing_about_us' => [
                        'name' => 'Viewing About Us',
                        'count' => $viewing_about_us_count ?? 0,
                        'percentage' => $total_sessions_count != 0 ? round((($viewing_about_us_count / $total_sessions_count) * 100)) : 0,
                    ],

                    'only_visiting' => [
                        'name' => 'Only Visiting',
                        'count' => $only_visiting_count ?? 0,
                        'percentage' => $total_sessions_count != 0 ? round((($only_visiting_count / $total_sessions_count) * 100)) : 0,
                    ],
                ],
            ],

            'shopping' => [
                'name' => 'Customers Shopping',
                'count' => $started_shopping_count ?? 0,
            ],

            'selected_product' => [
                'name' => 'Selected Products/Services',
                'count' => $selected_product_count ?? 0,
                'percentage' => $started_shopping_count != 0 ? round((($selected_product_count / $started_shopping_count) * 100)) : 0,
                'details' => [
                    'selected_one_product' => [
                        'name' => 'Selected One Product/Service',
                        'count' => $selected_one_product_count ?? 0,
                        'percentage' => $selected_product_count != 0 ? round((($selected_one_product_count / $selected_product_count) * 100)) : 0,
                    ],

                    'selected_more_products' => [
                        'name' => 'Selected More Products/Services',
                        'count' => $selected_more_products_count,
                        'percentage' => $selected_product_count != 0 ? round((($selected_more_products_count / $selected_product_count) * 100)) : 0,
                    ],
                ],
            ],

            'abandoned_cart' => [
                'name' => 'Abandoned Cart',
                'count' => $abandoned_cart_count ?? 0,
                'percentage' => $started_shopping_count != 0 ? round((($abandoned_cart_count / $started_shopping_count) * 100)) : 0,
                'details' => [
                    'abandoned_cart_before_product_selection' => [
                        'name' => 'Abandoned Cart Before Product Selection',
                        'count' => $abandoned_cart_before_product_selection_count ?? 0,
                        'percentage' => $abandoned_cart_count != 0 ? round((($abandoned_cart_before_product_selection_count / $abandoned_cart_count) * 100)) : 0,
                    ],

                    'abandoned_cart_before_payment_method_selection' => [
                        'name' => 'Abandoned Cart Before Payment Method Selection',
                        'count' => $abandoned_cart_before_payment_method_selection_count ?? 0,
                        'percentage' => $abandoned_cart_count != 0 ? round((($abandoned_cart_before_payment_method_selection_count / $abandoned_cart_count) * 100)) : 0,
                    ],

                    'abandoned_cart_after_payment_method_selection' => [
                        'name' => 'Abandoned Cart After Payment Method Selection',
                        'count' => $abandoned_cart_after_payment_method_selection_count ?? 0,
                        'percentage' => $abandoned_cart_count != 0 ? round((($abandoned_cart_after_payment_method_selection_count / $abandoned_cart_count) * 100)) : 0,
                    ],
                ],
            ],

            'selected_payment_method' => [
                'name' => 'Selected Payment Method',
                'count' => $selected_payment_method_count ?? 0,
                'percentage' => $started_shopping_count != 0 ? round((($selected_payment_method_count / $started_shopping_count) * 100)) : 0,
                'details' => [
                    'popular_payment_methods' => $popular_payment_method_details,
                ],
            ],

            'payment_success' => [
                'name' => 'Successful Payments',
                'count' => $payment_success_count ?? 0,
                'percentage' => $started_shopping_count != 0 ? round((($payment_success_count / $started_shopping_count) * 100)) : 0,
                'details' => [
                    'popular_successful_payment_methods' => $popular_successful_payment_methods_details,
                ],
            ],

            'payment_failed' => [
                'name' => 'Failed Payments',
                'count' => $payment_failed_count ?? 0,
                'percentage' => $started_shopping_count != 0 ? round((($payment_failed_count / $started_shopping_count) * 100)) : 0,
                'details' => [
                    'popular_failed_payment_methods' => $popular_failed_payment_method_details,
                ],
            ],

            'session_time' => [
                //  Convert to minutes and seconds and round the average time to one decimal place
                'minimum_time' => [
                    'name' => 'Minimum Session Time',
                    'minutes' => round($min_session_time / 60),
                    'seconds' => $min_session_time % 60,
                ],

                //  Convert to minutes and seconds and round the average time to one decimal place
                'maximum_time' => [
                    'name' => 'Maximum Session Time',
                    'minutes' => round($max_session_time / 60),
                    'seconds' => $max_session_time % 60,
                ],

                //  Convert to minutes and seconds and round the average time to one decimal place
                'average_time' => [
                    'name' => 'Average Session Time',
                    'minutes' => round($average_session_time / 60),
                    'seconds' => $average_session_time % 60,
                ],
            ],
        ];
    }

    /*
     *  Returns the total gross revenue made by this store
     */
    public function getTotalGrossRevenueAttribute()
    {
        $total = 0;

        foreach ($this->orders()->withPayments()->get() as $order) {
            $total += $order->grand_total;
        }

        return $total;
    }

    /*
     *  Returns the total taxes made by this store
     */
    public function getTotalTaxesAttribute()
    {
        $total = 0;

        foreach ($this->orders()->withPayments()->get() as $order) {
            $total += $order->grand_tax_total;
        }

        return $total;
    }

    /*
     *  Returns the total discounts made by this store
     */
    public function getTotalDiscountsAttribute()
    {
        $total = 0;

        foreach ($this->orders()->withPayments()->get() as $order) {
            $total += $order->grand_discount_total;
        }

        return $total;
    }

    /*
     *  Returns the total discounts made by this store
     */
    public function getTotalNetRevenueAttribute()
    {
        $total = 0;

        foreach ($this->orders()->withPayments()->get() as $order) {
            //  Subtotal = Grand Total - Tax Total - Discount Total
            $total += $order->sub_total;
        }

        //  Remove Refunds Total
        $total = $total - $this->refund_total;

        return $total;
    }

    /*
     *  Returns the total payment made to this store
     */
    public function getTransactionTotalAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {
            $total += $order->transaction_total;
        }

        return $total;
    }

    /*
     *  Returns the total refund paid to this store
     */
    public function getTotalRefundsAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {
            $total += $order->refund_total;
        }

        return $total;
    }

    /*
     *  Returns the outstanding balance after all payments to orders
     */
    public function getOutstandingBalanceAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {
            $total += $order->outstanding_balance;
        }

        return $total;
    }

    /*
     *  Returns the store logo
     */
    public function getLogoAttribute()
    {
        return $this->documents()->where('type', 'logo')->first();
    }

    /*
     *  Returns true/false whether the store has a verified email
     */
    public function getIsEmailVerifiedAttribute()
    {
        $verified_emails_count = $this->emails()->verified()->count();

        return ($verified_emails_count) ? true : false;
    }

    /*
     *  Returns true/false whether the store has a verified mobile number
     */
    public function getIsMobileVerifiedAttribute()
    {
        $verified_mobiles_count = $this->mobiles()->verified()->count();

        return ($verified_mobiles_count) ? true : false;
    }

    /*
     *  Returns true/false whether the store is verified.
     *  A verified store must contain atleast one verified mobile number
     */
    public function getIsVerifiedAttribute()
    {
        return ($this->is_mobile_verified) ? true : false;
    }

    /*
     *  Returns the resource type
     */
    public function getCustomerAccessCodeAttribute()
    {
        return $this->ussdInterface->customer_access_code ?? null;
    }

    /*
     *  Returns the resource type
     */
    public function getTeamAccessCodeAttribute()
    {
        return $this->ussdInterface->team_access_code ?? null;
    }

    /*
     *  Returns the store phones separated with commas
     */
    public function getPhoneListAttribute()
    {
        $phoneList = '';
        $phones = $this->phones()->whereIn('type', ['mobile', 'tel'])->get();

        foreach ($phones as $key => $phone) {
            /*  Merge the calling code and phone number  */
            $phoneList .= ($key != 0 ? ', ' : '').'(+'.$phone['calling_code'].') '.$phone['number'];

            /*  If this is not the last item add "," otherwise nothing  */
            $phoneList .= (next($phones)) ? ', ' : '';
        }

        return $phoneList;
    }

    /*
     *  Returns the store default mobile phone
     */
    public function getDefaultMobileAttribute()
    {
        return $this->mobiles()->where('default', 1)->first();
    }

    /*
     *  Returns the store default email
     */
    public function getDefaultEmailAttribute()
    {
        return $this->emails()->where('default', 1)->first();
    }

    /*
     *  Returns the store default address
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('default', 1)->first();
    }

    /*
     *  Returns the store average rating
     */
    public function getAverageRatingAttribute()
    {
        //  Get the store reviews
        $reviews = $this->reviews ?? [];

        //  If we have any reviews
        if ($reviews) {
            //  Return the average of the ratings combined
            return collect($reviews)->avg('rating');
        }
    }

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    /*
     *  Returns the last approved activity
     */
    public function getLastApprovedActivityAttribute()
    {
        return $this->approvedActivities()->select('type', 'user_id', 'created_at')->first();
    }

    /*
     *  Returns true/false if the store was approved
     */
    public function getIsApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    /*
     *  Returns the status of the store
     */
    public function getCurrentActivityStatusAttribute()
    {
        return $this->is_approved ? 'Approved' : 'Draft';
    }

    /*
     *  Returns the total number of activities
     */
    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->select(DB::raw('count(*) as total'))->groupBy('owner_type')->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }
}
