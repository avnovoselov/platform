<?php declare(strict_types=1);

namespace App\Domain\Types;

use App\Domain\AbstractEnumType;

class TaskStatusType extends AbstractEnumType
{
    final public const NAME = 'TaskStatusType';

    final public const STATUS_QUEUE = 'queue';
    final public const STATUS_WORK = 'work';
    final public const STATUS_DONE = 'done';
    final public const STATUS_FAIL = 'fail';
    final public const STATUS_CANCEL = 'cancel';
    final public const STATUS_DELETE = 'delete';

    final public const LIST = [
        self::STATUS_QUEUE,
        self::STATUS_WORK,
        self::STATUS_DONE,
        self::STATUS_FAIL,
        self::STATUS_CANCEL,
        self::STATUS_DELETE,
    ];
}
