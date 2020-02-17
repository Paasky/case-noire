<?php
/** @noinspection PhpUnused */

namespace App\Models\Common;

use App\Locations\LocationSettings;
use App\Models\Location;
use App\Models\ModelInstance;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin CaseNoireModel
 * @property LocationSettings $location_settings
 * @property-read \Illuminate\Database\Eloquent\Collection|Location[] $locations
 * @property-read int|null $locations_count
 */
trait HasAndSpawnsInstances
{
    use HasInstances;

    public function locations(): MorphToMany
    {
        return $this->morphToMany(Location::class, 'model', ModelInstance::table());
    }

    public function getLocationSettingsAttribute(): LocationSettings
    {
        return LocationSettings::fromJson($this->attributes['location_settings']);
    }

    /**
     * @param LocationSettings|string|array $settings
     * @return HasAndSpawnsInstances
     */
    public function setLocationSettingsAttribute($settings)
    {
        switch (true) {
            case $settings instanceof LocationSettings:
                break;
            case is_string($settings):
                $settings = LocationSettings::fromJson($settings);
                break;
            case is_array($settings):
                $settings = new LocationSettings($settings);
                break;
            default:
                $type = @get_class($settings) ?: gettype($settings);
                throw new \InvalidArgumentException("Unknown LocationSettings type $type");
        }
        $this->attributes['location_settings'] = $settings->toJson();
        return $this;
    }
}