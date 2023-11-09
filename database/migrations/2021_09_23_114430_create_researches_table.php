<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('researches', function (Blueprint $table) {
            $table->increments('research_id')->unique()->notnullable();
			$table->date('date_of_research')->notnullable();
			$table->char('doctor')->notnullable();
			$table->integer('patient_id')->notnullable()->references('patient_id')->on('patients');
			$table->integer('visit_id')->nullable()->default('0');
			$table->char('research_name')->notnullable();
			$table->longtext('research_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('researches');
    }*/
}

