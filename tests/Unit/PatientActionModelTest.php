<?php

namespace mmerlijn\patient\tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use mmerlijn\patient\Models\Patient;
use mmerlijn\patient\Models\PatientAction;
use mmerlijn\patient\Models\Requester;
use mmerlijn\patient\tests\TestCase;


class PatientActionModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        //$req = Requester::factory(3)
        //    ->hasPatients(30)
        //    ->create();

    }

    /** @test */
    public function test_update_information_stored()
    {
        $faker = \Faker\Factory::create('nl_NL');
        $p = Patient::factory()->create();
        $lastname = $p->lastname;
        $dob = $p->dob;
        $p->lastname = $faker->lastName;
        $p->dob = $faker->dateTimeThisCentury->format('Y-m-d');
        $p->save();
        $this->assertSame($p->lastname, $p->action->changes[0]['fields']['lastname']['new']);
        $this->assertSame($lastname, $p->action->changes[0]['fields']['lastname']['old']);
        $this->assertSame($p->dob->format('Y-m-d'), $p->action->changes[0]['fields']['dob']['new']);
        $this->assertSame($dob->format('Y-m-d'), $p->action->changes[0]['fields']['dob']['old']);
        //dd($p->action->changes);
    }

    public function test_add_and_remove_note()
    {
        $p = Patient::factory()->create();
        $p->addNote("Hello world");
        $this->assertIsArray($p->action->notes);
        $this->assertArrayHasKey(0, $p->action->notes);
        $p->addNote('Good morning!');
        $this->assertArrayHasKey(1, $p->action->notes);
        $date = $p->action->notes[0]['date'];
        $p->rmNote($date);
        $this->assertTrue(empty($p->action->notes));
    }

    public function test_add_action()
    {
        $p = Patient::factory()->create();
        $p->addAction('appointment', 'created', 'web afspraak  13-13-2021');
        $this->assertIsArray($p->action->actions);
        $this->assertArrayHasKey(0, $p->action->actions['appointment']);
    }
}