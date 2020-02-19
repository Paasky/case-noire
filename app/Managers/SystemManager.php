<?php

namespace App\Managers;

class SystemManager
{
    public static function setMemoryLimit(string $limit): void
    {
        ini_set('memory_limit', $limit);
    }
}