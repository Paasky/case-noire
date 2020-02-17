<?php

namespace App\Constants;

class AgentConst
{
    const TYPE_AGENT = 'agent';
    const TYPE_ADMIN = 'admin';
    const TYPE_OWNER = 'owner';
    const TYPES = [
        self::TYPE_AGENT,
        self::TYPE_ADMIN,
        self::TYPE_OWNER,
    ];
}