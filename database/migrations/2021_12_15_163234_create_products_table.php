<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
		Schema::create('products', function (Blueprint $table) {
			$table->increments('id')->unique()->notnullable();
			$table->char('product_title')->notnullable();
			$table->char('product_edizm')->notnullable();
			$table->float('product_in_price')->notnullable();
			$table->float('product_out_price')->notnullable();
			$table->integer('category')->nullable()->references('id')->on('product_categories');
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
        Schema::dropIfExists('products');
    }*/
}
