<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClueTest extends TestCase
{
    use DatabaseTransactions;

    public function testClue()
    {
        $caseTemplate = $this->caseTemplate();
        $conversation = $this->conversation($caseTemplate);

        $givenBy = $this->location();
        $givenEvidence = $this->evidence($caseTemplate);
        $requiredForEvidence = $this->conversationLine($conversation);
        $requiredForClueGroup = $this->clueGroup();

        $clue = $this->clue(
            $caseTemplate,
            $givenBy,
            $givenEvidence,
            $requiredForEvidence,
            $requiredForClueGroup
        );
        $this->verifyIsPartOfCase($clue);
        $this->verifyHasInstances($clue);

        $this::assertEquals($givenBy->id, $clue->givenBy->id);
        $this::assertEquals($givenEvidence->id, $clue->evidence->id);
        $this::assertEquals($requiredForEvidence->id, $clue->evidenceRequirement->id);
        $this::assertEquals(1, $clue->requiredForClueGroups->count());
        $this::assertEquals($requiredForClueGroup->id, $clue->requiredForClueGroups[0]->id);
    }
}
