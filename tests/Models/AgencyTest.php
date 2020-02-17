<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgencyTest extends TestCase
{
    use DatabaseTransactions;

    public function testAgency()
    {
        $user = $this->user();
        $user->save();

        $agency = $this->agency($user);
        $agency->save();

        $agent = $this->agent($user, $agency);
        $agent->save();

        $agencyCase = $this->agencyCase($agency);
        $agencyCase->save();

        $agency->refresh();

        $this::assertEquals($user->id, $agency->owner->id);

        $this::assertEquals(1, $agency->cases->count());
        $this::assertEquals($agencyCase->id, $agency->cases[0]->id);

        $this::assertEquals(1, $agency->agents->count());
        $this::assertEquals($agent->id, $agency->agents[0]->id);

        $agency->cases()->delete();
        $agency->agents()->delete();

        $agency->refresh();

        $this::assertEquals(0, $agency->cases->count());
        $this::assertEquals(0, $agency->agents->count());
    }
}
