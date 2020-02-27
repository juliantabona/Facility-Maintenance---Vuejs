<?php

namespace App;

use Illuminate\Support\Str;
use App\Traits\CommonTraits;
use App\AdvancedFilter\Dataviewer;
use App\Traits\RecentActivityTraits;
use Illuminate\Database\Eloquent\Model;

class RecentActivity extends Model
{
    use Dataviewer, CommonTraits, RecentActivityTraits;

    protected $table = 'recent_activities';

    protected $casts = [
        'activity' => 'array',
    ];

    protected $with = ['user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Activity Details  */
        'type', 'activity', 'user_id',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /*
     *  Returns the owner of the activity
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the user responsible for the activity
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'resource_type',
    ];

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(Str::snake(class_basename($this)));
    }
}
