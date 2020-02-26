<?php

namespace Tests\Managers;

use App\Locations\LocationSettings;
use App\Managers\LocationManager;
use App\Managers\SystemManager;
use App\Models\Clue;
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

        $maxRange = 1;
        $minRange = 0;
        $distances = 0;
        do {
            $distance = $this->runRandomPointDistance($center, $maxRange, $minRange);
            $this::assertTrue($distance >= 0 && $distance <= 1, "distance $distance between 0 & 1");
            $distances++;
        } while ($distances < 100);

        $maxRange = 1000;
        $minRange = 999;
        $distances = 0;
        do {
            $distance = $this->runRandomPointDistance($center, $maxRange, $minRange);
            $this::assertTrue($distance >= 999 && $distance <= 1000, "distance $distance between 999 & 1000");
            $distances++;
        } while ($distances < 100);
    }

    private function runRandomPointDistance(Point $center, int $maxRange, int $minRange): int
    {
        $randomPoint = LocationManager::getRandomPoint($center, $maxRange, $minRange);
        $distance = LocationManager::getDistanceInMeters($center, $randomPoint);
        $this::assertTrue(
            $distance <= $maxRange && $distance >= $minRange,
            "distance $distance between $minRange & $maxRange"
        );

        return $distance;
    }

    public function testGetCenterModel()
    {
        // 1) Spawn center is AgencyCase
        $caseTemplate = $this->caseTemplate();
        $clue = $this->clue($caseTemplate);

        $clue->location_settings = new LocationSettings();
        $clue->save();
        $agencyCase = $this->agencyCase(null, $caseTemplate);
        $agencyCase->save();

        $centerModel = LocationManager::getCenterModel($agencyCase, $clue);
        $this::assertEquals(get_class($agencyCase), get_class($centerModel), 'Center Model Classes match');
        $this::assertEquals($agencyCase->id, $centerModel->id, 'Center Model IDs match');

        $agencyCase->setInstanceOf($clue, $centerModel->location);

        // 2) Spawn center is the Clue
        $person = $this->person($caseTemplate);
        $person->location_settings = new LocationSettings([
            'spawnCenterAtType' => Clue::class,
            'spawnCenterAtTypeName' => $clue->name,
        ]);
        $person->save();

        $centerModel = LocationManager::getCenterModel($agencyCase, $person, true);
        $this::assertEquals(get_class($clue), $centerModel->model_type, 'Center Model Classes match');
        $this::assertEquals($clue->id, $centerModel->model_id, 'Center Model IDs match');
    }

    public function testGetCenterLocation()
    {
        // 1) Spawn center is AgencyCase
        $caseTemplate = $this->caseTemplate();
        $clue = $this->clue($caseTemplate);

        // Clue can spawn 10-100m from case
        $clue->location_settings = new LocationSettings();
        $clue->save();
        $agencyCase = $this->agencyCase(null, $caseTemplate);
        $agencyCase->save();

        $clueLocation = LocationManager::getCenterLocation($agencyCase);
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

        $personLocation = LocationManager::getCenterLocation($agencyCase->getInstanceOf($clue));
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
        $unusedLocation = $this->location(LocationManager::getRandomPoint($caseLocation->coords, 90));
        $unusedLocation->save();

        // Get Location for Clue
        $clueLocation = LocationManager::getForCaseModel($agencyCase, $clue);

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
        $personLocation = LocationManager::getForCaseModel($agencyCase, $person);

        // Make sure not same Location as AgencyCase or Person
        $this::assertNotEquals($caseLocation->id, $personLocation->id, "caseLocation->id != personLocation");
        $this::assertNotEquals($caseLocation->coords, $personLocation->coords, 'caseLocation->coords != personLocation->coords');
        $this::assertNotEquals($unusedLocation->id, $personLocation->id, "unusedLocation->id != personLocation");
        $this::assertNotEquals($unusedLocation->coords, $personLocation->coords, 'unusedLocation->coords != personLocation->coords');
    }
}
