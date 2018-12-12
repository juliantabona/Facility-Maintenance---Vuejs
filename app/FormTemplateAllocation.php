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

    /*
    public function getJobcardsAttribute()
    {
        if ($this->trackable_type == 'jobcard') {
            return $this->trackable;
        }

        return null;
    }

    public function jobcards()
    {
        return $this->where('trackable_type', 'jobcard')->morphTo();
    }
    */

    public function jobcards()
    {
        return $this->belongsTo(Jobcard::class, 'trackable_id')->where('form_template_allocations.trackable_type', 'jobcard');
    }

    /*
    public function product()
    {
        return $this->belongsTo(Product::class, 'favoritable_id')
            ->where('favorites.favoritable_type', Product::class);
    }
    */
}
