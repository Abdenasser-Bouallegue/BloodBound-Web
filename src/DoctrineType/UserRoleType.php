<?php

namespace App\DoctrineType;
use App\DoctrineType\AbstractEnumType;
use App\Enum\UserRole;

class UserRoleType extends AbstractEnumType
{
    public const NAME = 'UserRole';

    public function getName(): string // the name of the type.
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string // the enums class to convert
    {
        return UserRole::class;
    }
}