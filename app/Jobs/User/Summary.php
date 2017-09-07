<?php

namespace App\Jobs\User;

use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Steam;

class Summary implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $user_id;
	protected $steam_id;

	/**
	 * Create a new job instance.
	 *
	 * @param $user_id
	 * @param $steam_id
	 */
	public function __construct($user_id, $steam_id)
	{
		$this->user_id  = $user_id;
		$this->steam_id = $steam_id;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$summary = Steam::user($this->steam_id)->GetPlayerSummaries()[0];
		$level   = Steam::player($this->steam_id)->GetPlayerLevelDetails();

		User::find($this->user_id)->update([
			'steamid32'       => $summary->steamIds->id32,
			'name'            => $summary->realName,
			'nickname'        => $summary->personaName,
			'profile_url'     => $summary->profileUrl,
			'avatar'          => $summary->avatarUrl,
			'avatar_full'     => $summary->avatarFullUrl,
			'country'         => $summary->location->country ?? null,
			'state'           => $summary->location->state ?? null,
			'city'            => $summary->location->city ?? null,
			'account_created' => Carbon::createFromTimestampUTC($summary->timecreated),
			'xp'              => $level->playerXp,
			'level'           => $level->playerLevel,
		]);
	}
}
