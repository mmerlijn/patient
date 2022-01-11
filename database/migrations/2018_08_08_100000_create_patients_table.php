<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
//Class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //fast search table
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 255)->nullable();
            $table->char('sex', 1)->comment('F->female, M->Male');
            $table->string('initials', 20)->nullable();
            $table->string('lastname', 80)->nullable();
            $table->string('own_lastname', 80);
            $table->string('prefix', 20)->nullable();
            $table->string('own_prefix', 20)->nullable();
            $table->date('dob');
            $table->string('bsn', 10)->nullable();
            $table->string('postcode', 6)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('street', 80)->nullable();
            $table->string('building', 20)->nullable();
            $table->string("last_requester", 8)->comment('agbcode')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('phone2', 40)->nullable();
            $table->string('uzovi', 10)->nullable();
            $table->string('policy_nr', 20)->nullable();
            $table->string('lbsnr', 15)->nullable();
            $table->json('labels')->nullable();
            $table->unsignedSmallInteger('created_by')->nullable();
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['deleted_at', 'bsn', 'dob'], 'patient_ind');
            $table->index('deleted_at', 'last_requester', 'patient_requester_ind');
        });
        Schema::create('patient_actions', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->json('changes')->nullable();
            $table->json('notes')->nullable();
            $table->json('actions')->nullable()->comment('appointment, request, test, email, sms, no_show');
            $table->timestamps();
            $table->foreign('id')->references('id')->on('patients')->onDelete('cascade');
        });
        if (config('database.default') == 'mysql') {
            DB::unprepared("CREATE TRIGGER `patient_tr_after_insert` AFTER INSERT ON `patients` FOR EACH ROW BEGIN
                INSERT INTO `patient_actions` (`id`) VALUES (NEW.id);
                END");
        } else {
            //sqlite
            DB::unprepared("CREATE TRIGGER `patient_tr_after_insert` AFTER INSERT ON `patients` BEGIN
                INSERT INTO `patient_actions` (`id`) VALUES (NEW.id);
                END");
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_actions');
        Schema::dropIfExists('patients');
    }
};
