<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {		
		Schema::create('vacines', function (Blueprint $table) {
            $table->increments('vacines_id')->unique()->notnullable();
			$table->date('date_of_vacine')->notnullable();
			$table->char('doctor')->notnullable();
			$table->integer('patient_id')->notnullable()->references('patient_id')->on('patients');
			$table->char('vacine_name')->notnullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('vacines');
    }*/
}
