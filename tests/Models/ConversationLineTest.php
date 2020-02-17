<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConversationLineTest extends TestCase
{
    use DatabaseTransactions;

    public function testConversationLine()
    {
        $conversation = $this->conversation();
        $fromLine = $this->conversationLine($conversation);
        $line = $this->conversationLine($conversation, $fromLine);
        $nextLine = $this->conversationLine($conversation, $line);
        $nextLine->save();

        $this->verifyHasInstances($line);
        $this->verifyGivesClues($line);

        $this::assertEquals($conversation->id, $line->conversation->id);
        $this::assertEquals($fromLine->id, $line->fromLine->id);
        $this::assertEquals(1, $line->nextLines->count());
        $this::assertEquals($nextLine->id, $line->nextLines[0]->id);
    }
}
