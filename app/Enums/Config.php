<?php

namespace App\Enums;

class Config
{
    public const DEFAULT_PASSWORD = 'default_password';
    public const PAGE_SIZE = 'page_size';
    public const APP_NAME = 'app_name';
    public const INSTITUTION_NAME = 'institution_name';
    public const INSTITUTION_ADDRESS = 'institution_address';
    public const INSTITUTION_PHONE = 'institution_phone';
    public const INSTITUTION_EMAIL = 'institution_email';
    public const LANGUAGE = 'language';
    public const PIC = 'pic';

    public static function value($code): string
    {
        return match ($code) {
            self::DEFAULT_PASSWORD => 'default_password',
            self::PAGE_SIZE => 'page_size',
            self::APP_NAME => 'app_name',
            self::INSTITUTION_NAME => 'institution_name',
            self::INSTITUTION_ADDRESS => 'institution_address',
            self::INSTITUTION_PHONE => 'institution_phone',
            self::INSTITUTION_EMAIL => 'institution_email',
            self::LANGUAGE => 'language',
            self::PIC => 'pic',
            default => $code,
        };
    }
}
