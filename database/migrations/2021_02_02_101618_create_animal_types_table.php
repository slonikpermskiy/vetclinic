<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnimalTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {		
        Schema::create('animal_types', function (Blueprint $table) {
            $table->increments('animal_type_id')->unique()->notnullable();
			$table->char('type_title')->notnullable();
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
        Schema::dropIfExists('animal_types');
    }*/
}
