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
        
        'statistics'
    ];

    /*
     *  Returns the store statistics
     */
    public function getStatisticsAttribute()
    {
        return [
            'order' => [
                'totals' => [
                    'total_gross_revenue' => [
                        'name' => 'Gross Revenue',
                        'amount' => $this->total_gross_revenue
                    ],
                    'total_refunds' => [
                        'name' => 'Refunds',
                        'amount' => $this->total_refunds
                    ],
                    'total_discounts' => [
                        'name' => 'Discounts',
                        'amount' => $this->total_discounts
                    ],
                    'total_taxes' => [
                        'name' => 'Taxes',
                        'amount' => $this->total_taxes
                    ],
                    'total_delivery' => [
                        'name' => 'Delivery',
                        'amount' => 0
                    ],
                    'total_net_revenue' => [
                        'name' => 'Net Revenue',
                        'amount' => $this->total_net_revenue
                    ],
                ],
                'count' => $this->orders()->count()
            ],
            'customers' => [
                'count' => $this->customerContacts()->count()
            ],
            'mobile_store' => $this->mobile_store_session_stats,
        ];
    }

    /*
     *  Returns the total gross revenue made by this store
     */
    public function getTotalGrossRevenueAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {

            // If the order status is not any of the following statuses
            if( !collect(['Failed Payment', 'Cancelled', 'Pending Payment'])->contains($order->status['name']) ){

                $total += $order->grand_total;

            }

        }

        return $total;
    }
    
    /*
     *  Returns the total taxes made by this store
     */
    public function getTotalTaxesAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {

            // If the order status is not any of the following statuses
            if( !collect(['Failed Payment', 'Cancelled', 'Pending Payment'])->contains($order->status['name']) ){

                $total += $order->grand_tax_total;

            }

        }

        return $total;
    }
    
    /*
     *  Returns the total discounts made by this store
     */
    public function getTotalDiscountsAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {

            // If the order status is not any of the following statuses
            if( !collect(['Failed Payment', 'Cancelled', 'Pending Payment'])->contains($order->status['name']) ){

                $total += $order->grand_discount_total;

            }
            
        }

        return $total;
    }

    /*
     *  Returns the total discounts made by this store
     */
    public function getTotalNetRevenueAttribute()
    {
        $total = 0;

        foreach ($this->orders as $order) {

            // If the order status is not any of the following statuses
            if( !collect(['Failed Payment', 'Cancelled', 'Pending Payment'])->contains($order->status['name']) ){

                //  Subtotal = Grand Total - Tax Total - Discount Total
                $total += $order->sub_total;

            }

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
     *  Returns the total discounts made by this store
     */
    public function getMobileStoreSessionStatsAttribute()
    {
        $sessions = collect( $this->ussd_sessions );

        //  Find the average session time
        $session_times = $sessions->map(function ($session, $key) {
    
            $startTimestamp = (new \Carbon\Carbon( $session['metadata']['start_datetime'] ) )->getTimestamp();
            $endTimestamp = (new \Carbon\Carbon( $session['metadata']['end_datetime'] ) )->getTimestamp();

            //  Return the session time difference in seconds
            return $endTimestamp - $startTimestamp;

        });

        //  Find the average session time
        $average_session_time = $session_times->avg();

        //  Find the minimum session time
        $min_session_time = $session_times->min();

        //  Find the maximum session time
        $max_session_time = $session_times->max();

        //  Count the total sessions
        $total_sessions = $sessions->count();

        //  Get the sessions where customers were shopping
        $shopping_sessions = $sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer wanted to start shopping
            return $session['metadata']['started_shopping'] === true;

        });

        //  Get the sessions where customers were viewing their past orders (My Orders)
        $viewing_my_orders_sessions = $sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer was viewing their past orders (My Orders)
            return $session['metadata']['viewed_my_orders'] === true;

        });

        //  Get the sessions where customers were viewing the store Contact Us
        $viewing_contact_us_sessions = $sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer was viewing the store Contact Us
            return $session['metadata']['viewed_contact_us'] === true;

        });

        //  Get the sessions where customers were viewing the store About Us
        $viewing_about_us_sessions = $sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer was viewing the store About Us
            return $session['metadata']['viewed_about_us'] === true;

        });

        //  Count the sessions where customers were shopping
        $started_shopping = $shopping_sessions->count();

        //  Count the sessions where customers were viewing their past orders (My Orders)
        $viewing_my_orders = $viewing_my_orders_sessions->count();

        //  Count the sessions where customers were viewing the store Contact Us
        $viewing_contact_us = $viewing_contact_us_sessions->count();

        //  Count the sessions where customers were viewing the store About Us
        $viewing_about_us = $viewing_about_us_sessions->count();

        //  Count the sessions where customers only visited without selecting any store options
        $only_visiting = $total_sessions - $started_shopping - $viewing_my_orders - $viewing_contact_us - $viewing_about_us;
        
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
        
        //  Count the shopping sessions where customers selected a product
        $selected_product = $selected_product_sessions->count();

        //  Count the shopping sessions where customers selected only one product
        $selected_one_product = $selected_one_product_sessions->count();

        //  Count the shopping sessions where customers selected more that one product
        $selected_more_products = $shopping_sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer had selected more than one product
            return $session['metadata']['selected_more_products'] === true;

        })->count();

        //  Count the shopping sessions where customers selected a payment method
        $selected_payment_method = $shopping_sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer selected a payment method
            return $session['metadata']['selected_payment_method'] === true;

        })->count();


        //  Get the shopping sessions where customers abandoned their cart
        $abandoned_cart_sessions = $shopping_sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer payment passed
            return $session['metadata']['payment_success'] === null;

        });

        //  Count the shopping sessions where customers abandoned their cart
        $abandoned_cart = $abandoned_cart_sessions->count();

        //  Count the abandoned cart sessions where customers abandoned their cart before selecting a product
        $abandoned_cart_before_product_selection = $abandoned_cart_sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer had abandoned their cart before selecting a product
            return $session['metadata']['selected_product'] === false;

        })->count();

        //  Count the abandoned cart sessions where customers abandoned their cart before selecting a payment method
        $abandoned_cart_before_payment_method_selection = $abandoned_cart_sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer had abandoned their cart before selecting a payment method
            return $session['metadata']['selected_product'] === true &&
                   $session['metadata']['selected_payment_method'] === false;

        })->count();

        //  Count the abandoned cart sessions where customers abandoned their cart after selecting a payment method
        $abandoned_cart_after_payment_method_selection = $abandoned_cart_sessions->filter(function ($session, $key) {
    
            //  Return the session if the metadata shows that the customer had abandoned their cart after selecting a payment method
            return $session['metadata']['selected_product'] === true &&
                   $session['metadata']['selected_payment_method'] === true;

        })->count();

        //  Get the popular payment methods used during shopping sessions
        $popular_payment_methods = $shopping_sessions->groupBy('metadata.payment_method')->map(function ($session, $key) use ($started_shopping) {
            
            //  Count the number of sessions using the current payment method e.g Airtime = 3 or Mobile Money = 10
            $count = collect($session)->count();

            return [
                
                'count' => $count ?? 0,
                'percentage' => $started_shopping != 0 ? round((($count / $started_shopping) * 100)) : 0

            ];

        })->filter(function ($session, $key) {
    
            //  Return only data with actual statistics
            return !empty( $key );

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


        $payment_success = $payment_success_sessions->count();
        $payment_failed = $payment_failed_sessions->count();

        //  Get the popular payment methods used during successful payments
        $popular_successful_payment_methods = $payment_success_sessions->groupBy('metadata.payment_method')->map(function ($session, $key) use ($started_shopping) {
            
            //  Count the number of sessions using the current payment method e.g Airtime = 3 or Mobile Money = 10
            $count = collect($session)->count();

            return [
                
                'count' => $count ?? 0,
                'percentage' => $started_shopping != 0 ? round((($count / $started_shopping) * 100)) : 0

            ];

        })->filter(function ($session, $key) {
    
            //  Return only data with actual statistics
            return !empty( $key );

        });

        //  Get the popular payment methods used during unsuccessful payments
        $popular_failed_payment_methods = $payment_failed_sessions->groupBy('metadata.payment_method')->map(function ($session, $key) use ($started_shopping) {
            
            //  Count the number of sessions using the current payment method e.g Airtime = 3 or Mobile Money = 10
            $count = collect($session)->count();

            return [
                
                'count' => $count ?? 0,
                'percentage' => $started_shopping != 0 ? round((($count / $started_shopping) * 100)) : 0

            ];

        })->filter(function ($session, $key) {
    
            //  Return only data with actual statistics
            return !empty( $key );

        });

        return [

            'total_sessions' => [
                'name' => 'Store Visitations',
                'count' => $total_sessions ?? 0,
                'details' => [

                    'shopping' => [
                        'name' => 'Customers Shopping',
                        'count' => $started_shopping ?? 0,
                        'percentage' => $total_sessions != 0 ? round((($started_shopping / $total_sessions) * 100)) : 0
                    ],

                    'viewing_my_orders' => [
                        'name' => 'Viewing My Orders',
                        'count' => $viewing_my_orders ?? 0,
                        'percentage' => $total_sessions != 0 ? round((($viewing_my_orders / $total_sessions) * 100)) : 0
                    ],
                    
                    'viewing_contact_us' => [
                        'name' => 'Viewing Contact Us',
                        'count' => $viewing_contact_us ?? 0,
                        'percentage' => $total_sessions != 0 ? round((($viewing_contact_us / $total_sessions) * 100)) : 0
                    ],
                    
                    'viewing_about_us' => [
                        'name' => 'Viewing About Us',
                        'count' => $viewing_about_us ?? 0,
                        'percentage' => $total_sessions != 0 ? round((($viewing_about_us / $total_sessions) * 100)) : 0
                    ],
                    
                    'only_visiting' => [
                        'name' => 'Only Visiting',
                        'count' => $only_visiting ?? 0,
                        'percentage' => $total_sessions != 0 ? round((($only_visiting / $total_sessions) * 100)) : 0
                    ]

                ]
            ],

            'shopping' => [
                'name' => 'Customers Shopping',
                'count' => $started_shopping ?? 0
            ],

            'selected_product' => [
                'name' => 'Selected Products/Services',
                'count' => $selected_product ?? 0,
                'percentage' => $started_shopping != 0 ? round((($selected_product / $started_shopping) * 100)) : 0,
                'details' => [

                    'selected_one_product' => [
                        'name' => 'Selected One Product/Service',
                        'count' => $selected_one_product ?? 0,
                        'percentage' => $started_shopping != 0 ? round((($selected_one_product / $started_shopping) * 100)) : 0
                    ],
        
                    'selected_more_products' => [
                        'name' => 'Selected More Products/Services',
                        'count' => $selected_more_products,
                        'percentage' => $started_shopping != 0 ? round((($selected_more_products / $started_shopping) * 100)) : 0
                    ]

                ]
            ],
            
            'abandoned_cart' => [
                'name' => 'Abandoned Cart',
                'count' => $abandoned_cart ?? 0,
                'percentage' => $started_shopping != 0 ? round((($abandoned_cart / $started_shopping) * 100)) : 0,
                'details' => [

                    'abandoned_cart_before_product_selection' => [
                        'name' => 'Abandoned Cart Before Product Selection',
                        'count' => $abandoned_cart_before_product_selection ?? 0,
                        'percentage' => $started_shopping != 0 ? round((($abandoned_cart_before_product_selection / $started_shopping) * 100)) : 0
                    ],  
                    
                    'abandoned_cart_before_payment_method_selection' => [
                        'name' => 'Abandoned Cart Before Payment Method Selection',
                        'count' => $abandoned_cart_before_payment_method_selection ?? 0,
                        'percentage' => $started_shopping != 0 ? round((($abandoned_cart_before_payment_method_selection / $started_shopping) * 100)) : 0
                    ],  
                    
                    'abandoned_cart_after_payment_method_selection' => [
                        'name' => 'Abandoned Cart After Payment Method Selection',
                        'count' => $abandoned_cart_after_payment_method_selection ?? 0,
                        'percentage' => $started_shopping != 0 ? round((($abandoned_cart_after_payment_method_selection / $started_shopping) * 100)) : 0
                    ], 

                ]
            ],   

            'selected_payment_method' => [
                'name' => 'Selected Payment Method',
                'count' => $selected_payment_method ?? 0,
                'percentage' => $started_shopping != 0 ? round((($selected_payment_method / $started_shopping) * 100)) : 0,
                'details' => [

                    'popular_payment_methods' => $popular_payment_methods, 

                ]
            ],  

            'payment_success' => [
                'name' => 'Successful Payments',
                'count' => $payment_success ?? 0,
                'percentage' => $started_shopping != 0 ? round((($payment_success / $started_shopping) * 100)) : 0,
                'details' => [

                    'popular_successful_payment_methods' => $popular_successful_payment_methods, 

                ]
            ], 
            
            'payment_failed' => [
                'name' => 'Failed Payments',
                'count' => $payment_failed ?? 0,
                'percentage' => $started_shopping != 0 ? round((($payment_failed / $started_shopping) * 100)) : 0,
                'details' => [

                    'popular_failed_payment_methods' => $popular_failed_payment_methods, 

                ]
            ],

            'session_time' => [

                //  Convert to minutes and seconds and round the average time to one decimal place
                'minimum_time' => [
                    'name' => 'Minimum Session Time',
                    'minutes' => round($min_session_time / 60),
                    'seconds' => $min_session_time % 60
                ],

                //  Convert to minutes and seconds and round the average time to one decimal place
                'maximum_time' => [
                    'name' => 'Maximum Session Time',
                    'minutes' => round($max_session_time / 60),
                    'seconds' => $max_session_time % 60
                ],

                //  Convert to minutes and seconds and round the average time to one decimal place
                'average_time' => [
                    'name' => 'Average Session Time',
                    'minutes' => round($average_session_time / 60),
                    'seconds' => $average_session_time % 60
                ]

            ]
        ];
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
