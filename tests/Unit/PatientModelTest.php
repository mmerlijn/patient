<?php

namespace mmerlijn\patient\tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use mmerlijn\patient\Models\Patient;
use mmerlijn\patient\Models\Requester;
use mmerlijn\patient\tests\TestCase;


class PatientModelTest extends TestCase
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
    function test_filter()
    {
        Requester::factory()->hasPatients(1)->create();
        $p = Patient::first();
        $filter = ['name' => substr($p->own_lastname, 0, 4), 'dob' => $p->dob->format('Y-m-d'), 'city' => $p->city, 'postcode' => $p->postcode, 'bsn' => $p->bsn, 'requester' => $p->last_requester];
        DB::enableQueryLog();
        $patients = Patient::filtered($filter)->get();
        $query = DB::getQueryLog()[0]['query'];
        $this->assertStringContainsString('lastname', $query);
        $this->assertStringContainsString('dob', $query);
        $this->assertStringContainsString('city', $query);
        $this->assertStringContainsString('postcode', $query);
        $this->assertStringContainsString('bsn', $query);
        $this->assertStringContainsString('last_requester', $query);

        DB::flushQueryLog();
        //wrong dob, filter ignore dob
        //wrong bsn, filter ignore bsn
        $filter = ['dob' => '2000-14-31', 'bsn' => '1234'];
        $patients = Patient::filtered($filter)->get();
        $query = DB::getQueryLog()[0]['query'];
        $this->assertStringNotContainsString('dob', $query);
        $this->assertStringNotContainsString('bsn', $query);
    }

    public function test_salutation_attributes()
    {
        Patient::factory()->create();
        $patient = Patient::first();
        $this->assertStringContainsString($patient->lastname ?? "", $patient->name);
        $this->assertStringContainsString($patient->own_lastname, $patient->salutation);
        $this->assertStringContainsString($patient->own_prefix ?? "", $patient->salutation);
        $this->assertStringContainsString($patient->prefix ?? "", $patient->salutation);
        $this->assertStringContainsString($patient->initials, $patient->salutation);
        $this->assertStringContainsString(($patient->sex == "M") ? "Dhr. " : "Mevr. ", $patient->salutation);

    }

    public function test_initials_cast()
    {

        Patient::factory()->create();
        $p = Patient::first();
        $p->initials = "M.M.";
        $p->save();
        $this->assertSame('M.M.', $p->initials);
        $this->assertDatabaseHas('patients', ['id' => 1, 'initials' => 'MM']);
    }

    public function test_lastname_prefix_observer()
    {
        $p = new Patient();
        $p->sex = "F";
        $p->dob = "2000-10-10";
        $p->lastname = "de Velden";
        $p->prefix = "van de";
        $p->own_lastname = "de Groot";
        $p->own_prefix = "";
        $p->save();
        $p->refresh();
        $this->assertDatabaseHas('patients', ['lastname' => 'Velden', 'prefix' => 'van de', 'own_lastname' => "Groot", "own_prefix" => "de"]);
    }

    public function test_phone_observer_and_cast()
    {
        Patient::factory()->create();
        $p = Patient::first();
        $p->phone = "+31612341234";
        $p->save();
        $this->assertDatabaseHas('patients', ['id' => $p->id, 'phone' => "0612341234"]);
        $this->assertSame("06 1234 1234", (string)$p->phone);
    }

    public function test_create_new()
    {
        //DB::enableQueryLog();
        $p = Patient::findOrCreate(sex: "M", bsn: "123456782", dob: "2000-11-11", postcode: "1040AA", building: 30, street: "Streetname", city: "Amsterdam", own_lastname: "Van der Velden", phone2: "06 1234 1234", initials: "B");
        //dd(DB::getQueryLog());
        $this->assertDatabaseHas('patients', ['bsn' => "123456782", 'city' => 'Amsterdam', 'phone' => "0612341234"]);
        $p = Patient::findOrCreate(sex: "M", bsn: "123456782", dob: "2000-11-11", postcode: "1040AA", building: 30, street: "Streetname", city: "Amsterdam", own_lastname: "Van der Velden", initials: "B", lastname: 'Groen');
        $this->assertDatabaseCount('patients', 1);
        $this->assertDatabaseHas('patients', ['bsn' => '123456782', 'lastname' => 'Groen']);
    }

    public function test_addressModel_initialisation()
    {
        $p = Patient::factory()->create();
        $this->assertSame($p->postcode, $p->address->postcode);
        $p->building = "54a";
        $p->save();
        $this->assertSame("54", $p->address->building_nr);
        $this->assertSame("a", $p->address->building_addition);
        $this->assertSame("54 a", $p->address->building);
    }

    public function test_add_action()
    {
        $p = Patient::factory()->create();
        $p->addAction('appointment', 'created', ['start_time' => '2020-10-10 10:10:00']);
        $this->assertSame('2020-10-10 10:10:00', $p->action->actions['appointment'][0]['details']['start_time']);
        $this->assertSame('created', $p->action->actions['appointment'][0]['subject']);
        $this->assertSame('created', Patient::first()->action->actions['appointment'][0]['subject']);
    }
}