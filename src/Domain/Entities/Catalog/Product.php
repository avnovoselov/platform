<?php declare(strict_types=1);

namespace App\Domain\Entities\Catalog;

use App\Domain\AbstractEntity;
use App\Domain\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface as Uuid;

#[ORM\Table(name: 'catalog_product')]
#[ORM\Index(name: 'catalog_product_address_idx', columns: ['address'])]
#[ORM\Index(name: 'catalog_product_category_idx', columns: ['category'])]
#[ORM\Index(name: 'catalog_product_price_idx', columns: ['price', 'priceFirst', 'priceWholesale'])]
#[ORM\Index(name: 'catalog_product_volume_idx', columns: ['volume', 'unit'])]
#[ORM\Index(name: 'catalog_product_manufacturer_idx', columns: ['manufacturer'])]
#[ORM\Index(name: 'catalog_product_country_idx', columns: ['country'])]
#[ORM\Index(name: 'catalog_product_order_idx', columns: ['order'])]
#[ORM\UniqueConstraint(name: 'catalog_product_unique', columns: ['category', 'address', 'volume', 'unit', 'external_id'])]
#[ORM\Entity(repositoryClass: 'App\Domain\Repository\Catalog\ProductRepository')]
class Product extends AbstractEntity
{
    use FileTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidGenerator')]
    protected \Ramsey\Uuid\UuidInterface $uuid;

    public function getUuid(): \Ramsey\Uuid\UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @var string|Uuid
     */
    #[ORM\Column(type: 'uuid', options: ['default' => \Ramsey\Uuid\Uuid::NIL])]
    protected $category = \Ramsey\Uuid\Uuid::NIL;

    public function setCategory(mixed $uuid): self
    {
        $this->category = $this->getUuidByValue($uuid);

        return $this;
    }

    public function getCategory(): \Ramsey\Uuid\UuidInterface
    {
        return $this->category;
    }

    #[ORM\Column(type: 'string', length: 255, options: ['default' => ''])]
    protected string $title = '';

    public function setTitle(string $title): self
    {
        if ($this->checkStrLenMax($title, 255)) {
            $this->title = $title;
        }

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @see \App\Domain\Types\ProductTypeType::LIST
     */
    #[ORM\Column(type: 'CatalogProductTypeType', options: ['default' => \App\Domain\Types\Catalog\ProductTypeType::TYPE_PRODUCT])]
    protected string $type = \App\Domain\Types\Catalog\ProductTypeType::TYPE_PRODUCT;

    public function setType(string $type): self
    {
        if (in_array($type, \App\Domain\Types\Catalog\ProductTypeType::LIST, true)) {
            $this->type = $type;
        }

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    #[ORM\Column(type: 'text', length: 100000, options: ['default' => ''])]
    protected string $description = '';

    public function setDescription(string $description): self
    {
        if ($this->checkStrLenMax($description, 100000)) {
            $this->description = $description;
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    #[ORM\Column(type: 'text', length: 100000, options: ['default' => ''])]
    protected string $extra = '';

    public function setExtra(string $extra): self
    {
        if ($this->checkStrLenMax($extra, 100000)) {
            $this->extra = $extra;
        }

        return $this;
    }

    public function getExtra(): string
    {
        return $this->extra;
    }

    #[ORM\Column(type: 'string', length: 1000, options: ['default' => ''])]
    protected string $address = '';

    public function setAddress(string $address): self
    {
        if ($this->checkStrLenMax($address, 1000)) {
            $this->address = $this->getAddressByValue($address, str_replace('/', '-', $this->getTitle()));
        }

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    #[ORM\Column(type: 'text', length: 255, options: ['default' => ''])]
    protected string $vendorcode = '';

    public function setVendorCode(string $value): self
    {
        if ($this->checkStrLenMax($value, 255)) {
            $this->vendorcode = $value;
        }

        return $this;
    }

    public function getVendorCode(): string
    {
        return $this->vendorcode;
    }

    #[ORM\Column(type: 'text', length: 100, options: ['default' => ''])]
    protected string $barcode = '';

    public function setBarCode(string $value): self
    {
        if ($this->checkStrLenMax($value, 100)) {
            $this->barcode = $value;
        }

        return $this;
    }

    public function getBarCode(): string
    {
        return $this->barcode;
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $tax = 20.00;

    public function setTax(float $value): self
    {
        $this->tax = $value;

        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $priceFirst = .00;

    public function setPriceFirst(float $value): self
    {
        $this->priceFirst = $value;

        return $this;
    }

    public function getPriceFirst(): float
    {
        return $this->priceFirst;
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $price = .00;

    public function setPrice(float $value): self
    {
        $this->price = $value;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $priceWholesale = .00;

    public function setPriceWholesale(float $value): self
    {
        $this->priceWholesale = $value;

        return $this;
    }

    public function getPriceWholesale(): float
    {
        return $this->priceWholesale;
    }

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    protected float $priceWholesaleFrom = .00;

    public function setPriceWholesaleFrom(float $priceWholesaleFrom): self
    {
        $this->priceWholesaleFrom = $priceWholesaleFrom;

        return $this;
    }

    public function getPriceWholesaleFrom(): float
    {
        return $this->priceWholesaleFrom;
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $discount = .00;

    public function setDiscount(float $value): self
    {
        $this->discount = $value;

        return $this;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $special = false;

    public function setSpecial(mixed $value): self
    {
        $this->special = $this->getBooleanByValue($value);

        return $this;
    }

    public function getSpecial(): bool
    {
        return $this->special;
    }

    #[ORM\Column(type: 'json', options: ['default' => '{}'])]
    protected array $dimension = [
        'width' => 0.0,
        'height' => 0.0,
        'length' => 0.0,
    ];

    public function setDimension(array $data): self
    {
        $default = [
            'width' => 0.0,
            'height' => 0.0,
            'length' => 0.0,
        ];
        $data = array_merge($default, $data);

        $this->dimension = [
            'width' => floatval($data['width']),
            'height' => floatval($data['height']),
            'length' => floatval($data['length']),
        ];

        return $this;
    }

    public function getDimension(): array
    {
        return $this->dimension;
    }

    #[ORM\Column(type: 'float', scale: 3, precision: 10, options: ['default' => '1.0'])]
    protected float $volume = 0.000;

    public function setVolume(float $value): self
    {
        $this->volume = $value;

        return $this;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    #[ORM\Column(type: 'string', length: 64, options: ['default' => ''])]
    protected string $unit = '';

    public function setUnit(string $value): self
    {
        if ($this->checkStrLenMax($value, 64)) {
            $this->unit = $value;
        }

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getVolumeWithUnit(): string
    {
        return ($this->volume ?? .0) . ($this->unit ?: '');
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $stock = .00;

    public function setStock(float $value): self
    {
        $this->stock = $value;

        return $this;
    }

    public function getStock(): float
    {
        return $this->stock;
    }

    #[ORM\Column(type: 'text', length: 512, options: ['default' => ''])]
    protected string $field1 = '';

    public function setField1(string $value): self
    {
        if ($this->checkStrLenMax($value, 512)) {
            $this->field1 = $value;
        }

        return $this;
    }

    public function getField1(): string
    {
        return $this->field1;
    }

    #[ORM\Column(type: 'text', length: 512, options: ['default' => ''])]
    protected string $field2 = '';

    public function setField2(string $value): self
    {
        if ($this->checkStrLenMax($value, 512)) {
            $this->field2 = $value;
        }

        return $this;
    }

    public function getField2(): string
    {
        return $this->field2;
    }

    #[ORM\Column(type: 'text', length: 512, options: ['default' => ''])]
    protected string $field3 = '';

    public function setField3(string $value): self
    {
        if ($this->checkStrLenMax($value, 512)) {
            $this->field3 = $value;
        }

        return $this;
    }

    public function getField3(): string
    {
        return $this->field3;
    }

    #[ORM\Column(type: 'text', length: 512, options: ['default' => ''])]
    protected string $field4 = '';

    public function setField4(string $value): self
    {
        if ($this->checkStrLenMax($value, 512)) {
            $this->field4 = $value;
        }

        return $this;
    }

    public function getField4(): string
    {
        return $this->field4;
    }

    #[ORM\Column(type: 'text', length: 512, options: ['default' => ''])]
    protected string $field5 = '';

    public function setField5(string $value): self
    {
        if ($this->checkStrLenMax($value, 512)) {
            $this->field5 = $value;
        }

        return $this;
    }

    public function getField5(): string
    {
        return $this->field5;
    }

    /**
     * @var array
     */
    #[ORM\OneToMany(targetEntity: 'App\Domain\Entities\Catalog\ProductAttribute', mappedBy: 'product', orphanRemoval: true)]
    protected $attributes = [];

    public function hasAttributes(): int
    {
        return count($this->attributes);
    }

    /**
     * @param false $raw
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function getAttributes($raw = false)
    {
        return $raw ? $this->attributes : collect($this->attributes);
    }

    #[ORM\Column(type: 'string', length: 255, options: ['default' => ''])]
    protected string $country = '';

    public function setCountry(string $value): self
    {
        if ($this->checkStrLenMax($value, 255)) {
            $this->country = $value;
        }

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    #[ORM\Column(type: 'string', length: 255, options: ['default' => ''])]
    protected string $manufacturer = '';

    public function setManufacturer(string $value): self
    {
        if ($this->checkStrLenMax($value, 255)) {
            $this->manufacturer = $value;
        }

        return $this;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    #[ORM\Column(type: 'json', options: ['default' => '{}'])]
    protected array $tags = [];

    public function setTags(array|string $tags): self
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $this->tags = array_map('trim', $tags);

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @var array
     */
    #[ORM\OneToMany(targetEntity: 'App\Domain\Entities\Catalog\ProductRelation', mappedBy: 'product', orphanRemoval: true)]
    protected $relation = [];

    public function getRelations($raw = false)
    {
        return $raw ? $this->relation : collect($this->relation);
    }

    public function hasRelations(): int
    {
        return count($this->relation);
    }

    /**
     * @var array
     */
    #[ORM\OneToMany(targetEntity: 'App\Domain\Entities\Catalog\ProductRelation', mappedBy: 'related', orphanRemoval: true)]
    protected $related = [];

    public function getRelated($raw = false)
    {
        return $raw ? $this->related : collect($this->related);
    }

    public function hasRelated(): int
    {
        return count($this->related);
    }

    #[ORM\Column(name: '`order`', type: 'integer', options: ['default' => 1])]
    protected int $order = 1;

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @see \App\Domain\Types\ProductStatusType::LIST
     */
    #[ORM\Column(type: 'CatalogProductStatusType', options: ['default' => \App\Domain\Types\Catalog\ProductStatusType::STATUS_WORK])]
    protected string $status = \App\Domain\Types\Catalog\ProductStatusType::STATUS_WORK;

    public function setStatus(string $status): self
    {
        if (in_array($status, \App\Domain\Types\Catalog\ProductStatusType::LIST, true)) {
            $this->status = $status;
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected \DateTime $date;

    public function setDate($date, mixed $timezone = 'UTC'): self
    {
        $this->date = $this->getDateTimeByValue($date, $timezone);

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    #[ORM\Column(type: 'json', options: ['default' => '{}'])]
    protected array $meta = [
        'title' => '',
        'description' => '',
        'keywords' => '',
    ];

    public function setMeta(array $data): self
    {
        $default = [
            'title' => '',
            'description' => '',
            'keywords' => '',
        ];
        $data = array_merge($default, $data);

        $this->meta = [
            'title' => $data['title'],
            'description' => $data['description'],
            'keywords' => $data['keywords'],
        ];

        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    #[ORM\Column(type: 'string', length: 255, options: ['default' => ''])]
    protected string $external_id = '';

    public function setExternalId(string $external_id): self
    {
        if ($this->checkStrLenMax($external_id, 255)) {
            $this->external_id = $external_id;
        }

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->external_id;
    }

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'manual'])]
    protected string $export = 'manual';

    public function setExport(string $export): self
    {
        $this->export = $export;

        return $this;
    }

    public function getExport(): string
    {
        return $this->export;
    }

    /**
     * @var mixed temp variable
     */
    public mixed $buf;

    /**
     * @var array
     */
    #[ORM\OneToMany(targetEntity: '\App\Domain\Entities\File\CatalogProductFileRelation', mappedBy: 'catalog_product', orphanRemoval: true)]
    #[ORM\OrderBy(['order' => 'ASC'])]
    protected $files = [];
}
