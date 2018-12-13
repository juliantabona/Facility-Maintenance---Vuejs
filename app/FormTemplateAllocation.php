<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
    'jobcard' => 'App\Jobcard',
]);
class FormTemplateAllocation extends Model
{
    protected $casts = [
        'template' => 'array',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_template_allocations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'template', 'step',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }
}
