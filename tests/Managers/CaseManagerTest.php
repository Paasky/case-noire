<?php

namespace Tests\Managers;

use App\Locations\LocationSettings;
use App\Managers\CaseManager;
use App\Managers\LocationManager;
use App\Models\Person;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CaseManagerTest extends TestCase
{
    use DatabaseTransactions;

    public function testOpen()
    {
        // CaseTemplate with 3 Persons and 5 clues
        // One Person next to Case
        // Two People in 400-600m radius
        // Two clues next to Case
        // One Clue next to each Person
        $caseTemplate = $this->caseTemplate();

        $person1 = $this->person($caseTemplate);
        $person1->name = "{$person1->name} 1";
        $person1->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 0,
            'maxRange' => 10,
        ]);
        $person1->save();

        $person2 = $this->person($caseTemplate);
        $person2->name = "{$person2->name} 2";
        $person2->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 400,
            'maxRange' => 600,
        ]);
        $person2->save();

        $person3 = $this->person($caseTemplate);
        $person3->name = "{$person3->name} 3";
        $person3->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 400,
            'maxRange' => 600,
        ]);
        $person3->save();

        $clue1 = $this->clue($caseTemplate);
        $clue1->name = "{$clue1->name} 1";
        $clue1->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 0,
            'maxRange' => 10,
            'spawnCenterAtType' => Person::class,
            'spawnCenterAtTypeName' => $person1->name,
        ]);
        $clue1->save();

        $clue2 = $this->clue($caseTemplate);
        $clue2->name = "{$clue2->name} 2";
        $clue2->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 0,
            'maxRange' => 10,
            'spawnCenterAtType' => Person::class,
            'spawnCenterAtTypeName' => $person2->name,
        ]);
        $clue2->save();

        $clue3 = $this->clue($caseTemplate);
        $clue3->name = "{$clue3->name} 3";
        $clue3->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 0,
            'maxRange' => 10,
            'spawnCenterAtType' => Person::class,
            'spawnCenterAtTypeName' => $person3->name,
        ]);
        $clue3->save();

        $clue4 = $this->clue($caseTemplate);
        $clue4->name = "{$clue4->name} 4";
        $clue4->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 0,
            'maxRange' => 10,
        ]);
        $clue4->save();

        $clue5 = $this->clue($caseTemplate);
        $clue5->name = "{$clue5->name} 5";
        $clue5->location_settings = new LocationSettings([
            'mustSpawn' => true,
            'minRange' => 0,
            'maxRange' => 10,
        ]);
        $clue5->save();

        $agency = $this->agency();
        $agent = $this->agent(null, $agency);
        $agent->coords = new Point(51.518116, -0.086887);
        $agent->save();

        $agencyCase = CaseManager::open($caseTemplate, $agency, $agent->coords);

        // 3 People, 5 Clues = 8 instances
        $this::assertEquals(8, $agencyCase->modelInstances->count(), '$agencyCase->modelInstances->count()');

        $caseCoords = $agencyCase->location->coords;
        $person1instance = $agencyCase->getInstanceOf($person1, true);
        $person2instance = $agencyCase->getInstanceOf($person2, true);
        $person3instance = $agencyCase->getInstanceOf($person3, true);
        $clue1instance = $agencyCase->getInstanceOf($clue1, true);
        $clue2instance = $agencyCase->getInstanceOf($clue2, true);
        $clue3instance = $agencyCase->getInstanceOf($clue3, true);
        $clue4instance = $agencyCase->getInstanceOf($clue4, true);
        $clue5instance = $agencyCase->getInstanceOf($clue5, true);

        // Found Locations may be some way away from random point, so allow +/- 200m deviation

        $distance = LocationManager::getDistanceInMeters($caseCoords, $person1instance->coords);
        $this::assertTrue($distance >= 0 && $distance <= 10, "Person 1 is 0-10m from case ($distance)");

        $distance = LocationManager::getDistanceInMeters($caseCoords, $person2instance->coords);
        $this::assertTrue($distance >= 400 && $distance <= 600, "Person 2 is 400-600m from case ($distance)");

        $distance = LocationManager::getDistanceInMeters($caseCoords, $person3instance->coords);
        $this::assertTrue($distance >= 400 && $distance <= 600, "Person 3 is 400-600m from case ($distance)");

        $distance = LocationManager::getDistanceInMeters($person1instance->coords, $clue1instance->coords);
        $this::assertTrue($distance >= 0 && $distance <= 10, "Clue 1 is 0-10m from Person 1 ($distance)");

        $distance = LocationManager::getDistanceInMeters($person2instance->coords, $clue2instance->coords);
        $this::assertTrue($distance >= 0 && $distance <= 10, "Clue 2 is 0-10m from Person 2 ($distance)");

        $distance = LocationManager::getDistanceInMeters($person3instance->coords, $clue3instance->coords);
        $this::assertTrue($distance >= 0 && $distance <= 10, "Clue 3 is 0-10m from Person 3 ($distance)");

        $distance = LocationManager::getDistanceInMeters($caseCoords, $clue4instance->coords);
        $this::assertTrue($distance >= 0 && $distance <= 10, "Clue 4 is 0-10m from case ($distance)");

        $distance = LocationManager::getDistanceInMeters($caseCoords, $clue5instance->coords);
        $this::assertTrue($distance >= 0 && $distance <= 10, "Clue 5 is 0-10m from case ($distance)");
    }
}
