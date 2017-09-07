<?php

namespace App\Jobs\User;

use App\Models\Game;
use App\Models\Game\Achievement;
use App\Models\User\GameAchievement;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Achievements implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $timeout = 240;

	protected $user_id;
	protected $steam_id;
	protected $game_ids;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param $game_ids
	 */
	public function __construct(User $user, $game_ids)
	{
		$this->user_id  = $user->id;
		$this->steam_id = $user->steamid64;
		$this->game_ids = $game_ids;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->game_ids->each(function ($id) {
			if (!isset($id)) return;
			if (!Game::where('id', $id)->count()) return;

			try {
				$achievements = \Steam::userStats($this->steam_id)->GetPlayerAchievements($id);
			} catch (\Exception $e) {
				\Log::error($e);

				return;
			}

			if (!isset($achievements)) return;

			collect($achievements)->each(function ($achievement) use ($id) {
				try {
					$achievement_id = Achievement::where([
						'game_id' => $id,
						'name'    => $achievement->apiName,
					])->first()->id;

					GameAchievement::updateOrCreate([
						'user_id'        => $this->user_id,
						'game_id'        => $id,
						'achievement_id' => $achievement_id,
					], [
						'user_id'        => $this->user_id,
						'game_id'        => $id,
						'achievement_id' => $achievement_id,
						'achieved'       => $achievement->achieved,
						'unlocked_at'    => (!isset($achievement->unlockTimestamp) ? null : Carbon::createFromTimestampUTC
						($achievement->unlockTimestamp)),
					]);
				} catch (\Exception $e) {
					\Log::error($e);
				}
			});
		});
	}
}
