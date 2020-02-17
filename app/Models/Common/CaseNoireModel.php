<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

abstract class CaseNoireModel extends Model
{
    public static function table(): string
    {
        return (new static())->getTable();
    }

    public function nameForDebug(): string
    {
        $name = $this->name ?? '';
        $name .= ($name ? ' ' : '') . "ID {$this->getKey()}";
        return $name;
    }
}