<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\SettingTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Dataviewer, CommonTraits, SettingTraits;

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Setting Details  */
        'details',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    /* 
     *  Returns the owner of the settings
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns recent activities owned by this settings
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
