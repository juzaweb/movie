<?php

namespace Juzaweb\Modules\Core\Tests\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
    case VERIFICATION = 'verification';
}
