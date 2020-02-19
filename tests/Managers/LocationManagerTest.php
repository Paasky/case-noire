<?php

namespace Tests\Managers;

use App\Locations\LocationSettings;
use App\Managers\LocationManager;
use App\Managers\SystemManager;
use App\Models\Clue;
use App\Models\ModelInstance;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocationManagerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetDistance()
    {
        $from = new Point(-0.064937, 51.50819);
        $to = new Point(-0.045745, 51.517801);

        $this::assertEquals(
            2387,
            LocationManager::getDistanceInMeters($from, $to)
        );
    }

    public function testGetRandomPoint()
    {
        $center = new Point(-0.064937, 51.50819);
        $distances = [];

        $maxRange = 10;
        $minRange = 0;
        do {
            $distances[] = $this->runRandomPointDistance($center, $maxRange, $minRange);
        } while (count($distances) < 100);

        // avg should be 5, allow for +/-1 (10%) variance
        $avg = array_sum($distances) / count($distances);
        $this::assertTrue($avg > 4 && $avg < 6, "avg $avg between 4 & 6");


        $distances = [];
        $maxRange = 100;
        $minRange = 50;
        do {
            $distances[] = $this->runRandomPointDistance($center, $maxRange, $minRange);
        } while (count($distances) < 100);

        // avg should be 75, allow for +/-5 (10%) variance
        $avg = array_sum($distances) / count($distances);
        $this::assertTrue($avg > 70 && $avg < 80, "avg $avg between 70 & 80");
    }

    private function runRandomPointDistance(Point $center, int $maxRange, int $minRange): int
    {
        $randomPoint = LocationManager::getRandomPoint($center, $maxRange, $minRange);
        $distance = LocationManager::getDistanceInMeters($center, $randomPoint);
        $this::assertTrue(
            $maxRange >= $distance,
            "maxRange $maxRange >= distance $distance"
        );
        $this::assertTrue(
            $minRange <= $distance,
            "minRange $minRange <= distance $distance"
        );

        return $distance;
    }

    public function testGetSpawnCenter()
    {
        // 1) Spawn center is AgencyCase
        $caseTemplate = $this->caseTemplate();
        $clue = $this->clue($caseTemplate);

        // Clue can spawn 10-100m from case
        $clue->location_settings = new LocationSettings();
        $clue->save();
        $agencyCase = $this->agencyCase(null, $caseTemplate);
        $agencyCase->save();

        $clueLocation = LocationManager::getSpawnCenterLocation($agencyCase, $clue);
        $this::assertEquals($agencyCase->location->id, $clueLocation->id, 'Spawn Location IDs match');

        // Set center for clue instance
        $agencyCase->clues()->save($clue, ['location_id' => $clueLocation->id]);

        // 2) Spawn center is the Clue
        $person = $this->person($caseTemplate);
        $person->location_settings = new LocationSettings([
            'spawnCenterAtType' => Clue::class,
            'spawnCenterAtTypeName' => $clue->name,
        ]);
        $person->save();
        $agencyCase->persons()->save($person);

        $personLocation = LocationManager::getSpawnCenterLocation($agencyCase, $person);
        $this::assertEquals($clueLocation->id, $personLocation->id, 'Spawn Location IDs match');
    }

    public function testGetLocation()
    {
        SystemManager::setMemoryLimit('128M');

        // Create CaseTemplate with Clue & Person
        $caseTemplate = $this->caseTemplate();
        $caseTemplate->save();

        $locationSettings = new LocationSettings(['maxRange' => 100]);
        $clue = $this->clue($caseTemplate);
        $clue->location_settings = $locationSettings;
        $clue->save();
        $person = $this->person($caseTemplate);
        $person->location_settings = $locationSettings;
        $person->save();

        // Create AgencyCase with Location
        $caseLocation = $this->location(new Point(51.506116, -0.062509));
        $agencyCase = $this->agencyCase(null, $caseTemplate, $caseLocation);
        $agencyCase->save();

        // Create Location in range for both Clue & Person
        $unusedLocation = $this->location(LocationManager::getRandomPoint($caseLocation->coords, 100));
        $unusedLocation->save();

        // Get Location for Clue
        $clueLocation = LocationManager::get($agencyCase, $clue);

        // Make sure not same Location as AgencyCase
        $this::assertNotEquals($caseLocation->id, $clueLocation->id, "caseLocation->id != clueLocation");
        $this::assertNotEquals($caseLocation->coords, $clueLocation->coords, 'caseLocation->coords != clueLocation->coords');

        // Make sure the existing Location was selected
        $this::assertEquals($unusedLocation->id, $clueLocation->id, "unusedLocation->id == clueLocation");
        $this::assertEquals($unusedLocation->coords, $clueLocation->coords, 'unusedLocation->coords == clueLocation->coords');

        // Store instance
        $agencyCase->clues()->save($clue, ['location_id' => $clueLocation->id]);
        $agencyCase->refresh();

        // Get Location for Person
        $personLocation = LocationManager::get($agencyCase, $person);

        // Make sure not same Location as AgencyCase or Person
        $this::assertNotEquals($caseLocation->id, $personLocation->id, "caseLocation->id != personLocation");
        $this::assertNotEquals($caseLocation->coords, $personLocation->coords, 'caseLocation->coords != personLocation->coords');
        $this::assertNotEquals($unusedLocation->id, $personLocation->id, "unusedLocation->id != personLocation");
        $this::assertNotEquals($unusedLocation->coords, $personLocation->coords, 'unusedLocation->coords != personLocation->coords');
    }
}
