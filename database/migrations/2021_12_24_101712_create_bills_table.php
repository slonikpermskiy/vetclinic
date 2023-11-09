<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {		
		Schema::create('bills', function (Blueprint $table) {
            $table->increments('bill_id')->unique()->notnullable();
			$table->date('date_of_bill')->notnullable();
			$table->char('staff')->notnullable();
            $table->integer('category')->notnullable();
			$table->integer('patient_id')->nullable();
			$table->longtext('product_text')->nullable();
			$table->integer('product_discount')->nullable();
			$table->float('product_summ')->nullable();
			$table->longtext('service_text')->nullable();
			$table->integer('service_discount')->nullable();
			$table->float('service_summ')->nullable();
			$table->float('bill_summ')->nullable();
			$table->float('paied')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('bills');
    }*/
}
