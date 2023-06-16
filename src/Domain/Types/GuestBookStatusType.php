<?php declare(strict_types=1);

namespace App\Domain\Types;

use App\Domain\AbstractEnumType;

class GuestBookStatusType extends AbstractEnumType
{
    final public const NAME = 'GuestBookStatusType';

    final public const STATUS_WORK = 'work';
    final public const STATUS_MODERATE = 'moderate';

    final public const LIST = [
        self::STATUS_WORK,
        self::STATUS_MODERATE,
    ];
}
