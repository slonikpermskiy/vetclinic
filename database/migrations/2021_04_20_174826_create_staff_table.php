<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
			$table->increments('staff_id')->unique()->notnullable();
            $table->char('last_name')->notnullable();
			$table->char('first_name')->notnullable();
			$table->char('middle_name')->nullable();
			$table->char('position')->notnullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('staff');
    }*/
}
