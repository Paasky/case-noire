<?php

namespace Tests\Blueprints;

use App\Blueprints\AgencyCaseBlueprint;
use App\Constants\CaseConst;
use App\Managers\LocationManager;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgencyCaseBlueprintTest extends TestCase
{
    use DatabaseTransactions;

    public function testMinimumData()
    {
        $agency = $this->agency();
        $agency->save();
        $caseTemplate = $this->caseTemplate();
        $caseTemplate->save();
        $location = $this->location(new Point(60.173825, 24.948761));
        $location->save();

        $blueprint = new AgencyCaseBlueprint($agency, $caseTemplate, $location);
        $modelParams = $blueprint->getModelParams();
        $generatedCoords = $modelParams['coords'];
        $distance = LocationManager::getDistanceInMeters($location->coords, $generatedCoords);
        $this::assertTrue($distance >= 1 && $distance <= 3, "Generated coords are 1-3m from location, was $distance");

        $this::assertEquals(
            [
                'agency_id' => $agency->id,
                'case_template_id' => $caseTemplate->id,
                'location_id' => $location->id,
                'coords' => $generatedCoords,
                'status' => 'open',
                'data' => null,
            ],
            $modelParams
        );
    }

    public function testFullData()
    {
        $agency = $this->agency();
        $agency->save();
        $caseTemplate = $this->caseTemplate();
        $caseTemplate->save();
        $location = $this->location(new Point(60.173825, 24.948761));
        $location->save();

        $blueprint = new AgencyCaseBlueprint($agency, $caseTemplate, $location);
        $blueprint->setStatus(CaseConst::STATUS_CLOSED)->setData(['this' => 'that']);

        $modelParams = $blueprint->getModelParams();
        $generatedCoords = $modelParams['coords'];
        $distance = LocationManager::getDistanceInMeters($location->coords, $generatedCoords);
        $this::assertBetween($distance, 1, 3,"Generated coords distance from location");

        $this::assertEquals(
            [
                'agency_id' => $agency->id,
                'case_template_id' => $caseTemplate->id,
                'location_id' => $location->id,
                'coords' => $generatedCoords,
                'status' => 'closed',
                'data' => ['this' => 'that'],
            ],
            $modelParams
        );
    }

    public function testInvalidData()
    {
        $agency = $this->agency();
        $caseTemplate = $this->caseTemplate();
        $location = $this->location();
        $location->coords = null;

        $blueprint = new AgencyCaseBlueprint($agency, $caseTemplate, $location);
        $blueprint->setStatus('something');

        $exceptionMessage = 'Invalid AgencyCaseBlueprint: '. json_encode([
            "Agency doesn't have ID",
            "CaseTemplate doesn't have ID",
            "Location doesn't have ID",
            "Location doesn't have coordinates",
            "Invalid status something"
        ]);
        $this::assertThrows(
            function() use ($blueprint) {
                $blueprint->isValid(true);
            },
            'isValid throws exception',
            new \InvalidArgumentException($exceptionMessage)
        );
    }
}
