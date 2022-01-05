<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
//Class CreateRequestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //fast search table
        Schema::create('requesters', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('agbcode', 10)->nullable();
            $table->char('sex', 1)->nullable();
            $table->string('initials', 50)->nullable();
            $table->string('prefix', 50)->nullable();
            $table->string('lastname', 100);
            $table->string('name', 150)->nullable();
            $table->string('postcode', 7)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('building_nr', 20)->nullable();
            $table->string('postbus', 10)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('extra_address', 100)->nullable();
            $table->json('labels')->nullable();
            $table->json('relations')->nullable(); //array with related requesters (agbcode's)
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requesters');
    }
};
