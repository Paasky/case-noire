<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUser()
    {
        $user = $this->user();
        $agency = $this->agency($user);
        $agent = $this->agent($user, $agency);
        $agent->save();

        $this::assertEquals(1, $user->agencies->count());
        $this::assertEquals($agency->id, $user->agencies[0]->id);
        $this::assertEquals(1, $user->agents->count());
        $this::assertEquals($agent->id, $user->agents[0]->id);
    }
}
