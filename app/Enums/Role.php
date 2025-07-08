<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case DIVISION_ADMIN = "division_admin";
    case DEPARTMENT_ADMIN = 'department_admin';
    case UPPER_STAFF = 'upper_staff';

    public function status(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::STAFF => 'Staff',
            self::DIVISION_ADMIN => 'Divison Admin',
            self::DEPARTMENT_ADMIN => 'Department Admin',
            self::UPPER_STAFF => 'Upper Staff',
        };
    }
}
