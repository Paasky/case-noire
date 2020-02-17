<?php

namespace App\Traits;

use DB;

trait ManagesDbTransactions
{
    public static function beginTransaction(): void
    {
        DB::connection('mysql')->beginTransaction();
    }
    public static function commitTransaction(): void
    {
        DB::connection('mysql')->commit();
    }
    public static function rollbackTransaction(): void
    {
        DB::connection('mysql')->rollBack();
    }
}
