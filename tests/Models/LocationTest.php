<?php

namespace Tests\Models;

use App\Managers\LocationManager;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
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

    public function testRadiusQuery()
    {
        $center = $this->location(new Point(-0.064937, 51.50819));
        $fiftyM = $this->location(new Point(-0.0652711631, 51.5078890961));
        $hundredM = $this->location(new Point(-0.0645712129, 51.5073683693));

        $center->save();
        $fiftyM->save();
        $hundredM->save();

        /** @var Collection|Location[] $within45m */
        $within45m = Location::inRange($center->coords, 45)->get();
        $this::assertEquals(1, $within45m->count(), '$within45m->count()');
        $this::assertEquals($center->id, $within45m[0]->id, '$center ID');

        /** @var Collection|Location[] $within45m */
        $within5to55m = Location::inRange($center->coords, 55, 5)->get();
        $this::assertEquals(1, $within5to55m->count(), '$within5to55m->count()');
        $this::assertEquals($fiftyM->id, $within5to55m[0]->id, '$fiftyM ID');

        /** @var Collection|Location[] $within45m */
        $within55to105m = Location::inRange($center->coords, 105, 55)->get();
        $this::assertEquals(1, $within55to105m->count(), '$within55to105m->count()');
        $this::assertEquals($hundredM->id, $within55to105m[0]->id, '$hundredM ID');
    }
}
