<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breeds', function (Blueprint $table) {
			$table->increments('breed_id')->unique()->notnullable();
			$table->char('breed_title')->notnullable();
			$table->integer('animal_type_id')->notnullable()->references('animal_type_id')->on('animal_types');
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
        Schema::dropIfExists('breeds');
    }*/
}
