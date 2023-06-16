<?php declare(strict_types=1);

namespace App\Domain\Types;

use App\Domain\AbstractEnumType;

class FileItemType extends AbstractEnumType
{
    final public const NAME = 'FileItemType';

    final public const ITEM_USER_UPLOAD = 'user_upload';
    final public const ITEM_PAGE = 'page';
    final public const ITEM_PUBLICATION = 'publication';
    final public const ITEM_CATALOG_CATEGORY = 'catalog_category';
    final public const ITEM_CATALOG_PRODUCT = 'catalog_product';
    final public const ITEM_FORM_DATA = 'form_data';
    final public const ITEM_THEME = 'theme';

    final public const LIST = [
        self::ITEM_USER_UPLOAD,
        self::ITEM_PAGE,
        self::ITEM_PUBLICATION,
        self::ITEM_FORM_DATA,
        self::ITEM_CATALOG_CATEGORY,
        self::ITEM_CATALOG_PRODUCT,
        self::ITEM_THEME,
    ];
}
