<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructuresTable extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('structures', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->unsignedInteger('course_id')->default(1);
			$table->unsignedInteger('parent_id')->nullable();
			$table->timestamps();
		});
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('structures');
	}
}
