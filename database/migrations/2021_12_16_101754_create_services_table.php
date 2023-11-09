<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
		Schema::create('services', function (Blueprint $table) {
			$table->increments('id')->unique()->notnullable();
			$table->char('service_title')->notnullable();
			$table->float('service_price')->notnullable();
			$table->integer('category')->nullable()->references('id')->on('service_categories');
			$table->longtext('old_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('services');
    }*/
}
