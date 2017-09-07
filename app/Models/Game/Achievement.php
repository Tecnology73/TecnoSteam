<?php

namespace App\Models\Game;

use App\Models\Game;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
	protected $fillable = [
		'game_id',
		'name',
		'display_name',
		'default_value',
		'hidden',
		'description',
		'icon',
		'icon_gray',
	];

	protected $hidden = [
		'game_id',
		'hidden',
		'default_value',
	];

	protected $casts = [
		'hidden' => 'boolean',
	];

	public $timestamps = false;

	public function game()
	{
		return $this->belongsTo(Game::class);
	}

	public function format($include_game = false, $include_full = false)
	{
		$icon = $this->icon;

		if (substr($icon, -strlen('.jpg'), strlen('.jpg')) !== '.jpg')
			$icon = null;

		$data = [
			'id'          => $this->id,
			'name'        => $this->name,
			'displayName' => $this->display_name,
			'description' => $this->description,
			'icon'        => $icon,
		];

		if ($include_game)
			$data['game'] = $this->game->format();

		if ($include_full) {
			$data['defaultValue'] = $this->default_value;
			$data['hidden']       = $this->getAttribute('hidden');
		}

		return $data;
	}
}
