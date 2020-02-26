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
        // 1) Create AgencyCase
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

        // 2) Create CaseTemplate Model instances
        $modelsToSet = $caseTemplate->all_models;
        $setModelsByClassAndName = [];

        // Case models can require each other for spawn locations, so keep looping $modelsToSet until
        // a) $modelsToSet is empty or
        // b) Nothing was done, in which case throw an exception as the CaseTemplate is invalid
        do {
            $somethingWasDone = false;

            // Models can
            // a) spawn around case or
            // b) spawn around another model
            // c) not have a location
            foreach ($modelsToSet as $i => $model) {
                $hasLocation = isset($model->location_settings) && $model->location_settings->isMustSpawn();

                // a) Has location & spawn at the case
                $caseIsCenter = $hasLocation && $model->location_settings->getSpawnAtClass() == AgencyCase::class;

                // b) Has location & center is another model
                if ($hasLocation && !$caseIsCenter) {
                    $reqClassAndName =
                        "{$model->location_settings->getSpawnAtClass()}_" .
                        "{$model->location_settings->getSpawnAtName()}";
                    $requiredClassIsSet = isset($setModelsByClassAndName[$reqClassAndName]);
                } else {
                    $requiredClassIsSet = false;
                }

                // If a, b, or c is true, set the instance
                if ($caseIsCenter || $requiredClassIsSet || !$hasLocation) {
                    if ($hasLocation) {
                        $location = LocationManager::getForCaseModel($agencyCase, $model);
                    }
                    $agencyCase->setInstanceOf($model, $location ?? null);

                    // This model has now been set & something was indeed done
                    unset($modelsToSet[$i]);
                    $setModelsByClassAndName[get_class($model) . "_{$model->name}"] = true;
                    $somethingWasDone = true;
                }
            }

            if (!$somethingWasDone) {
                throw new \InvalidArgumentException("Invalid CaseTemplate configuration, could not instantiate any of these models: ". json_encode($modelsToSet));
            }
        } while ($modelsToSet->count());

        $agencyCase->refresh();
        return $agencyCase;
    }
}