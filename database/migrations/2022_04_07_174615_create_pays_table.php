<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->increments('pay_id')->unique()->notnullable();
			$table->integer('bill_id')->notnullable();
			$table->integer('patient_id')->nullable();
			$table->date('date_of_pay')->notnullable();
			$table->float('pay_summ')->notnullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('pays');
    }*/
}
