<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EvidenceTest extends TestCase
{
    use DatabaseTransactions;

    public function testEvidence()
    {
        $caseTemplate = $this->caseTemplate();
        $evidence = $this->evidence($caseTemplate);
        $evidence->save();
        $this->verifyIsPartOfCase($evidence);
        $this->verifyHasInstances($evidence);

        $givenByClue = $this->clue($caseTemplate, null, $evidence);
        $givenByClue->save();
        $givenByClueGroup = $this->clueGroup($evidence);
        $givenByClueGroup->save();

        $evidence->refresh();

        $this::assertEquals(1, $evidence->givenByClues->count());
        $this::assertEquals(1, $evidence->givenByClueGroups->count());
        $this::assertEquals($givenByClue->id, $evidence->givenByClues[0]->id);
        $this::assertEquals($givenByClueGroup->id, $evidence->givenByClueGroups[0]->id);
    }
}
