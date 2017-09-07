<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedBigInteger('steamid64')->index()->unique();
			$table->string('steamid32')->index()->unique()->nullable();
			$table->string('nickname');
			$table->string('name')->nullable();
			$table->string('profile_url')->nullable();
			$table->string('avatar')->nullable();
			$table->string('avatar_full')->nullable();
			$table->string('country')->nullable();
			$table->string('state')->nullable();
			$table->string('city')->nullable();
			$table->unsignedInteger('xp')->default(0);
			$table->unsignedInteger('level')->default(1);
			$table->timestamp('account_created')->nullable();
			$table->rememberToken();
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
		Schema::dropIfExists('users');
	}
}
