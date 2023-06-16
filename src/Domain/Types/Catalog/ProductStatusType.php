<?php declare(strict_types=1);

namespace App\Domain\Types\Catalog;

use App\Domain\AbstractEnumType;

class ProductStatusType extends AbstractEnumType
{
    final public const NAME = 'CatalogProductStatusType';

    final public const STATUS_WORK = 'work';
    final public const STATUS_DELETE = 'delete';

    final public const LIST = [
        self::STATUS_WORK,
        self::STATUS_DELETE,
    ];
}
