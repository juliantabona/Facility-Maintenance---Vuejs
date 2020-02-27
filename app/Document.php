<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\DocumentTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use Dataviewer, CommonTraits, DocumentTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Document Details  */
        'name', 'type', 'mime', 'size', 'url', 
        
        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the document
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns recent activities owned by this document
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
