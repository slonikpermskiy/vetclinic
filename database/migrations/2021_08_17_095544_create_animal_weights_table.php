<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnimalWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animal_weights', function (Blueprint $table) {
			$table->increments('id')->unique()->notnullable();
			$table->char('visit_date_id')->notnullable(); /*timestamp*/
			$table->date('date_of_visit')->notnullable();
			$table->integer('anymal_id')->notnullable();
			$table->float('weight')->notnullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('animal_weights');
    }*/
}
