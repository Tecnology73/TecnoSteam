<?php

namespace App\Models\User;

use App\Models\Game;
use App\User;
use Illuminate\Database\Eloquent\Model;

class GameAchievement extends Model
{
	protected $fillable = [
		'user_id',
		'game_id',
		'achievement_id',
		'achieved',
		'unlocked_at',
	];

	protected $hidden = [
		'user_id',
		'game_id',
		'achievement_id',
	];

	protected $dates = [
		'unlocked_at',
	];

	protected $table      = 'user_game_achievements';
	public    $timestamps = false;

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function game()
	{
		return $this->belongsTo(Game::class);
	}

	public function achievement()
	{
		return $this->belongsTo(Game\Achievement::class);
	}

	public function format($include_user = false, $include_game = false)
	{
		$data = [
			'id'         => $this->id,
			'achieved'   => $this->achieved,
			'unlockedAt' => (!isset($this->unlocked_at) ? null : $this->unlocked_at->format('Y-m-d H:i:s\Z')),
		];

		$data = array_merge($data, $this->achievement->format($include_game));

		if ($include_user)
			$data['user'] = $this->user->format();

		return $data;
	}
}
