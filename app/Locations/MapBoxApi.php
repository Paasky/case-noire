<?php

namespace App\Locations;

use App\Blueprints\LocationBlueprint;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GuzzleHttp\Client;

class MapBoxApi implements MapApi
{
    protected $apiKey;
    protected $geocodingUrl = 'http://api.mapbox.com/geocoding';
    protected $version = 'v5';

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey ?: env('MAPBOX_API_KEY');
    }

    protected function constructPlacesQuery(float $lat, float $lng, array $types, int $limit): string
    {
        if (!$types) {
            $types = ['address', 'poi'];
        }
        $limit = $limit ?: (count($types) > 1 ? 1 : 5);
        $types = implode('%2C', $types);
        return "{$this->geocodingUrl}/{$this->version}/mapbox.places/{$lng}%2C{$lat}.json?access_token={$this->apiKey}&types={$types}&limit={$limit}";
    }

    /**
     * @inheritDoc
     */
    public function findByLatLng(float $lat, float $lng, array $types = [], int $limit = 0): array
    {
        $query = $this->constructPlacesQuery($lat, $lng, $types, $limit);
        $client = new Client();
        $response = $client->get($query);
        $data = json_decode($response->getBody()->getContents(), true);

        $blueprints = [];
        foreach ($data['features'] ?? [] as $feature) {
            $id = $feature['id'];
            $type = $feature['place_type'][0];
            $name = $feature['text'];
            $address = $feature['place_name'];
            list($lng, $lat) = $feature['center'];

            $addressPieces = explode(', ', $address);
            $addressPieces = array_unique($addressPieces);
            $address = implode(', ', $addressPieces);

            $blueprint = new LocationBlueprint(
                Location::SOURCE_MAPBOX,
                $id,
                $type,
                new Point($lat, $lng),
                $address
            );

            if ($name) {
                $blueprint->setName($name);
            }

            $blueprints[] = $blueprint;
        }

        return $blueprints;
    }

    /**
     * @inheritDoc
     */
    public function findByText(string $text, array $types = []): array
    {
        // TODO: Implement findByText() method.
    }
}