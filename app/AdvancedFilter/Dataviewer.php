<?php

namespace App\AdvancedFilter;

use Illuminate\Validation\ValidationException;

trait Dataviewer
{
    public function scopeAdvancedFilter($query, $options = array())
    {
        //  Default settings
        $defaults = array(
            'order_join' => '',
            'paginate' => true,
          );

        //  Replace defaults with any provided options
        $config = array_merge($defaults, $options);

        //  e.g "company." added to "created_at" to create "company.created_at"
        $order_join = !empty($config['order_join']) ? $config['order_join'].'.' : '';

        $data = $this->process($query, request()->all())
            ->orderBy(
                //  e.g company.created_at
                $order_join.request('order_column', 'created_at'),

                request('order_direction', 'desc')
            );

        //  If to return paginated
        if ($config['paginate']) {
            return $data->paginate(request('limit', 10));
        }

        //  If to return only as relationship
        return $data;
    }

    public function process($query, $data)
    {
        $v = validator()->make($data, [
            'order_column' => 'sometimes|required|in:'.$this->orderableColumns(),
            'order_direction' => 'sometimes|required|in:asc,desc',

            'limit' => 'sometimes|required|integer|min:1',

            // advanced filter
            'filter_match' => 'sometimes|required|in:and,or',
            'f' => 'sometimes|required|array',
            'f.*.column' => 'required|in:'.$this->whiteListColumns(),
            'f.*.operator' => 'required_with:f.*.column|in:'.$this->allowedOperators(),
            'f.*.query_1' => 'required',
            'f.*.query_2' => 'required_if:f.*.operator,between,not_between',
        ]);

        if ($v->fails()) {
            // debug
            return dd($v->messages()->all());

            throw new ValidationException();
        }

        return (new CustomQueryBuilder())->apply($query, $data);
    }

    protected function whiteListColumns()
    {
        return implode(',', $this->allowedFilters);
    }

    protected function orderableColumns()
    {
        return implode(',', $this->allowedOrderableColumns);
    }

    protected function allowedOperators()
    {
        return implode(',', [
            'equal_to',
            'not_equal_to',
            'less_than',
            'greater_than',
            'between',
            'not_between',
            'contains',
            'starts_with',
            'ends_with',
            'in_the_past',
            'in_the_next',
            'in_the_peroid',
            'less_than_count',
            'greater_than_count',
            'equal_to_count',
            'not_equal_to_count',
        ]);
    }
}
