<?php
namespace mmerlijn\patient\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use mmerlijn\patient\Models\Patient;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         Patient::factory()->create();
    }
}
