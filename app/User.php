<?php

namespace App;

use App\Models\Game;
use App\Models\User\GameAchievement;
use App\Models\User\Library\Category;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable;

	protected $fillable = [
		'steamid64',
		'steamid32',
		'nickname',
		'name',
		'profile_url',
		'avatar',
		'avatar_full',
		'country',
		'state',
		'city',
		'account_created',
	];

	protected $hidden = [
		'name',
		'state',
		'city',
	];

	protected $dates = [
		'account_created',
	];

	public function games()
	{
		return $this->belongsToMany(Game::class, 'game_users', 'user_id', 'game_id');
	}

	public function achievements($game_id = null)
	{
		if (isset($game_id)) return $this->achievements->where('game_id', $game_id);

		return $this->hasMany(GameAchievement::class);
	}

	public function friends()
	{
		return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
	}

	public function categories()
	{
		return $this->hasMany(Category::class);
	}

	public function format($include_personal = false)
	{
		$data = [
			'id'             => $this->id,
			'steamIds'       => [
				'id64' => $this->steamid64,
				'id32' => $this->steamid32,
			],
			'profileUrl'     => $this->profile_url,
			'nickname'       => $this->nickname,
			'avatar'         => $this->avatar,
			'avatarFull'     => $this->avatar_full,
			'country'        => $this->country,
			'accountCreated' => (!isset($this->account_created) ? null : $this->account_created->format('Y-m-d H:i:s\Z')),
		];

		if ($include_personal) {
			$data['name']  = $this->name;
			$data['state'] = $this->state;
			$data['city']  = $this->city;
		}

		return $data;
	}

	public function addFriend(User $friend)
	{
		if (!$this->friends()->where('id', $friend->id)->count())
			$this->friends()->attach($friend);
	}
}
