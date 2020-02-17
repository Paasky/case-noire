<?php

namespace Tests\Managers;

use App\Managers\LocationManager;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocationManagerTest extends TestCase
{
    //use DatabaseTransactions;

    public function testGetDistance()
    {
        $from = new Point(-0.064937, 51.50819);
        $to = new Point(-0.045745, 51.517801);

        $this::assertEquals(
            2387,
            LocationManager::getDistanceInMeters($from, $to)
        );
    }

    public function testGetRandomPoint()
    {
        $center = new Point(-0.064937, 51.50819);
        $distances = [];

        $maxRange = 10;
        $minRange = 0;
        do {
            $distances[] = $this->runRandomPointDistance($center, $maxRange, $minRange);
        } while (count($distances) < 100);

        // avg should be 5, allow for +/-1 (10%) variance
        $avg = array_sum($distances) / count($distances);
        $this::assertTrue($avg > 4 && $avg < 6, "avg $avg between 4 & 6");


        $distances = [];
        $maxRange = 100;
        $minRange = 50;
        do {
            $distances[] = $this->runRandomPointDistance($center, $maxRange, $minRange);
        } while (count($distances) < 100);

        // avg should be 75, allow for +/-5 (10%) variance
        $avg = array_sum($distances) / count($distances);
        $this::assertTrue($avg > 70 && $avg < 80, "avg $avg between 70 & 80");
    }

    private function runRandomPointDistance(Point $center, int $maxRange, int $minRange): int
    {
        $randomPoint = LocationManager::getRandomPoint($center, $maxRange, $minRange);
        $distance = LocationManager::getDistanceInMeters($center, $randomPoint);
        $this::assertTrue(
            $maxRange >= $distance,
            "maxRange $maxRange >= distance $distance"
        );
        $this::assertTrue(
            $minRange <= $distance,
            "minRange $minRange <= distance $distance"
        );

        return $distance;
    }
}
