<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
	public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('client_id')->unique()->notnullable();
			$table->char('last_name')->notnullable();
			$table->char('first_name')->notnullable();
			$table->char('middle_name')->nullable();
			$table->longtext('address')->nullable();
			$table->char('phone')->nullable();
			$table->longtext('phonemore')->nullable();
			$table->char('email')->nullable();
			$table->integer('data_ready')->nullable()->default('0');
			$table->text('comments')->nullable();
			$table->float('total_dept')->notnullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('clients');
    }*/
}
