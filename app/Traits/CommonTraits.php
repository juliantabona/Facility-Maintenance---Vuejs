<?php

namespace App\Traits;

trait CommonTraits
{
    /** Return the query start datetime provided in the request payload
     *  otherwise determine a customer datetime.
     */
    public function getQueryStartDatetime()
    {
        //  Get the start date provided by the request payload otherwise use todays datetime
        $start_time = request()->input('start_date') ?? (\Carbon\Carbon::now())->subMonth()->format('Y-m-d H:i:s');

        return new \Carbon\Carbon($start_time);
    }

    /** Return the query end datetime provided in the request payload
     *  otherwise determine a customer datetime.
     */
    public function getQueryEndDatetime()
    {
        //  Get the end date provided by the request payload otherwise use todays datetime but subtract one month
        $end_time = request()->input('end_date') ?? (\Carbon\Carbon::now())->format('Y-m-d H:i:s');

        return new \Carbon\Carbon($end_time);
    }

    /** Scope and return only records that exist between the query start datetime
     *  and the query end datetime.
     */
    public function scopeOnlyQueryWithinDateTime($query)
    {
        /** Only query for results where the created_at date is between the start date and end date
         *  The $this->getTable() method is used to get the current models table name so that we
         *  can target the created_at field of that exact table.
         */
        $query->whereBetween($this->getTable().'.created_at', [ $this->getQueryStartDatetime(), $this->getQueryEndDatetime()])->get();
    }

    public function getDatesBetweenStartAndEndDatetimeAttribute()
    {
        //  Get the dates between the query start and end datetime
        $datesBetween = \Carbon\CarbonPeriod::create($this->getQueryStartDatetime(), $this->getQueryEndDatetime())->toArray();

        return $datesBetween;
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific columns that can then be used for storage.
     *  We may only want to summarize the data to very important information, rather than storing everything along with
     *  useless information. In this instance we specify table columns that we want (we access the fillable columns of
     *  the model), while also removing any custom attributes we do not want to store (we access the appends columns
     *  of the model)
     */
    public function summarize($also_forget = [])
    {
        //  Collect and select table columns
        return collect($this->fillable)
                //  Remove all custom attributes
                ->forget($this->appends)
                //  Remove any specified key/value pairs to also remove
                ->forget($also_forget);
    }
}
