<?php

namespace Tests\External;

use App\Locations\MapBoxApi;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Tests\TestCase;

class MapBoxTest extends TestCase
{
    public function testSingleAddress()
    {
        $mapBox = new MapBoxApi();
        $blueprints = $mapBox->findByLatLng(51.508695, -0.054003, [Location::TYPE_ADDRESS], 1);
        $this::assertEquals(1, count($blueprints));
        $this::assertEquals(
            [
                'source' => "mapbox",
                'source_id' => "address.79648971403708",
                'type' => "address",
                'coords' => new Point(51.508665, -0.05493),
                'lat' => 51.508665,
                'lng' => -0.05493,
                'address' => "54 Maynards Quay, St. Katharine's, London, E1W 3QZ, United Kingdom",
                'name' => "Maynards Quay",
                'description' => null,
                'image_url' => null,
                'link' => null,
            ],
            $blueprints[0]->getModelParams()
        );
    }

    public function testFourAddresses()
    {
        $mapBox = new MapBoxApi();
        $blueprints = $mapBox->findByLatLng(51.509998, -0.043843, [Location::TYPE_ADDRESS], 5);
        $this::assertEquals(4, count($blueprints));
        $this::assertEquals(
            [
                [
                    'source' => "mapbox",
                    'source_id' => "address.871951806641340",
                    'type' => "address",
                    'coords' => new Point(51.510045, -0.043577),
                    'lat' => 51.510045,
                    'lng' => -0.043577,
                    'address' => "55 Jardine Road, St. Katharine's, London, E1W 3WD, United Kingdom",
                    'name' => "Jardine Road",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "address.6447356577610470",
                    'type' => "address",
                    'coords' => new Point(51.510251, -0.04509),
                    'lat' => 51.510251,
                    'lng' => -0.04509,
                    'address' => "350 the Highway, St. Katharine's, London, E1W 3WD, United Kingdom",
                    'name' => "the Highway",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "address.5244914222052514",
                    'type' => "address",
                    'coords' => new Point(51.51040517965257, -0.043249622757878046),
                    'lat' => 51.51040517965257,
                    'lng' => -0.043249622757878046,
                    'address' => "Ratcliffe Orchard, St. Katharine's, London, E1W 3WD, United Kingdom",
                    'name' => "Ratcliffe Orchard",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "address.7635072229283150",
                    'type' => "address",
                    'coords' => new Point(51.511137, -0.04397),
                    'lat' => 51.511137,
                    'lng' => -0.04397,
                    'address' => "5 Cranford Street, Shadwell, London, E1W 3HS, United Kingdom",
                    'name' => "Cranford Street",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
            ],
            [
                $blueprints[0]->getModelParams(),
                $blueprints[1]->getModelParams(),
                $blueprints[2]->getModelParams(),
                $blueprints[3]->getModelParams(),
            ]
        );
    }

    public function testSinglePoi()
    {
        $mapBox = new MapBoxApi();
        $blueprints = $mapBox->findByLatLng(51.508695, -0.054003, [Location::TYPE_POI], 1);
        $this::assertEquals(1, count($blueprints));
        $this::assertEquals(
            [
                'source' => "mapbox",
                'source_id' => "poi.738734407062",
                'type' => "poi",
                'coords' => new Point(51.508160000000004, -0.052549),
                'lat' => 51.508160000000004,
                'lng' => -0.052549,
                'address' => "Shadwell Basin, London, England E1W 3QZ, United Kingdom",
                'name' => "Shadwell Basin",
                'description' => null,
                'image_url' => null,
                'link' => null,
            ],
            $blueprints[0]->getModelParams()
        );
    }

    public function testFivePois()
    {
        $mapBox = new MapBoxApi();
        $blueprints = $mapBox->findByLatLng(51.508695, -0.054003, [Location::TYPE_POI], 5);
        $this::assertEquals(5, count($blueprints));
        $this::assertEquals(
            [
                [
                    'source' => "mapbox",
                    'source_id' => "poi.738734407062",
                    'type' => "poi",
                    'coords' => new Point(51.508160000000004, -0.052549),
                    'lat' => 51.508160000000004,
                    'lng' => -0.052549,
                    'address' => "Shadwell Basin, London, England E1W 3TL, United Kingdom",
                    'name' => "Shadwell Basin",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "poi.283467884291",
                    'type' => "poi",
                    'coords' => new Point(51.50758, -0.05468),
                    'lat' => 51.50758,
                    'lng' => -0.05468,
                    'address' => "Wapping Mini Store, 8 Garnet Street, London, England E1W 3TR, United Kingdom",
                    'name' => "Wapping Mini Store",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "poi.103079301033",
                    'type' => "poi",
                    'coords' => new Point(51.508273, -0.05598),
                    'lat' => 51.508273,
                    'lng' => -0.05598,
                    'address' => "Wapping Woods, London, England E1W 3QX, United Kingdom",
                    'name' => "Wapping Woods",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "poi.833223667000",
                    'type' => "poi",
                    'coords' => new Point(51.507114, -0.054338),
                    'lat' => 51.507114,
                    'lng' => -0.054338,
                    'address' => "Riverside Mansions, MIlk Yard, London, England E1W 3SZ, United Kingdom",
                    'name' => "Riverside Mansions",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
                [
                    'source' => "mapbox",
                    'source_id' => "poi.618475294324",
                    'type' => "poi",
                    'coords' => new Point(51.507306, -0.051918),
                    'lat' => 51.507306,
                    'lng' => -0.051918,
                    'address' => "Wapping Hydraulic Power Station (Disused), Wapping Wall, London, England E1W 3SF, United Kingdom",
                    'name' => "Wapping Hydraulic Power Station (Disused)",
                    'description' => null,
                    'image_url' => null,
                    'link' => null,
                ],
            ],
            [
                $blueprints[0]->getModelParams(),
                $blueprints[1]->getModelParams(),
                $blueprints[2]->getModelParams(),
                $blueprints[3]->getModelParams(),
                $blueprints[4]->getModelParams(),
            ]
        );
    }
}
