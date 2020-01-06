<?php

namespace App;

use App\Traits\UssdSessionTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store'
]);

class UssdSession extends Model
{
    use UssdSessionTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Session Details  */
        'session_id', 'service_code', 'phone_number', 'status', 'text',

        /*  Meta Data  */
        'metadata',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    /*
     *  Returns the owner of the ussd session
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* ATTRIBUTES */

    protected $appends = [
        'status', 'resource_type',
    ];

    public function getStatusAttribute($status)
    {
        switch ($status) {
            case '0':
                $status_name = 'Incomplete';
                $status_description = 'The session was not completed';
                break;
            case '1':
                $status_name = 'Completed';
                $status_description = 'The session was completed successfully';
                break;
            case '2':
                $status_name = 'Failed';
                $status_description = 'The session failed. A problem was encountered';
                break;
            default:
                $status_name = 'Unknown';
                $status_description = 'Status is unknown';
        }

        return [
            'name' => $status_name,
            'description' => $status_description,
        ];
    }

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }
}