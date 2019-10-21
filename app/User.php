<?php

namespace App;

use App\Traits\UserTraits;
use Laravel\Passport\HasApiTokens;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Dataviewer, UserTraits;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_of_birth',
    ];

    protected $with = ['phones'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'first_name', 'last_name', 'gender', 'date_of_birth', 'bio', 
        
        /*  Address Info  */
        'email', 'additional_email',  'username', 'password', 'verified', 'setup', 'account_type',
        
        /*  Social Info  */
        'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link',

        /*  Company Info  */
        'company_id'

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
     *  Returns the users settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /*
     *  Returns all the companies the user has been allocated to. This includes allocations
     *  of the user as admin, staff, customer, vendor e.t.c. Any allocation will pass as a 
     *  valid company to retrieve on this instance. We can then filter our results to be
     *  more specific (using a scope) e.g) Get all companies where the user is an admin. 
     */
    public function companies()
    {
        return $this->morphedByMany('App\Company', 'owner', 'user_allocations');
    }

    /* 
     *  Scope the users by type
     */
    public function scopeWhereUserType($query, $type)
    {
        //  If multiple type provided
        if( is_array($type) ){

            return $query->whereIn('user_allocations.type', $type);

        //  If single type provided
        }else{

            return $query->where('user_allocations.type', $type);
        }

        //  Otherwise return query as is
        return $query;
    }

    /* 
     *  Returns companies where the user is an admin
     */
    public function companiesWhereUserIsAdmin()
    {
        return $this->companies()->whereUserType('admin');
    }

    /* 
     *  Returns companies where the user is a staff member
     */
    public function companiesWhereUserIsStaff()
    {
        return $this->companies()->whereUserType('staff');
    }

    /* 
     *  Returns companies where the user is a customer
     */
    public function companiesWhereUserIsCustomer()
    {
        return $this->companies()->whereUserType('customer');
    }

    /* 
     *  Returns companies where the user is a vendor
     */
    public function companiesWhereUserIsVendor()
    {
        return $this->companies()->whereUserType('vendor');
    }

    /*
     *  Returns all the stores the user has been allocated to. This includes allocations
     *  of the user as admin, staff, customer, vendor e.t.c. Any allocation will pass as a 
     *  valid store to retrieve on this instance. We can then filter our results to be
     *  more specific (using a scope) e.g) Get all stores where the user is an admin. 
     */
    public function stores()
    {
        return $this->morphedByMany('App\Store', 'owner', 'user_allocations');
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
        return ($this->account_type == 'superadmin');
    }

    /* 
     *  Returns recent activities owned by this user
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /*  Attributes */

    protected $appends = ['profile_image', 'full_name', 'address', 'resource_type'];

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
     *  Returns the users default address
     */
    public function getAddressAttribute()
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
}
