<?php

namespace App\Jobs\User;

use App\Jobs\FriendLibrary;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Steam;

class Friends implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $user;

	/**
	 * Create a new job instance.
	 *
	 * @param $user
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$friends = Steam::user($this->user->steamid64)->GetFriendList();

		$i = 1;

		collect($friends)->each(function ($friend) use (&$i) {
			$friend_user = User::updateOrCreate([
				'steamid64' => $friend->steamIds->id64,
			], [
				'steamid64'       => $friend->steamIds->id64,
				'steamid32'       => $friend->steamIds->id32,
				'name'            => $friend->realName,
				'nickname'        => $friend->personaName,
				'profile_url'     => $friend->profileUrl,
				'avatar'          => $friend->avatarUrl,
				'avatar_full'     => $friend->avatarFullUrl,
				'country'         => $friend->location->country ?? null,
				'state'           => $friend->location->state ?? null,
				'city'            => $friend->location->city ?? null,
				'account_created' => (!isset($friend->timecreated) ? null : Carbon::createFromTimestampUTC
				($friend->timecreated)),
			]);

			$this->user->addFriend($friend_user);

			dispatch((new FriendLibrary($friend_user))
				->onQueue('veryhigh')
				->delay(Carbon::now()->addSecond(floor(rand(1, 3)) * $i++)));
		});
	}
}
