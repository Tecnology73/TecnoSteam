<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('games', function (Blueprint $table) {
			$table->unsignedInteger('id')->index()->unique()->primary();
			$table->string('name');
			$table->string('controller_support')->nullable();
			$table->longText('description');
			$table->string('icon')->nullable();
			$table->string('logo')->nullable();
			$table->string('header')->nullable();
			$table->longText('about');
			$table->string('website');
			$table->json('developers');
			$table->json('publishers');
			$table->integer('price');
			$table->string('price_currency')->default('USD');
			$table->integer('price_discount')->default(0);
			$table->json('platforms');
			$table->json('categories');
			$table->json('genres');
			$table->timestamp('release');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('games');
	}
}
