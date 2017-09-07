<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news', function (Blueprint $table) {
			$table->unsignedBigInteger('id')->primary();
			$table->unsignedInteger('game_id')->index();
			$table->string('title');
			$table->longText('content')->nullable();
			$table->string('url')->nullable();
			$table->boolean('external')->default(false);
			$table->string('author')->nullable();
			$table->string('label')->nullable();
			$table->timestamp('posted');

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
		Schema::dropIfExists('news');
	}
}
