<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\AdvancedFilter\Dataviewer;
use App\Traits\UserTraits;

class User extends Authenticatable
{
    use EntrustUserTrait { restore as private restoreA; }
    use HasApiTokens, Notifiable;
    use Dataviewer;
    use UserTraits;

    public function restore()
    {
        $this->restoreA();
    }

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_of_birth',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'country', 'provience', 'city',
        'postal_or_zipcode', 'email', 'additional_email', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link',
        'bio', 'username', 'password', 'verified', 'company_branch_id', 'company_id', 'position', 'accessibility',
    ];

    protected $allowedFilters = [
        'id', 'first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'country', 'provience', 'city',
        'postal_or_zipcode', 'email', 'additional_email', 'position', 'accessibility', 'created_at',
    ];

    protected $orderable = [
        'id', 'first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'country', 'provience', 'city',
        'postal_or_zipcode', 'email', 'additional_email', 'position', 'accessibility', 'created_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the verification token. This token is used when activating
     * the users account after successful registration.
     */
    public function verification()
    {
        return $this->hasOne('App\VerifyUser');
    }

    /**
     *   Get the users company branch. This is the branch that the user belongs to.
     *   A user must belong to a company branch to access more information for that
     *   specific branch. This can be jobcards, staff, suppliers, clients,
     *   quotations, invoices, receipts, documents, e.t.c related to the branch.
     */
    public function companyBranch()
    {
        return $this->belongsTo('App\CompanyBranch', 'company_branch_id');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function phones()
    {
        return $this->morphMany('App\Phone', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    /**
     *   Get the recent activities that belong to the user.
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    /**
     *   Incase the user does not have a profile image, use the default placeholder.
     */
    public function getAvatarAttribute($value)
    {
        //  If the avatar is not empty ('', NULL, false, e.t.c) then return the avatar url
        //  Otherwise return the default avatar placeholder
        return !empty($value) ? $value : '/images/assets/placeholders/profile_placeholder.png';
    }

    protected $appends = ['full_name', 'model_type'];

    //  Getter for calculating the deadline returned as array
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    //  Getter for the type of model
    public function getModelTypeAttribute()
    {
        return 'user';
    }
}
