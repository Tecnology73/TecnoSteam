<?php

namespace App\Models\Game;

use App\Models\Game;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
	protected $fillable = [
		'game_id',
		'url',
	];

	protected $hidden = [
		'id',
	];

	public $timestamps = false;

	public function game()
	{
		return $this->belongsTo(Game::class);
	}
}
