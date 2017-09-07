<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameAchievementsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_game_achievements', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->index();
			$table->unsignedInteger('game_id')->index();
			$table->unsignedInteger('achievement_id')->index();
			$table->boolean('achieved')->default(false);
			$table->timestamp('unlocked_at')->nullable();

			$table->foreign('user_id')
			      ->references('id')->on('users')
			      ->onDelete('cascade');

			$table->foreign('game_id')
			      ->references('id')->on('games')
			      ->onDelete('cascade');

			$table->foreign('achievement_id')
			      ->references('id')->on('achievements')
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
		Schema::dropIfExists('user_game_achievements');
	}
}
