<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClueGroupTest extends TestCase
{
    use DatabaseTransactions;

    public function testClueGroup()
    {
        $caseTemplate = $this->caseTemplate();
        $evidence = $this->evidence($caseTemplate);
        $requiredClue = $this->clue($caseTemplate);
        $evidence->save();
        $requiredClue->save();

        $clueGroup = $this->clueGroup($evidence, $requiredClue);

        $this->verifyGivesClues($clueGroup);
        $this::assertEquals(1, $clueGroup->requiredClues->count());
        $this::assertEquals($requiredClue->id, $clueGroup->requiredClues[0]->id);
    }
}
