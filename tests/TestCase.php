<?php

namespace Tests;

use App\Constants\CaseConst;
use App\Models\Agency;
use App\Models\AgencyCase;
use App\Models\Agent;
use App\Models\CaseTemplate;
use App\Models\Clue;
use App\Models\ClueGroup;
use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\CreatesInstances;
use App\Models\Common\GivesClues;
use App\Models\Common\HasCoordinates;
use App\Models\Common\HasInstances;
use App\Models\Common\HasLocation;
use App\Models\Common\IsPartOfCase;
use App\Models\Conversation;
use App\Models\ConversationLine;
use App\Models\Event;
use App\Models\Evidence;
use App\Models\Location;
use App\Models\Person;
use App\User;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function agency(User $user = null): Agency
    {
        $user = $user ?: $this->user();
        $user->save();
        return new Agency([
            'user_id' => $user->id,
            'name' => 'Testing Agency',
            'slogan' => null,
        ]);
    }

    public function agencyCase(Agency $agency = null, CaseTemplate $caseTemplate = null, Location $location = null): AgencyCase
    {
        $agency = $agency ?: $this->agency();
        $caseTemplate = $caseTemplate ?: $this->caseTemplate();
        $location = $location ?: $this->location();
        $agency->save();
        $caseTemplate->save();
        $location->save();
        return new AgencyCase([
            'agency_id' => $agency->id,
            'case_template_id' => $caseTemplate->id,
            'location_id' => $location->id,
            'coords' => $location->coords,
            'status' => CaseConst::STATUS_OPEN,
            'data' => null,
        ]);
    }

    public function agent(User $user = null, Agency $agency = null): Agent
    {
        $user = $user ?: $this->user();
        $user->save();
        $agency = $agency ?: $this->agency();
        $agency->save();

        $agent = new Agent([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'slogan' => null,
        ]);
        $agency->agents()->save($agent);

        return $agent;
    }

    public function caseTemplate(): CaseTemplate
    {
        return new CaseTemplate([
            'name' => 'Testing Case',
            'type' => CaseConst::TYPE_LOST_ITEM,
        ]);
    }

    public function location(Point $coords = null): Location
    {
        $coords = $coords ?: $coords ?: new Point(rand(-179, 179), rand(-80, 80));
        return new Location([
            'source' => Location::SOURCE_TEST,
            'source_id' => rand(1,999999),
            'coords' => $coords,
            'lat' => $coords->getLat(),
            'lng' => $coords->getLng(),
            'address' => rand(1,99999) . ' Test Road',
            'name' => null,
            'description' => null,
            'image_url' => null,
            'link' => null,
        ]);
    }

    public function clue(
        ?CaseTemplate $caseTemplate = null,
        CaseNoireModel $givenBy = null,
        Evidence $givenEvidence = null,
        CaseNoireModel $evidenceRequirement = null,
        ClueGroup $requiredForClueGroup = null
    ): Clue {
        $caseTemplate = $caseTemplate ?: $this->caseTemplate();
        $caseTemplate->save();

        $givenBy = $givenBy ?: $this->location();
        $givenBy->save();

        if ($givenEvidence) {
            $givenEvidence->save();
        }
        if ($evidenceRequirement) {
            $evidenceRequirement->save();
        }
        if ($requiredForClueGroup) {
            $requiredForClueGroup->save();
        }

        $clue = new Clue([
            'name' => 'Test Clue ' . rand(1, 9999),
            'description' => null,
            'image_url' => null,
            'case_template_id' => $caseTemplate->id,
            'given_by_id' => $givenBy->id,
            'given_by_type' => get_class($givenBy),
            'evidence_id' => $givenEvidence ? $givenEvidence->id : null,
            'evidence_requirement_id' => $evidenceRequirement ? $evidenceRequirement->id : null,
            'evidence_requirement_type' => $evidenceRequirement ? get_class($evidenceRequirement) : null,
            'location_settings' => '{}',
        ]);

        if ($requiredForClueGroup) {
            $clue->save();
            $clue->requiredForClueGroups()->save($requiredForClueGroup);
        }

        return $clue;
    }

    public function clueGroup(CaseNoireModel $gain = null, Clue $requiredClue = null): ClueGroup
    {
        $gain = $gain ?: $this->clue();
        $gain->save();

        $requiredClue = $requiredClue ?: $this->clue($gain->caseTemplate);

        $clueGroup = new ClueGroup([
            'gain_id' => $gain->id,
            'gain_type' => get_class($gain),
        ]);
        $clueGroup->save();
        $clueGroup->requiredClues()->save($requiredClue);
        return $clueGroup;
    }

    public function conversation(CaseTemplate $caseTemplate = null): Conversation
    {
        $caseTemplate = $caseTemplate ?: $this->caseTemplate();
        $caseTemplate->save();

        return new Conversation([
            'case_template_id' => $caseTemplate->id,
        ]);
    }

    /**
     * @param Conversation|null $conversation
     * @param ConversationLine|null $fromLine
     * @param Person|string $saidBy
     * @return ConversationLine
     */
    public function conversationLine(
        Conversation $conversation = null,
        ConversationLine $fromLine = null,
        $saidBy = 'agent'
    ): ConversationLine {
        $conversation = $conversation ?: $this->conversation();
        $conversation->save();

        if ($fromLine) {
            $fromLine->save();
        }

        if ($saidBy instanceof Person) {
            $saidBy->save();
        }

        return new ConversationLine([
            'conversation_id' => $conversation->id,
            'from_line_id' => $fromLine ? $fromLine->id : null,
            'said_by' => $saidBy instanceof Person ? $saidBy->id : $saidBy,
            'text' => 'Test Line',
            'audio_file' => null,
        ]);
    }

    public function event(CaseTemplate $caseTemplate = null, Event $fireByEvent = null): Event
    {
        $caseTemplate = $caseTemplate ?: $this->caseTemplate();
        $caseTemplate->save();

        if ($fireByEvent) {
            $fireByEvent->save();
        }

        return new Event([
            'case_template_id' => $caseTemplate->id,
            'fired_by_event_id' => $fireByEvent ? $fireByEvent->id : null,
            'name' => 'Test Event',
            'description' => null,
            'image_url' => null,
            'timer' => null,
            'location_settings' => '{}',
        ]);
    }

    public function evidence(CaseTemplate $caseTemplate = null): Evidence
    {
        $caseTemplate = $caseTemplate ?: $this->caseTemplate();
        $caseTemplate->save();

        return new Evidence([
            'case_template_id' => $caseTemplate->id,
            'name' => 'Test Evidence',
            'description' => null,
            'image_url' => null,
            'location_settings' => '{}',
        ]);
    }

    public function person(CaseTemplate $caseTemplate = null): Person
    {
        $caseTemplate = $caseTemplate ?: $this->caseTemplate();
        $caseTemplate->save();

        return new Person([
            'case_template_id' => $caseTemplate->id,
            'name' => 'Test Person',
            'description' => null,
            'image_url' => null,
            'location_settings' => '{}',
        ]);
    }

    public function user(): User
    {
        return new User([
            'name' => 'Test User',
            'email' => 'test' . rand(1000,999999) . '@casenoire.com',
            'password' => 'not a password',
        ]);
    }

    /**
     * @param IsPartOfCase|CaseNoireModel $model
     * @throws \Exception
     */
    protected function verifyIsPartOfCase(CaseNoireModel $model)
    {
        $caseTemplate = $this->caseTemplate();
        $caseTemplate->save();
        $model->case_template_id = $caseTemplate->id;
        $model->save();

        $this::assertEquals($caseTemplate->id, $model->caseTemplate->id, '$model->caseTemplate->id');

        $this::assertThrows(
            function() use ($model) {
                $model->caseTemplate->delete();
            },
            'Should not allow deleting active CaseTemplate',
            new \Exception("CaseTemplate ID {$model->case_template_id} is active and cannot be deleted")
        );
    }

    /**
     * @param HasLocation|CaseNoireModel $model
     * @throws \Exception
     */
    protected function verifyHasLocation(CaseNoireModel $model)
    {
        $location = $this->location();
        $location->save();
        $model->update(['location_id' => $location->id]);
        $model->refresh();
        $this::assertEquals($location->id, $model->location->id, '$model->location->id');
    }

    /**
     * @param CreatesInstances|CaseNoireModel $model
     * @throws \Exception
     */
    protected function verifyCreatesInstances(CaseNoireModel $model)
    {
        $model->clues()->delete();
        $model->conversations()->delete();
        $model->events()->delete();
        $model->evidences()->delete();
        $model->persons()->delete();
        $model->modelInstances()->delete();
        $model->refresh();

        $this::assertEquals(0, count($model->clues));
        $this::assertEquals(0, count($model->conversations));
        $this::assertEquals(0, count($model->events));
        $this::assertEquals(0, count($model->evidences));
        $this::assertEquals(0, count($model->persons));
        $this::assertEquals(0, count($model->modelInstances));

        $caseTemplate = $model->caseTemplate ?? $this->caseTemplate();

        $location = $this->location();
        $location->save();

        $clue = $this->clue($caseTemplate);
        $conversation = $this->conversation($caseTemplate);
        $event = $this->event($caseTemplate);
        $evidence = $this->evidence($caseTemplate);
        $person = $this->person($caseTemplate);

        $clue->save();
        $conversation->save();
        $event->save();
        $evidence->save();
        $person->save();

        $model->setInstanceOf($clue);
        $model->setInstanceOf($conversation);
        $model->setInstanceOf($event);
        $model->setInstanceOf($evidence);
        $model->setInstanceOf($person, $location);

        $model->refresh();

        $this::assertEquals(1, count($model->clues));
        $this::assertEquals($clue->id, $model->clues[0]->id);
        $this::assertEquals(1, count($model->conversations));
        $this::assertEquals($conversation->id, $model->conversations[0]->id);
        $this::assertEquals(1, count($model->events));
        $this::assertEquals($event->id, $model->events[0]->id);
        $this::assertEquals(1, count($model->evidences));
        $this::assertEquals($evidence->id, $model->evidences[0]->id);
        $this::assertEquals(1, count($model->persons));
        $this::assertEquals($person->id, $model->persons[0]->id);

        $this::assertEquals(5, $model->modelInstances->count());

        $this::assertEquals(1, $model->locations->count());
        $this::assertEquals($location->id, $model->locations[0]->id);

        $model->clues()->delete();
        $model->conversations()->delete();
        $model->events()->delete();
        $model->evidences()->delete();
        $model->persons()->delete();
        $model->modelInstances()->delete();
        $model->refresh();

        $this::assertEquals(0, count($model->clues));
        $this::assertEquals(0, count($model->conversations));
        $this::assertEquals(0, count($model->events));
        $this::assertEquals(0, count($model->evidences));
        $this::assertEquals(0, count($model->persons));
        $this::assertEquals(0, count($model->modelInstances));
    }

    /**
     * This test also tests against HasAndSpawnsInstances Trait, which uses HasInstances
     * @param HasInstances|HasAndSpawnsInstances|CaseNoireModel $model
     * @throws \Exception
     */
    protected function verifyHasInstances(CaseNoireModel $model)
    {
        $model->agencyCases()->delete();
        $model->instances()->delete();
        if (isset($model->locations)) {
            $model->locations()->delete();
        }

        $model->refresh();

        $this::assertEquals(0, count($model->agencyCases));
        $this::assertEquals(0, count($model->instances));
        if (isset($model->locations)) {
            $this::assertEquals(0, count($model->locations));
        }

        $agencyCase = $this->agencyCase($model->agency ?: null, $model->caseTemplate ?: null);
        $location = $this->location();
        $agencyCase->save();
        $location->save();

        $model->agencyCases()->save($agencyCase, ['location_id' => $location->id]);
        $model->refresh();

        $this::assertEquals(1, count($model->agencyCases));
        $this::assertEquals($agencyCase->id, $model->agencyCases[0]->id);
        $this::assertEquals(1, count($model->instances));
        $this::assertEquals($agencyCase->id, $model->instances[0]->agencyCase->id);
        $this::assertEquals($location->hash, $model->instances[0]->location->hash);
        if (isset($model->locations)) {
            $this::assertEquals(1, count($model->locations));
            $this::assertEquals($location->hash, $model->locations[0]->hash);
        }

        $model->instances[0]->delete();
        $model->refresh();
        $this::assertEquals(0, count($model->agencyCases));
        $this::assertEquals(0, count($model->instances));
        if (isset($model->locations)) {
            $this::assertEquals(0, count($model->locations));
        }

        if (isset($model->location_settings)) {
            $validSettingsArray = [
                'mustSpawn' => true,
                'minRange' => 2,
                'maxRange' => 123,
                'allowedTypes' => [Location::TYPE_ADDRESS],
                'spawnCenterAtType' => Clue::class,
                'spawnCenterAtTypeName' => 'Test Clue',
            ];
            $model->location_settings = $validSettingsArray;
            $this::assertEquals(
                $validSettingsArray,
                json_decode($model->location_settings->toJson(), true),
                '$model->location_settings'
            );
        }
    }

    /**
     * @param GivesClues|CaseNoireModel $model
     * @throws \Exception
     */
    public function verifyGivesClues(CaseNoireModel $model): void
    {
        $model->givenClues()->delete();
        $model->refresh();
        $this::assertEquals(0, count($model->givenClues));

        $clue = $this->clue($model->caseTemplate ?? null);

        $model->givenClues()->save($clue);
        $model->refresh();

        $this::assertEquals(1, count($model->givenClues));
        $this::assertEquals($clue->id, $model->givenClues[0]->id);

        $model->givenClues[0]->delete();
        $model->refresh();
        $this::assertEquals(0, count($model->givenClues));
    }

    /**
     * @param HasCoordinates|CaseNoireModel $model
     * @throws \Exception
     */
    public function verifyHasCoordinates(CaseNoireModel $model): void
    {
        $coords = new Point(10,20);
        $model->coords = $coords;
        $this::assertEquals($coords, $model->coords);
        $this::assertEquals($coords->getLat(), $model->lat);
        $this::assertEquals($coords->getLng(), $model->lng);
        $model->save();
        $this::assertEquals($coords, $model->coords);
    }

    public static function assertThrows(
        callable $thrownIn,
        string $outputMessage = 'Nothing was thrown',
        \Throwable $expectedThrowable = null
    ): void {
        $wasThrown = false;
        try {
            $thrownIn();
        } catch (\Throwable $e) {
            $wasThrown = true;

            if ($expectedThrowable) {
                $thrownClass = get_class($e);
                $expectedClass = get_class($expectedThrowable);
                static::assertTrue(
                    $e instanceof $expectedThrowable,
                    "$thrownClass was thrown, expected $expectedClass"
                );

                if ($expectedThrowable->getMessage()) {
                    static::assertEquals(
                        $expectedThrowable->getMessage(),
                        $e->getMessage(),
                        "Wrong message was thrown"
                    );
                }
            }
        }

        static::assertTrue($wasThrown, $outputMessage);
    }

    public static function assertBetween($value, float $min, float $max, string $message = 'Value'): void
    {
        static::assertTrue($value >= $min && $value <= $max, "$message $value is between $min-$max");
    }
}
