<?php

namespace mmerlijn\patient\tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use mmerlijn\patient\Models\Requester;
use mmerlijn\patient\tests\TestCase;


class RequesterObserverTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $requesters;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /** @test */
    function test_new_requester_has_relation()
    {
        $requester1 = Requester::factory()->create();
        $requester2 = Requester::factory()->state(['relations' => [$requester1->agbcode]])->create();
        $this->assertDatabaseCount('requesters', 2);
        $requester1->refresh(); //opnieuw ophalen uit de database
        $requester2->refresh();
        $this->assertNotEmpty($requester1->relations);
        $this->assertCount(2, $requester1->relations);
        $this->assertSame($requester1->relations, $requester2->relations);
    }

    function test_add_relation_to_requester()
    {
        $requester1 = Requester::factory()->create();
        $requester2 = Requester::factory()->create();
        $requester3 = Requester::factory()->create();
        $r = $requester2->relations;
        $r[] = $requester3->agbcode;
        $requester2->relations = $r;
        $requester2->save();
        $requester1->refresh();
        $requester2->refresh();
        $requester3->refresh();
        $this->assertCount(1, $requester1->relations);
        $this->assertCount(2, $requester2->relations);
        $this->assertCount(2, $requester3->relations);

    }

    public function test_remove_relation_from_requester()
    {
        $requester1 = Requester::factory()->create();
        $requester2 = Requester::factory()->create();
        $requester3 = Requester::factory()->state(['relations' => [$requester1->agbcode, $requester2->agbcode]])->create();
        $requester1->refresh();
        $requester2->refresh();
        $requester3->refresh();
        $this->assertCount(3, $requester2->relations);
        $this->assertCount(3, $requester1->relations);
        $this->assertCount(3, $requester3->relations);
        $this->assertSame($requester2->relations, $requester3->relations);

        //remove all relations for requester1
        $requester1->relations = [];
        $requester1->save();
        $requester1->refresh();
        $requester2->refresh();
        $requester3->refresh();
        $this->assertCount(1, $requester1->relations);
        $this->assertCount(2, $requester2->relations);
        $this->assertCount(2, $requester3->relations);
        $this->assertSame($requester2->relations, $requester3->relations);
    }

    public function test_delete_relation()
    {
        $requester1 = Requester::factory()->create();
        $requester2 = Requester::factory()->create();
        $requester3 = Requester::factory()->state(['relations' => [$requester1->agbcode, $requester2->agbcode]])->create();
        $requester1->refresh();
        $requester2->refresh();
        $requester3->refresh();
        $this->assertCount(3, $requester2->relations);
        $this->assertCount(3, $requester1->relations);
        $this->assertCount(3, $requester3->relations);
        $requester1->delete();
        $requester2->refresh();
        $requester3->refresh();
        $this->assertSame($requester2->relations, $requester3->relations);
        $this->assertCount(1, $requester1->relations);
        $this->assertCount(2, $requester2->relations);
        $this->assertSoftDeleted('requesters', ['id' => $requester1->id]);
    }
}