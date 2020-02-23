<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgentTest extends TestCase
{
    use DatabaseTransactions;

    public function testAgent()
    {
        $user = $this->user();
        $agency = $this->agency($user);
        $agent = $this->agent($user, $agency);
        $agent->save();

        $this::assertEquals($user->id, $agent->user->id);
        $this::assertEquals(1, $agent->agencies->count());
        $this::assertEquals($agency->id, $agent->agencies[0]->id);

        $agent->agencies()->delete();
        $agent->refresh();

        $this::assertEquals(0, $agent->agencies->count());

        $this->verifyHasCoordinates($agent);
    }
}
