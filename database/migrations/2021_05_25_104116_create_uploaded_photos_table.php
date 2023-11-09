<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_photos', function (Blueprint $table) {
            
			$table->increments('id')->unique()->notnullable();
			$table->char('visit_date_id')->nullable();
			$table->integer('anymal_id')->notnullable();
			$table->char('image_name')->notnullable();
			$table->char('image_old_name')->notnullable();
			$table->text('description')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('uploaded_photos');
    }*/
}
