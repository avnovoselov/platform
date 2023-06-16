<?php declare(strict_types=1);

namespace App\Domain\References;

class Catalog
{
    final public const IMPORT_EXPORT_FIELDS_DEFAULT = [
        'uuid',
        'title',
        'vendorcode',
        'price',
        'country', 'manufacturer',
        'order',
    ];

    final public const IMPORT_FIELDS = [
        'uuid', 'external_id',
        'title', 'description', 'extra',
        'address', 'type',
        'barcode', 'vendorcode',
        'priceFirst', 'price', 'priceWholesale', 'priceWholesaleFrom', 'tax', 'special',
        'volume', 'unit', 'stock',
        'field1', 'field2', 'field3', 'field4', 'field5',
        'country', 'manufacturer',
        'order',
    ];

    final public const EXPORT_FIELDS = [
        'uuid', 'external_id',
        'title', 'description', 'extra',
        'address', 'type',
        'barcode', 'vendorcode',
        'priceFirst', 'price', 'priceWholesale', 'priceWholesaleFrom', 'tax', 'special',
        'volume', 'unit', 'stock',
        'field1', 'field2', 'field3', 'field4', 'field5',
        'country', 'manufacturer',
        'order', 'date',
    ];

    // possible order by
    final public const ORDER_BY_TITLE = 'title';
    final public const ORDER_BY_PRICE = 'price';
    final public const ORDER_BY_STOCK = 'stock';
    final public const ORDER_BY_DATE = 'date';
    final public const ORDER_BY_ORDER = 'order';

    // list of order by
    final public const ORDER_BY = [
        self::ORDER_BY_TITLE,
        self::ORDER_BY_PRICE,
        self::ORDER_BY_STOCK,
        self::ORDER_BY_DATE,
        self::ORDER_BY_ORDER,
    ];

    // possible order directions
    final public const ORDER_DIRECTION_DESC = 'DESC';
    final public const ORDER_DIRECTION_ASC = 'ASC';

    // list of order directions
    final public const ORDER_DIRECTION = [
        self::ORDER_DIRECTION_ASC,
        self::ORDER_DIRECTION_DESC,
    ];
}
