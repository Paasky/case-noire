<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BuildsQueries
{
    /**
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param array $values
     * @param array $columns
     * @param bool $allowNulls
     */
    public static function anyValueInAnyColumn(&$query, array $values, array $columns, bool $allowNulls = false): void
    {
        $query->where(function($where) use ($values, $columns, $allowNulls) {
            /** @var Builder $where */
            foreach ($columns as $column) {
                $where->orWhereIn($column, $values);

                if ($allowNulls) {
                    $where->orWhereNull($column);
                }
            }
        });
    }
}
