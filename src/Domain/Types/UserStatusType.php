<?php declare(strict_types=1);

namespace App\Domain\Types;

use App\Domain\AbstractEnumType;

class UserStatusType extends AbstractEnumType
{
    final public const NAME = 'UserStatusType';

    final public const STATUS_WORK = 'work';
    final public const STATUS_DELETE = 'delete';
    final public const STATUS_BLOCK = 'block';

    final public const LIST = [
        self::STATUS_WORK,
        self::STATUS_BLOCK,
        self::STATUS_DELETE,
    ];
}
