<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysisTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analysis_templates', function (Blueprint $table) {
            $table->increments('id')->unique()->notnullable();
			$table->char('analysis_plate_title')->notnullable();
			$table->text('text')->notnullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('analysis_templates');
    }*/
}
