<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\VerificationTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'phone' => 'App\Phone',
    'email' => 'App\Email',
]);

class Verification extends Model
{
    use Dataviewer, CommonTraits, VerificationTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Verification Details  */
        'token', 
        
        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the verification
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns recent activities owned by this verification
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'resource_type'
    ];

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }
    
}
