<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuggestionVotesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('suggestion_votes', function (Blueprint $table) {
			$table->increments('id')->index();
			$table->unsignedInteger('user_id')->index();
			$table->unsignedInteger('suggestion_id')->index();
			$table->string('type');

			$table->foreign('user_id')
			      ->references('id')->on('users')
			      ->onDelete('cascade');

			$table->foreign('suggestion_id')
			      ->references('id')->on('suggestions')
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
		Schema::dropIfExists('suggestion_votes');
	}
}
