<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CaseTemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function testCaseTemplate()
    {
        $caseTemplate = $this->caseTemplate();

        $agencyCase = $this->agencyCase(null, $caseTemplate);
        $clue = $this->clue($caseTemplate);
        $conversation = $this->conversation($caseTemplate);
        $event = $this->event($caseTemplate);
        $evidence = $this->evidence($caseTemplate);
        $person = $this->person($caseTemplate);

        $agencyCase->save();
        $clue->save();
        $conversation->save();
        $event->save();
        $evidence->save();
        $person->save();

        $this::assertEquals(1, $caseTemplate->agencyCases->count());
        $this::assertEquals(1, $caseTemplate->clues->count());
        $this::assertEquals(1, $caseTemplate->conversations->count());
        $this::assertEquals(1, $caseTemplate->events->count());
        $this::assertEquals(1, $caseTemplate->evidences->count());
        $this::assertEquals(1, $caseTemplate->persons->count());

        $this::assertEquals($agencyCase->id, $caseTemplate->agencyCases[0]->id);
        $this::assertEquals($clue->id, $caseTemplate->clues[0]->id);
        $this::assertEquals($conversation->id, $caseTemplate->conversations[0]->id);
        $this::assertEquals($event->id, $caseTemplate->events[0]->id);
        $this::assertEquals($evidence->id, $caseTemplate->evidences[0]->id);
        $this::assertEquals($person->id, $caseTemplate->persons[0]->id);

        $agencyCase->delete();
        $clue->delete();
        $conversation->delete();
        $event->delete();
        $evidence->delete();
        $person->delete();

        $caseTemplate->refresh();

        $this::assertEquals(0, $caseTemplate->agencyCases->count());
        $this::assertEquals(0, $caseTemplate->clues->count());
        $this::assertEquals(0, $caseTemplate->conversations->count());
        $this::assertEquals(0, $caseTemplate->events->count());
        $this::assertEquals(0, $caseTemplate->evidences->count());
        $this::assertEquals(0, $caseTemplate->persons->count());
    }
}
