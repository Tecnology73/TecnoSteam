<?php

namespace App\Jobs;

use App\Jobs\Game\Screenshots;
use App\Models\Game;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FriendLibrary implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $friend;

	public $timeout = 240;
	public $tries   = 3;

	/**
	 * Create a new job instance.
	 *
	 * @param User $friend
	 */
	public function __construct(User $friend)
	{
		$this->friend = $friend;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$friend_games   = \Steam::player($this->friend->steamid64)->GetOwnedGames();
		$existing_games = Game::whereIn('id', $friend_games->pluck('appId'))
		                      ->select('id')
		                      ->get();

		$friend_games->reject(function ($game) use ($existing_games) {
			return $existing_games->contains($game->appId);
		})->each(function ($game) {
			try {
				$app_details = \Steam::app()->appDetails($game->appId)->first();

				if (!isset($app_details)) return;

				$app_details->icon   = $game->icon;
				$app_details->logo   = $game->logo;
				$app_details->header = $game->header;

				Game::fromAppDetails($app_details);
			} catch (\Exception $e) {
			}
		});

		$friend_games->reject(function ($game) use ($existing_games) {
			return !$existing_games->contains($game->appId);
		})->each(function ($game) {
			if (!$this->friend->games->contains($game->appId))
				$this->friend->games()->attach($game->appId);
		});

		// Grab game screenshots
		// Don't grab any screenshots for games
		// if we already have screenshots for that game
		/*$i           = 1;
		$screenshots = Game\Screenshot::whereIn('game_id', $friend_games->pluck('appId'))
		                              ->select('game_id')
		                              ->get();

		$friend_games->reject(function ($game) use ($screenshots) {
			return $screenshots->contains($game->appId);
		})->map(function ($game) {
			return $game->appId;
		})->chunk(10)->each(function ($ids) use (&$i) {
			dispatch((new Screenshots($ids))
				->onQueue('low')
				->delay(Carbon::now()->addSeconds(floor(rand(1, 3)) * $i++)));
		});*/
	}

	public function failed(\Exception $e)
	{
		\Log::error($e);

		$this->delete();
	}
}
