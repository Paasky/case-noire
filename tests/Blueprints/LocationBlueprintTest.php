<?php

namespace Tests\Blueprints;

use App\Blueprints\LocationBlueprint;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Tests\TestCase;

class LocationBlueprintTest extends TestCase
{
    public function testMinimumData()
    {
        $location = new Point(-25, -35);
        $blueprint = new LocationBlueprint(
            Location::SOURCE_TEST,
            'id-123',
            Location::TYPE_ADDRESS,
            $location,
            'Some Street, City'
        );

        $this::assertEquals(
            [
                'source' => 'test',
                'source_id' => 'id-123',
                'coords' => $location,
                'lat' => -25.0,
                'lng' => -35.0,
                'type' => 'address',
                'address' => 'Some Street, City',
                'name' => null,
                'description' => null,
                'image_url' => null,
                'link' => null,
            ],
            $blueprint->getModelParams()
        );

        $blueprint
            ->setName('')
            ->setDescription('')
            ->setImageUrl('')
            ->setLink('');

        $this::assertEquals(
            [
                'source' => 'test',
                'source_id' => 'id-123',
                'coords' => $location,
                'lat' => -25.0,
                'lng' => -35.0,
                'type' => 'address',
                'address' => 'Some Street, City',
                'name' => null,
                'description' => null,
                'image_url' => null,
                'link' => null,
            ],
            $blueprint->getModelParams()
        );
    }

    public function testFullData()
    {
        $location = new Point(-25, -35);
        $blueprint = new LocationBlueprint(
            Location::SOURCE_TEST,
            'id-123',
            Location::TYPE_ADDRESS,
            $location,
            'Some Street, City'
        );
        $blueprint
            ->setName('Name')
            ->setDescription('Description')
            ->setImageUrl('https://link.to/image.jpg')
            ->setLink('https://link.to/location');

        $this::assertEquals(
            [
                'source' => 'test',
                'source_id' => 'id-123',
                'coords' => $location,
                'lat' => -25.0,
                'lng' => -35.0,
                'type' => 'address',
                'address' => 'Some Street, City',
                'name' => 'Name',
                'description' => 'Description',
                'image_url' => 'https://link.to/image.jpg',
                'link' => 'https://link.to/location',
            ],
            $blueprint->getModelParams()
        );
    }

    public function testInvalidData()
    {
        $blueprint = new LocationBlueprint(
            'not a source',
            '',
            'not a type',
            new Point(0, 0),
            ''
        );
        $blueprint
            ->setImageUrl('notalink')
            ->setLink('https://notalink');

        $errors = [
            "`not a source` is not a valid source",
            "sourceId cannot be empty",
            "`not a type` is not a valid type",
            "coords cannot be [0, 0]",
            "address cannot be empty",
            "imageUrl `notalink` is not a valid url",
            "link `https://notalink` is not a valid url",
        ];
        $this::assertThrows(
            function() use ($blueprint) {
                $blueprint->isValid(true);
            },
            'isValid() should throw an exception',
            new \InvalidArgumentException('Invalid LocationBlueprint: '. json_encode($errors))
        );

        $blueprint->setSource('')->setType('');
        $errors = [
            "source cannot be empty",
            "sourceId cannot be empty",
            "type cannot be empty",
            "coords cannot be [0, 0]",
            "address cannot be empty",
            "imageUrl `notalink` is not a valid url",
            "link `https://notalink` is not a valid url",
        ];
        $this::assertThrows(
            function() use ($blueprint) {
                $blueprint->isValid(true);
            },
            'isValid() should throw an exception',
            new \InvalidArgumentException('Invalid LocationBlueprint: '. json_encode($errors))
        );
    }
}
