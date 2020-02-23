<?php

namespace App\Managers;

use App\Blueprints\AgencyCaseBlueprint;
use App\Constants\CaseConst;
use App\Models\Agency;
use App\Models\AgencyCase;
use App\Models\CaseTemplate;
use App\Models\Clue;
use App\Models\Conversation;
use App\Models\Event;
use App\Models\Evidence;
use App\Models\Person;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class CaseManager
{
    public static function open(CaseTemplate $caseTemplate, Agency $agency, Point $coords): AgencyCase
    {
        $caseBlueprint = new AgencyCaseBlueprint(
            $agency,
            $caseTemplate,
            LocationManager::get(
                $coords,
                CaseConst::CASE_OPEN_MAX_RANGE,
                CaseConst::CASE_OPEN_MIN_RANGE
            )
        );

        $agencyCase = $caseBlueprint->findExistingModel();
        if ($agencyCase) {
            throw new \InvalidArgumentException("This case is already open as {$agencyCase->nameForDebug()}");
        }
        $agencyCase = AgencyCase::create($caseBlueprint->getModelParams());

        /** @var Clue[]|Conversation[]|Event[]|Evidence[]|Person[] $modelsToSet */
        $modelsToSet = $caseTemplate->all_models->all();
        $setModelsByClassAndName = [];

        // Models can a) not require a location, b) spawn around case or c) spawn around another model
        do {
            $somethingWasDone = false;
            foreach ($modelsToSet as $i => $model) {
                // a) No location
                $hasLocation = isset($model->location_settings) && $model->location_settings->isMustSpawn();

                // b) Has location, center is the case
                $caseIsCenter = $hasLocation ?
                    $model->location_settings->getSpawnAtClass() == AgencyCase::class :
                    false;

                // c) Has location, center is another model
                if ($hasLocation && !$caseIsCenter) {
                    $reqClassAndName =
                        "{$model->location_settings->getSpawnAtClass()}_" .
                        "{$model->location_settings->getSpawnAtName()}";
                    $requiredClassIsSet = isset($setModelsByClassAndName[$reqClassAndName]);
                }

                // if a, b, or c is true, set the instance
                if (!$hasLocation || $caseIsCenter || ($requiredClassIsSet ?? null)) {
                    $location = $hasLocation ?
                        LocationManager::getForCaseModel($agencyCase, $model) :
                        null;
                    $agencyCase->setInstanceOf($model, $location);
                    unset($modelsToSet[$i]);
                    $setModelsByClassAndName[get_class($model) . "_{$model->name}"] = true;
                    $somethingWasDone = true;
                }
            }

            if (!$somethingWasDone) {
                throw new \InvalidArgumentException("Invalid CaseTemplate configuration, could not instantiate any models: ". json_encode($modelsToSet));
            }
        } while ($modelsToSet);

        $agencyCase->refresh();
        return $agencyCase;
    }
}