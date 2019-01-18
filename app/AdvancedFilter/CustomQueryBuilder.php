<?php

namespace App\AdvancedFilter;

class CustomQueryBuilder
{
    public function apply($query, $data)
    {
        //  Check if we have a filter applied
        if (isset($data['f'])) {
            //  Foreach filter
            foreach ($data['f'] as $filter) {
                //  Check if the filter match (and/or) is set. If not then default to "and"
                $filter['match'] = isset($filter['filter_match']) ? $ $filter['filter_match'] : 'and';

                //  Lets filter the query
                $this->makeFilter($query, $filter);
            }
        }

        return $query;
    }

    protected function makeFilter($query, $filter)
    {
        // Determine if this is a column or a nested relation with a column e.g)
        //  E.g scenerio 1: name
        //  E.g scenerio 2: company.name

        if (strpos($filter['column'], '.') !== false) {
            // nested column

            //  Get both the relation and column
            //  Store the relation into $relation
            //  And the column into $filter['column']

            list($relation, $filter['column']) = explode('.', $filter['column']);

            //  Set the filter match to "and"
            $filter['match'] = 'and';

            //  If the filter column is equal to "count"
            if ($filter['column'] == 'count') {
                //  The camel_case function converts the given string to camelCase
                //  E.g) camel_case('foo_bar') will produce "fooBar"
                //  E.g) Expected Output: $this.equalTo($filter, $query, $relation)

                $this->{camel_case($filter['operator'])}($filter, $query, $relation);
            } else {
                $query->whereHas($relation, function ($q) use ($filter) {
                    $this->{camel_case($filter['operator'])}($filter, $q);
                });
            }
        } elseif (strpos($filter['column'], '>') !== false) {
            //  Support for JSON Based filter
            $filter['column'] = str_replace(' ', '', $filter['column']);
            $filter['column'] = str_replace('>', '->', $filter['column']);

            //  Perform filter
            $this->{camel_case($filter['operator'])}($filter, $query);
        } else {
            //  Perform filter
            $this->{camel_case($filter['operator'])}($filter, $query);
        }
    }

    public function equalTo($filter, $query)
    {
        return $query->where($filter['column'], $filter['query_1']);
    }

    public function notEqualTo($filter, $query)
    {
        return $query->where($filter['column'], '<>', $filter['query_1'], $filter['match']);
    }

    public function lessThan($filter, $query)
    {
        return $query->where($filter['column'], '<', $filter['query_1'], $filter['match']);
    }

    public function greaterThan($filter, $query)
    {
        return $query->where($filter['column'], '>', $filter['query_1'], $filter['match']);
    }

    public function between($filter, $query)
    {
        return $query->whereBetween($filter['column'], [
            $filter['query_1'], $filter['query_2'],
        ], $filter['match']);
    }

    public function notBetween($filter, $query)
    {
        return $query->whereNotBetween($filter['column'], [
            $filter['query_1'], $filter['query_2'],
        ], $filter['match']);
    }

    public function contains($filter, $query)
    {
        return $query->where($filter['column'], 'like', '%'.$filter['query_1'].'%', $filter['match']);
    }

    public function startsWith($filter, $query)
    {
        return $query->where($filter['column'], 'like', $filter['query_1'].'%', $filter['match']);
    }

    public function endsWith($filter, $query)
    {
        return $query->where($filter['column'], 'like', '%'.$filter['query_1'], $filter['match']);
    }

    public function inThePast($filter, $query)
    {
        $end = now()->endOfDay();

        $begin = now();

        switch ($filter['query_2']) {
            case 'hours':
                $begin->subHours($filter['query_1']);
                break;
            case 'days':
                $begin->subDays($filter['query_1'])->startOfDay();
                break;

            case 'months':
                $begin->subMonths($filter['query_1'])->startOfDay();
                break;

            case 'years':
                $begin->subYears($filter['query_1'])->startOfDay();
                break;

            default:
                $begin->subDays($filter['query_1'])->startOfDay();
                break;
        }

        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function inTheNext($filter, $query)
    {
        $begin = now()->startOfDay();

        $end = now();

        switch ($filter['query_2']) {
            case 'hours':
                $end->addHours($filter['query_1']);
                break;
            case 'days':
                $end->addDays($filter['query_1'])->endOfDay();
                break;

            case 'months':
                $end->addMonths($filter['query_1'])->endOfDay();
                break;

            case 'years':
                $end->addYears($filter['query_1'])->endOfDay();
                break;

            default:
                $end->addDays($filter['query_1'])->endOfDay();
                break;
        }

        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function inThePeroid($filter, $query)
    {
        $begin = now();
        $end = now();

        switch ($filter['query_1']) {
            case 'today':
                $begin->startOfDay();
                $end->endOfDay();
                break;
            case 'yesterday':
                $begin->subDay(1)->startOfDay();
                $end->subDay(1)->endOfDay();
                break;
            case 'tomorrow':
                $begin->addDay(1)->startOfDay();
                $end->addDay(1)->endOfDay();
                break;

            case 'last_month':
                $begin->subMonth(1)->startOfMonth();
                $end->subMonth(1)->endOfMonth();
                break;
            case 'this_month':
                $begin->startOfMonth();
                $end->endOfMonth();
                break;
            case 'next_month':
                $begin->addMonth(1)->startOfMonth();
                $end->addMonth(1)->endOfMonth();
                break;

            case 'last_year':
                $begin->subYear(1)->startOfYear();
                $end->subYear(1)->endOfYear();
                break;
            case 'this_year':
                $begin->startOfYear();
                $end->endOfYear();
                break;
            case 'next_year':
                $begin->addYear(1)->startOfYear();
                $end->addYear(1)->endOfYear();
                break;
            default:
                break;
        }

        return $query->whereBetween($filter['column'], [$begin, $end], $filter['match']);
    }

    public function equalToCount($filter, $query, $relation)
    {
        return $query->has($relation, '=', $filter['query_1']);
    }

    public function notEqualToCount($filter, $query, $relation)
    {
        return $query->has($relation, '<>', $filter['query_1']);
    }

    public function lessThanCount($filter, $query, $relation)
    {
        return $query->has($relation, '<', $filter['query_1']);
    }

    public function greaterThanCount($filter, $query, $relation)
    {
        return $query->has($relation, '>', $filter['query_1']);
    }
}
