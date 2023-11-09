<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->increments('visit_id')->unique()->notnullable();
			$table->char('visit_date_id')->unique()->notnullable(); /*timestamp*/
			$table->date('date_of_visit')->notnullable();
			$table->char('doctor')->notnullable();
			$table->integer('patient_id')->notnullable()->references('patient_id')->on('patients');
			$table->integer('visit_purpose')->nullable();
			$table->integer('visit_type')->nullable();
			$table->longtext('complaints')->nullable();
			$table->longtext('inspection_results')->nullable();
			$table->longtext('clinic_comments')->nullable();
			$table->longtext('research_needed')->nullable();
			$table->longtext('analisys_needed')->nullable();
			$table->longtext('recomendation')->nullable();
			$table->char('old_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('visits');
    }*/
}
