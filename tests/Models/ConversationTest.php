<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use DatabaseTransactions;

    public function testConversation()
    {
        $conversation = $this->conversation();
        $this->verifyIsPartOfCase($conversation);
        $this->verifyHasInstances($conversation);

        $line = $this->conversationLine($conversation);
        $line->save();
        $conversation->refresh();
        $this::assertEquals(1, $conversation->lines->count());
        $this::assertEquals($line->id, $conversation->lines[0]->id);
    }
}
