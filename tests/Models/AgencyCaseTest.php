<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgencyCaseTest extends TestCase
{
    use DatabaseTransactions;

    public function testAgencyCase()
    {
        $agency = $this->agency();
        $agency->save();

        $agencyCase = $this->agencyCase($agency);
        $agencyCase->save();

        $this::assertEquals($agency->id, $agencyCase->agency->id);

        $this->verifyIsPartOfCase($agencyCase);
        $this->verifyCreatesInstances($agencyCase);
        $this->verifyHasLocation($agencyCase);
        $this->verifyHasCoordinates($agencyCase);
    }
}
