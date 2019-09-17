<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\SettingTraits;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
    'store' => 'App\Store',
]);

class Setting extends Model
{
    use Dataviewer;
    use SettingTraits;

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'details', 'owner_id', 'owner_type',
    ];

    /**
     *  Get the owner from the morphTo relationship.
     *  Settings can be assigned to multiple types of
     *  owning resources e.g users, companies, stores,
     *  e.t.c
     */
    public function owner()
    {
        return $this->morphTo();
    }
}
