<?php

namespace App\Locations;

use App\Blueprints\LocationBlueprint;

interface MapApi
{
    /**
     * @param float $lat
     * @param float $lng
     * @param string[] $types
     * @param int $limit
     * @return LocationBlueprint[]
     */
    public function findByLatLng(float $lat, float $lng, array $types = [], int $limit = 0): array;

    /**
     * @param string $text
     * @param string[] $types
     * @return LocationBlueprint[]
     */
    public function findByText(string $text, array $types = []): array;
}