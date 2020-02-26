<?php

namespace App\Managers;

class ValidationManager
{
    public static function isUrl(string $url, bool $verify = false): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false ||
            count(explode('.', $url)) < 2
        ) {
            if ($verify) {
                throw new \InvalidArgumentException("url `$url` is not a valid url");
            }
            return false;
        }
        return true;
    }

}