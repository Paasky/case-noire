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
    const STATUS_CLOSED = 'closed';
    const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
    ];

    const CASE_OPEN_MAX_RANGE = 1000;
    const CASE_OPEN_MIN_RANGE = 500;
}