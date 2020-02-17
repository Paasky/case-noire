<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgencyCaseTest extends TestCase
{
    use DatabaseTransactions;

    public function testAgencyCase()
    {
        $user = $this->user();
        $user->save();

        $agency = $this->agency($user);
        $agency->save();

        $agent = $this->agent($user, $agency);
        $agent->save();

        $location = $this->location();
        $location->save();

        $agencyCase = $this->agencyCase($agency, null, $location);
        $agencyCase->save();

        $this::assertEquals($agency->id, $agencyCase->agency->id);
        $this::assertEquals($location->id, $agencyCase->location->id);

        $clue = $this->clue($agencyCase->caseTemplate);
        $agencyCase->clues()->save($clue, ['location_id' => $location->id]);
        $agencyCase->refresh();

        $this::assertEquals(1, $agencyCase->locations->count());
        $this::assertEquals($location->id, $agencyCase->locations[0]->id);

        $this->verifyIsPartOfCase($agencyCase);
        $this->verifyCreatesInstances($agencyCase);
    }
}
