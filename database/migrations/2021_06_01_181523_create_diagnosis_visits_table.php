<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosisVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnosis_visits', function (Blueprint $table) {
            $table->increments('id')->unique()->notnullable();
			$table->char('visit_date_id')->notnullable();
			$table->integer('anymal_id')->notnullable();
			$table->integer('diagnosis_id')->notnullable();
			$table->integer('need_aprove_id')->nullable();
			$table->integer('permanent_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('diagnosis_visits');
    }*/
}
