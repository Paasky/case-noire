<?php /** @noinspection PhpUnused */

namespace App\Locations;

use App\Models\AgencyCase;
use App\Models\Location;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

class LocationSettings implements Jsonable
{
    protected $mustSpawn = false;
    protected $minRange = 1;
    protected $maxRange = 10;
    protected $allowedTypes = Location::TYPES;
    protected $spawnCenterAtType = AgencyCase::class;
    protected $spawnCenterAtTypeName = '';

    public function __construct(array $params = [])
    {
        foreach ($params as $param => $value) {
            $setter = Str::camel("set $param");
            if (!method_exists($this, $setter)) {
                throw new \InvalidArgumentException("$param does not exist in LocationSettings");
            }
            $this->{$setter}($value);
        }
    }

    public static function fromJson(string $json): self
    {
        return new static(json_decode($json, true));
    }

    /**
     * @inheritDoc
     */
    public function toJson($options = 0): string
    {
        $safeVars = [];
        foreach (get_object_vars($this) as $prop => $value) {
            if (property_exists($this, $prop)) {
                $safeVars[$prop] = $value;
            }
        }
        return json_encode($safeVars, $options);
    }

    /**
     * @return bool
     */
    public function isMustSpawn(): bool
    {
        return $this->mustSpawn;
    }

    /**
     * @param bool $mustSpawn
     * @return LocationSettings
     */
    public function setMustSpawn(bool $mustSpawn): LocationSettings
    {
        $this->mustSpawn = $mustSpawn;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinRange(): int
    {
        return $this->minRange;
    }

    /**
     * @param int $minRange
     * @return LocationSettings
     */
    public function setMinRange(int $minRange): LocationSettings
    {
        $this->minRange = $minRange;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRange(): int
    {
        return $this->maxRange;
    }

    /**
     * @param int $maxRange
     * @return LocationSettings
     */
    public function setMaxRange(int $maxRange): LocationSettings
    {
        $this->maxRange = $maxRange;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }

    /**
     * @param array $allowedTypes
     * @return LocationSettings
     */
    public function setAllowedTypes(array $allowedTypes): LocationSettings
    {
        $this->allowedTypes = $allowedTypes;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpawnCenterAtType(): string
    {
        return $this->spawnCenterAtType;
    }

    /**
     * @param string $spawnCenterAtType
     * @return LocationSettings
     */
    public function setSpawnCenterAtType(string $spawnCenterAtType): LocationSettings
    {
        $this->spawnCenterAtType = $spawnCenterAtType;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpawnCenterAtTypeName(): string
    {
        return $this->spawnCenterAtTypeName;
    }

    /**
     * @param string $spawnCenterAtTypeName
     * @return LocationSettings
     */
    public function setSpawnCenterAtTypeName(string $spawnCenterAtTypeName): LocationSettings
    {
        $this->spawnCenterAtTypeName = $spawnCenterAtTypeName;
        return $this;
    }
}