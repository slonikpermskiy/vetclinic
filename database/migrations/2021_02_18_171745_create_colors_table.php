<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
		Schema::create('colors', function (Blueprint $table) {
            $table->increments('color_id')->unique()->notnullable();
			$table->char('color_title')->notnullable();
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
        Schema::dropIfExists('colors');
    }*/
}
