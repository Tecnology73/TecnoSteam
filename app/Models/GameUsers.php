<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameUsers extends Model
{
	protected $fillable = [
		'game_id',
		'user_id',
		'playtime_two_weeks',
		'playtime_forever',
	];

	protected $primaryKey = 'game_id';
	public    $timestamps = false;
}
