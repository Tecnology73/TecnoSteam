<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('game_users', function (Blueprint $table) {
			$table->unsignedInteger('user_id')->index();
			$table->unsignedInteger('game_id')->index();
			$table->unsignedInteger('playtime_two_weeks')->default(0);
			$table->unsignedInteger('playtime_forever')->default(0);

			$table->foreign('user_id')
			      ->references('id')->on('users')
			      ->onDelete('cascade');

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
		Schema::dropIfExists('game_users');
	}
}
