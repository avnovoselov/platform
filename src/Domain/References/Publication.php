<?php declare(strict_types=1);

namespace App\Domain\References;

class Publication
{
    // possible order by
    final public const ORDER_BY_TITLE = 'title';
    final public const ORDER_BY_DATE = 'date';

    // list of order by
    final public const ORDER_BY = [
        self::ORDER_BY_DATE,
        self::ORDER_BY_TITLE,
    ];

    // possible order directions
    final public const ORDER_DIRECTION_DESC = 'DESC';
    final public const ORDER_DIRECTION_ASC = 'ASC';

    // list of order directions
    final public const ORDER_DIRECTION = [
        self::ORDER_DIRECTION_DESC,
        self::ORDER_DIRECTION_ASC,
    ];
}
