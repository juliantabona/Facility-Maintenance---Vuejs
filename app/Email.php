<?php

namespace App;


use App\Traits\EmailTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'contact' => 'App\Contact',
]);

class Email extends Model
{
    use Dataviewer;
    use EmailTraits;
    
    protected $casts = [
        'default' => 'boolean', //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Email Details  */
        'email', 'default',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the email
     */
    public function owner()
    {
        return $this->morphTo();
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

    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }

}
