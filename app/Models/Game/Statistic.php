<?php

namespace App\Models\Game;

use App\Models\Game;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
	protected $fillable = [
		'game_id',
		'name',
		'default_value',
		'display_name',
	];

	public $timestamps = false;

	public function game()
	{
		return $this->belongsTo(Game::class);
	}

	public function format($include_game = false)
	{
		$data = [
			'id'           => $this->id,
			'name'         => $this->name,
			'displayName'  => $this->display_name,
			'defaultValue' => $this->default_value,
		];

		if ($include_game)
			$data['game'] = $this->game->format();

		return $data;
	}
}
