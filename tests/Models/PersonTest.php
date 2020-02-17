<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use DatabaseTransactions;

    public function testPerson()
    {
        $person = $this->person();
        $this->verifyIsPartOfCase($person);
        $this->verifyHasInstances($person);

        $conversation = $this->conversation($person->caseTemplate);
        $conversationLine = $this->conversationLine($conversation, null, $person);
        $conversationLine->save();

        $person->refresh();
        $this::assertEquals(1, $person->conversationLines->count());
        $this::assertEquals($conversationLine->id, $person->conversationLines[0]->id);
    }
}
