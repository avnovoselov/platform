<?php declare(strict_types=1);

namespace App\Domain\Types\Catalog;

use App\Domain\AbstractEnumType;

class OrderStatusType extends AbstractEnumType
{
    final public const NAME = 'CatalogOrderStatusType';

    final public const STATUS_NEW = 'new';
    final public const STATUS_PROCESS = 'process';
    final public const STATUS_PAYMENT = 'payment';
    final public const STATUS_READY = 'ready';
    final public const STATUS_COMPLETE = 'complete';
    final public const STATUS_CANCEL = 'cancel';

    final public const LIST = [
        self::STATUS_NEW,
        self::STATUS_PROCESS,
        self::STATUS_PAYMENT,
        self::STATUS_READY,
        self::STATUS_COMPLETE,
        self::STATUS_CANCEL,
    ];
}
