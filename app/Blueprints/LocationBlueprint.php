<?php

namespace App\Blueprints;

use App\Managers\ValidationManager;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class LocationBlueprint implements BlueprintInterface
{
    /** @var string */
    protected $source = '';
    /** @var string */
    protected $sourceId;
    /** @var string */
    protected $type;
    /** @var Point */
    protected $coords;
    /** @var string */
    protected $address;
    /** @var string|null */
    protected $name;
    /** @var string|null */
    protected $description;
    /** @var string|null */
    protected $imageUrl;
    /** @var string|null */
    protected $link;

    /**
     * LocationBlueprint constructor.
     * @param string $source
     * @param string $sourceId
     * @param string $type
     * @param Point $coords
     * @param string $address
     */
    public function __construct(string $source, string $sourceId, string $type, Point $coords, string $address)
    {
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->type = $type;
        $this->coords = $coords;
        $this->address = $address;
    }

    public function getModelParams(bool $verify = true): array
    {
        if ($verify) {
            $this->isValid(true);
        }

        return [
            'source' => $this->source,
            'source_id' => $this->sourceId,
            'coords' => $this->coords,
            'lat' => $this->coords->getLat(),
            'lng' => $this->coords->getLng(),
            'type' => $this->type,
            'address' => $this->address,
            'name' => $this->name ?: null,
            'description' => $this->description ?: null,
            'image_url' => $this->imageUrl ?: null,
            'link' => $this->link ?: null,
        ];
    }

    public function findExistingModel(): ?Location
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Location::where($this->getSearchParams())->first();
    }

    public function getSearchParams(): array
    {
        return ['hash' => Location::generateHash($this->coords, $this->address)];
    }

    public function isValid(bool $verify = false): bool
    {
        $errors = [];

        if (!$this->source) {
            $errors[] = "source cannot be empty";
        } elseif (!in_array($this->source, Location::SOURCES, true)) {
            $errors[] = "`$this->source` is not a valid source";
        }

        if (!$this->sourceId) {
            $errors[] = "sourceId cannot be empty";
        }

        if (!$this->type) {
            $errors[] = "type cannot be empty";
        } elseif (!in_array($this->type, Location::TYPES, true)) {
            $errors[] = "`$this->type` is not a valid type";
        }

        if (!$this->coords) {
            $errors[] = "coords cannot be empty";
        }
        if (!$this->coords->getLat() && !$this->coords->getLng()) {
            $errors[] = "coords cannot be [0, 0]";
        }

        if (!$this->address) {
            $errors[] = "address cannot be empty";
        }

        if ($this->imageUrl && !ValidationManager::isUrl($this->imageUrl)) {
            $errors[] = "imageUrl `$this->imageUrl` is not a valid url";
        }

        if ($this->link && !ValidationManager::isUrl($this->link)) {
            $errors[] = "link `$this->link` is not a valid url";
        }

        if ($errors) {
            if ($verify) {
                throw new \InvalidArgumentException("Invalid LocationBlueprint: " . json_encode($errors));
            }
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return LocationBlueprint
     */
    public function setSource(string $source): LocationBlueprint
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @param string $sourceId
     * @return LocationBlueprint
     */
    public function setSourceId(string $sourceId): LocationBlueprint
    {
        $this->sourceId = $sourceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return LocationBlueprint
     */
    public function setType(string $type): LocationBlueprint
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Point
     */
    public function getCoords(): Point
    {
        return $this->coords;
    }

    /**
     * @param Point $coords
     * @return LocationBlueprint
     */
    public function setCoords(Point $coords): LocationBlueprint
    {
        $this->coords = $coords;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return LocationBlueprint
     */
    public function setAddress(string $address): LocationBlueprint
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return LocationBlueprint
     */
    public function setName(?string $name): LocationBlueprint
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return LocationBlueprint
     */
    public function setDescription(?string $description): LocationBlueprint
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $imageUrl
     * @return LocationBlueprint
     */
    public function setImageUrl(?string $imageUrl): LocationBlueprint
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return LocationBlueprint
     */
    public function setLink(?string $link): LocationBlueprint
    {
        $this->link = $link;
        return $this;
    }
}