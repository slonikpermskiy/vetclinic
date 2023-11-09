<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	 
	
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('patient_id')->unique()->notnullable();
			$table->integer('client_id')->notnullable()->references('client_id')->on('clients');
			$table->char('short_name')->notnullable();
			$table->char('full_name')->nullable();
			$table->integer('sex_id')->nullable()->references('sex_id')->on('sex');
			$table->integer('animal_type_id')->notnullable()->references('animal_type_id')->on('anymal_types');
			$table->integer('breed_id')->nullable()->references('breed_id')->on('breeds');
			$table->integer('color_id')->nullable()->references('color_id')->on('colors');
			$table->date('date_of_birth')->nullable();
			$table->integer('aprox_date')->nullable()->default('0');
			$table->integer('rip')->nullable()->default('0');
			$table->char('tatoo')->nullable();
			$table->char('chip')->nullable();
			$table->timestamp('registration_date')->notnullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('castrated')->nullable();
			$table->text('additional_info')->nullable();
			$table->integer('old_id')->notnullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            
        });
    }*/
}
