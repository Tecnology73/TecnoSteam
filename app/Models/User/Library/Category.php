<?php

namespace App\Models\User\Library;

use App\Models\Game;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function games()
	{
		return $this->belongsToMany(Game::class, 'game_categories', 'category_id', 'game_id');
	}

	public function format($include_games = true)
	{
		$data = [
			'id'          => $this->id,
			'name'        => $this->name,
			'description' => $this->description,
		];

		if ($include_games) {
			$data['games'] = $this->games->transform(function ($game) {
				return $game->format();
			});
		}

		return $data;
	}
}
