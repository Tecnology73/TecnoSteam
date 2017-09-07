<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievementsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('achievements', function (Blueprint $table) {
			$table->increments('id')->index();
			$table->unsignedInteger('game_id')->index();
			$table->string('name');
			$table->string('display_name');
			$table->string('default_value')->nullable();
			$table->boolean('hidden')->default(false);
			$table->text('description')->nullable();
			$table->string('icon')->nullable();
			$table->string('icon_gray')->nullable();

			$table->foreign('game_id')
			      ->references('id')->on('games')
			      ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('achievements');
	}
}
