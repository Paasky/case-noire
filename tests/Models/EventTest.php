<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventTest extends TestCase
{
    use DatabaseTransactions;

    public function testEvent()
    {
        $caseTemplate = $this->caseTemplate();
        $firedByEvent = $this->event($caseTemplate);
        $event = $this->event($caseTemplate, $firedByEvent);
        $eventToFire = $this->event($caseTemplate, $event);
        $eventToFire->save();

        $this->verifyIsPartOfCase($event);
        $this->verifyHasInstances($event);

        $this::assertEquals($firedByEvent->id, $event->firedByEvent->id);
        $this::assertEquals(1, $event->eventsToFire->count());
        $this::assertEquals($eventToFire->id, $event->eventsToFire[0]->id);
    }
}
