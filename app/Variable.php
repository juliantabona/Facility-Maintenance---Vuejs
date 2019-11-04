<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Variable Details  */
        'name', 'value',

        /*  Ownership Information  */
        'product_id'

    ];

    /* 
     *  Returns the product that owns this variable
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    /* 
     *  Returns recent activities owned by this variable
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
