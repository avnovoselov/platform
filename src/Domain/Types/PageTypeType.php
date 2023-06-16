<?php declare(strict_types=1);

namespace App\Domain\Types;

use App\Domain\AbstractEnumType;

class PageTypeType extends AbstractEnumType
{
    final public const NAME = 'PageTypeType';

    final public const TYPE_HTML = 'html';
    final public const TYPE_TEXT = 'text';

    final public const LIST = [
        self::TYPE_HTML,
        self::TYPE_TEXT,
    ];
}
