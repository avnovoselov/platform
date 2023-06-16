<?php declare(strict_types=1);

namespace App\Domain\Types\Catalog;

use App\Domain\AbstractEnumType;

class ProductTypeType extends AbstractEnumType
{
    final public const NAME = 'CatalogProductTypeType';

    final public const TYPE_PRODUCT = 'product';
    final public const TYPE_SERVICE = 'service';

    final public const LIST = [
        self::TYPE_PRODUCT,
        self::TYPE_SERVICE,
    ];
}
