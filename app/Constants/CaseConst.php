<?php

namespace App\Constants;

class CaseConst
{
    const TYPE_LOST_ITEM = 'lost-item';
    const TYPE_ADULTERY = 'adultery';
    const TYPE_THEFT = 'theft';
    const TYPE_FRAUD = 'fraud';
    const TYPE_MISSING_PERSON = 'missing-person';
    const TYPE_MURDER = 'murder';
    const TYPE_ARSON = 'arson';
    const TYPES = [
        self::TYPE_LOST_ITEM,
        self::TYPE_ADULTERY,
        self::TYPE_THEFT,
        self::TYPE_FRAUD,
        self::TYPE_MISSING_PERSON,
        self::TYPE_MURDER,
        self::TYPE_ARSON,
    ];

    const STATUS_OPEN = 'open';
}