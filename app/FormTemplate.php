<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'form_template' => 'array',
    ];

    protected $table = 'form_templates';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_template', 'type', 'selected', 'deletable', 'company_id',
    ];

    public function formAllocations()
    {
        return $this->hasMany('App\FormTemplateAllocation');
    }
}
