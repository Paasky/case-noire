<?php

namespace Tests\Models;

use App\Models\Clue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use DatabaseTransactions;

    public function testLocation()
    {
        $location = $this->location();
        $location->save();

        $caseTemplate = $this->caseTemplate();

        $clue = $this->clue($caseTemplate);
        $clue->save();

        $agencyCase = $this->agencyCase(null, $caseTemplate);
        $agencyCase->save();

        $agencyCase->clues()->save($clue, ['location_id' => $location->id]);

        $this::assertEquals(1, $location->agencyCases->count());
        $this::assertEquals($agencyCase->id, $location->agencyCases[0]->id);
    }
}
