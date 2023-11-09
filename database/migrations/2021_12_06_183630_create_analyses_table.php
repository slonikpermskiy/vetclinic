<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
		Schema::create('analyses', function (Blueprint $table) {
            $table->increments('analysis_id')->unique()->notnullable();
			$table->date('date_of_analysis')->notnullable();
			$table->char('doctor')->notnullable();
			$table->integer('patient_id')->notnullable()->references('patient_id')->on('patients');
			$table->integer('visit_id')->nullable()->default('0');
			$table->char('analysis_name')->notnullable();
			$table->longtext('analysis_text')->nullable();
        });
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('analyses');
    }*/
}
