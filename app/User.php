<?php

namespace App;

use App\Traits\UserTraits;
use App\Traits\CommonTraits;
use Laravel\Passport\HasApiTokens;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Dataviewer, CommonTraits, UserTraits;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_of_birth',
    ];

    protected $with = ['phones'];


    protected $casts = [
        
        'default' => 'boolean', //  Return the following 1/0 as true/false
        
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'first_name', 'last_name', 'gender', 'date_of_birth', 'bio',

        /*  Account Info  */
        'password', 'setup', 'account_type',

        /*  Social Info  */
        'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link'
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     *  Find the user instance for the given username.
     *  This is used by Passport to get the user by
     *  allowing our own custom logic to find the
     *  user then return the result.
     *
     *  @param  string  $username
     *
     *  @return \App\User
     */
    public function findForPassport($identity)
    {
        //  Get the users identity information. This identity can be
        //  the users email or mobile number. We can assume that the
        //  user provided either the email or the mobile number.
        $identity = [
            'email' => $identity,
            'mobile_number' => $identity,
        ];

        /*
         *  Search for any existing user account with the given identity
         *  email or mobile number
         */
        $user = (new \App\User())->findMatchingAccount($identity);

        //  Return the user found
        return $user;
    }

    /*
     * Get the verification token. This token is used when activating
     * the users account after successful registration.
     */
    public function verification()
    {
        return $this->hasOne('App\VerifyUser');
    }

    public function passwordResetTokens()
    {
        return $this->hasOne('App\PasswordResetTokens', 'email', 'email');
    }

    public function findMatchingAccount($identity = ['email' => null, 'mobile_number' => null])
    {
        /*  If we have an email or mobile number provided  */
        if (!empty($identity['email']) || !empty($identity['mobile_number'])) {
            $user = $this;

            /*  If we have an email provided  */
            if (!empty($identity['email'])) {
                /*  Get the provided email  */
                $email = $identity['email'];

                /*  Get the user that matches the given email  */
                $user = $user->whereHas('emails', function (Builder $query) use ($email) {
                    /*  Match the provided mobile number with verified emails of the users account  */
                    $query->where('email', $email)->verified();
                });
            }

            /*  If we have an mobile number provided  */
            if (!empty($identity['mobile_number'])) {
                /*  Get the provided mobile number  */
                $mobile_number = $identity['mobile_number'];

                /*  Get the user that matches the given mobile number  */
                $user = $user->orWhereHas('mobiles', function (Builder $query) use ($mobile_number) {
                    /*  Match the provided mobile number with verified mobile numbers of the users account  */
                    $query->where('number', $mobile_number)->verified();
                });
            }

            return $user->first() ?? null;
        }

        return null;
    }

    /*
     *  Returns documents associated with this user. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to
     *  this user is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'owner');
    }

    /*
     *  Scope the documents by type
     */
    public function scopeWhereDocumentType($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    /*
     *  Returns documents categorized as files
     */
    public function files()
    {
        return $this->documents()->whereDocumentType('file');
    }

    /*
     *  Returns phones associated with this user. This includes all
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
     *  Returns addresses associated with this user
     */
    public function addresses()
    {
        return $this->morphMany('App\Address', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns emails associated with this user
     */
    public function emails()
    {
        return $this->morphMany('App\Email', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns the users settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /*
     *  Returns all the accounts the user has been allocated to. This includes allocations
     *  of the user as admin, staff, customer, vendor e.t.c. Any allocation will pass as a
     *  valid account to retrieve on this instance. We can then filter our results to be
     *  more specific (using a scope) e.g) Get all accounts where the user is an admin.
     */
    public function accounts()
    {
        return $this->morphedByMany('App\Account', 'owner', 'user_allocations')->withTimestamps();
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
     *  Returns accounts where the user is an admin
     */
    public function accountsWhereUserIsAdmin()
    {
        return $this->accounts()->whereUserType('admin');
    }

    /*
     *  Returns accounts where the user is a staff member
     */
    public function accountsWhereUserIsStaff()
    {
        return $this->accounts()->whereUserType('staff');
    }

    /*
     *  Returns accounts where the user is a customer
     */
    public function accountsWhereUserIsCustomer()
    {
        return $this->accounts()->whereUserType('customer');
    }

    /*
     *  Returns accounts where the user is a vendor
     */
    public function accountsWhereUserIsVendor()
    {
        return $this->accounts()->whereUserType('vendor');
    }

    /*
     *  Returns all the stores the user has been allocated to. This includes allocations
     *  of the user as admin, staff, customer, vendor e.t.c. Any allocation will pass as a
     *  valid store to retrieve on this instance. We can then filter our results to be
     *  more specific (using a scope) e.g) Get all stores where the user is an admin.
     */
    public function stores()
    {
        return $this->morphedByMany('App\Store', 'owner', 'user_allocations')->withTimestamps();
    }

    /*
     *  Returns only stores that support USSD
     */
    public function ussdStores()
    {
        return $this->stores()->supportUssd();
    }

    /*
     *  Returns only stores that don't support USSD
     */
    public function nonUssdStores()
    {
        return $this->stores()->dontSupportUssd();
    }

    /*
     *  Returns stores where the user is an admin
     */
    public function storesWhereUserIsAdmin()
    {
        return $this->stores()->whereUserType('admin');
    }

    /*
     *  Returns stores where the user is a staff member
     */
    public function storesWhereUserIsStaff()
    {
        return $this->stores()->whereUserType('staff');
    }

    /*
     *  Returns stores where the user is a customer
     */
    public function storesWhereUserIsCustomer()
    {
        return $this->stores()->whereUserType('customer');
    }

    /*
     *  Returns stores where the user is a vendor
     */
    public function storesWhereUserIsVendor()
    {
        return $this->stores()->whereUserType('vendor');
    }

    /*
     *  Returns all the Ussd Services the user has been allocated to. Any allocation will 
     *  pass as a valid Ussd Services to retrieve on this instance.
     */
    public function ussdServices()
    {
        return $this->morphedByMany('App\UssdService', 'owner', 'user_allocations')->withTimestamps();
    }

    /*
     *  Checks if a given user id matches the current user model id. This check
     *  confirms if that user id provided owns the user model resource.
     */
    public function isAccountOwner($user_id)
    {
        return ($this->id == $user_id) ? true : false;
    }

    /*
     *  Checks if a given user is a Super Admin
     */
    public function isSuperAdmin()
    {
        return $this->account_type == 'superadmin';
    }

    /*
     *  Returns recent activities owned by this user
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /*  Attributes */

    protected $appends = [
        'is_verified', 'is_email_verified', 'is_mobile_verified', 'profile_image', 'full_name',
        'phone_list', 'default_account', 'default_mobile', 'default_email', 'default_address', 
        'resource_type'
    ];

    /*
     *  Returns true/false whether the user has a verified email
     */
    public function getIsEmailVerifiedAttribute()
    {
        $verified_emails_count = $this->emails()->verified()->count();

        return ($verified_emails_count) ? true : false;
    }

    /*
     *  Returns true/false whether the user has a verified mobile number
     */
    public function getIsMobileVerifiedAttribute()
    {
        $verified_mobiles_count = $this->mobiles()->verified()->count();

        return ($verified_mobiles_count) ? true : false;
    }

    /*
     *  Returns true/false whether the user is verified.
     *  A verified user must contain atleast one verified
     *  email or mobile number
     */
    public function getIsVerifiedAttribute()
    {
        return ($this->is_email_verified || $this->is_mobile_verified) ? true : false;
    }

    /*
     *  Returns the users profile picture
     */
    public function getProfileImageAttribute()
    {
        return $this->documents()->where('type', 'profile_image')->first();
    }

    /*
     *  Returns the users first and last name combined
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /*
     *  Returns the user phones separated with commas
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
     *  Returns the users default account
     */
    public function getDefaultAccountAttribute()
    {
        return $this->accounts()->where('user_allocations.default', 1)->first();
    }

    /*
     *  Returns the users default mobile phone
     */
    public function getDefaultMobileAttribute()
    {
        return $this->mobiles()->where('default', 1)->first();
    }

    /*
     *  Returns the users default email
     */
    public function getDefaultEmailAttribute()
    {
        return $this->emails()->where('default', 1)->first();
    }

    /*
     *  Returns the users default address
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('default', 1)->first();
    }

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }
    
    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }
}
