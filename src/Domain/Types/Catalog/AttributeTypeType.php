<?php declare(strict_types=1);

namespace App\Domain\Types\Catalog;

use App\Domain\AbstractEnumType;

class AttributeTypeType extends AbstractEnumType
{
    final public const NAME = 'CatalogAttributeTypeType';

    final public const TYPE_STRING = 'string';
    final public const TYPE_INTEGER = 'integer';
    final public const TYPE_FLOAT = 'float';
    final public const TYPE_BOOLEAN = 'boolean';

    final public const LIST = [
        self::TYPE_STRING,
        self::TYPE_INTEGER,
        self::TYPE_FLOAT,
        self::TYPE_BOOLEAN,
    ];
}
