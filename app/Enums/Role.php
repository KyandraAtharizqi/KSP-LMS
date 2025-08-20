<?php

namespace App\Enums;

class Role
{
    public const ADMIN = 'admin';
    public const STAFF = 'staff';
    public const DIVISION_ADMIN = 'division_admin';
    public const DEPARTMENT_ADMIN = 'department_admin';
    public const UPPER_STAFF = 'upper_staff';

    public static function label(string $role): string
    {
        return match($role) {
            self::ADMIN => 'Admin',
            self::STAFF => 'Staff',
            self::DIVISION_ADMIN => 'Division Admin',
            self::DEPARTMENT_ADMIN => 'Department Admin',
            self::UPPER_STAFF => 'Upper Staff',
            default => $role,
        };
    }

    public static function cases(): array
    {
        return [
            self::ADMIN,
            self::STAFF,
            self::DIVISION_ADMIN,
            self::DEPARTMENT_ADMIN,
            self::UPPER_STAFF,
        ];
    }
}
