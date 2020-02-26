<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;

abstract class CaseNoireModel extends Model
{
    public static function table(): string
    {
        return (new static())->getTable();
    }

    public function nameForDebug(): string
    {
        $name = $this->name ?: '-no name-';
        return static::class . " $name [ID {$this->getKey()}]";
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder|Relation $query
     * @return string
     */
    public static function toRawSql($query): string
    {
        return array_reduce($query->getBindings(), function ($sql, $binding) {
            return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
        }, $query->toSql());
    }
}