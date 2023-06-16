<?php declare(strict_types=1);

namespace App\Domain\References;

class Date
{
    final public const MINUTE = 60;
    final public const HOUR = 3600;
    final public const DAY = 86400;
    final public const WEEK = 604800;
    final public const MONTH = 2592000;
    final public const YEAR = 31536000;

    final public const NULL_DATETIME = '0000-00-00 00:00:00';
    final public const NULL_DATE = '0000-00-00';
    final public const NULL_TIME = '00:00:00';
    final public const DATE = 'Y-m-d';
    final public const DATETIME = 'Y-m-d H:i:s';
    final public const DATE_RUS = 'd.m.Y';
    final public const DATETIME_RUS = 'd.m.Y H:i';
    final public const TIME = 'H:i:s';
}
