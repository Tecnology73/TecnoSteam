<?php

namespace App\Jobs;

use App\Jobs\Game\News;
use App\Jobs\Game\Schema;
use App\Jobs\Game\Screenshots;
use App\Jobs\User\Achievements;
use App\Models\Game;
use App\Models\GameUsers;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UserLibrary implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $user;

	public $timeout = 240;

	/**
	 * Create a new job instance.
	 * @param User $user
	 */
	public function __construct(User $user)
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
		$user_games     = \Steam::player($this->user->steamid64)->GetOwnedGames();
		$existing_games = Game::whereIn('id', $user_games->pluck('appId'))
		                      ->select('id')
		                      ->get();

		// Log any Games that aren't already in the db
		$user_games->reject(function ($game) use ($existing_games) {
			return $existing_games->contains($game->appId);
		})->each(function ($game) use ($existing_games) {
			try {
				$app_details = \Steam::app()->appDetails($game->appId)->first();

				if (!isset($app_details)) return;

				$app_details->icon   = $game->icon;
				$app_details->logo   = $game->logo;
				$app_details->header = $game->header;

				$game = Game::fromAppDetails($app_details);
				$existing_games->push($game);
			} catch (\Exception $e) {
			}
		});

		// Add all the games to users library
		// Only if the game exists in the db
		// Could cause problems...
		$user_games->reject(function ($game) use ($existing_games) {
			return !$existing_games->contains($game->appId);
		})->each(function ($game) {
			GameUsers::updateOrCreate([
				'game_id' => $game->appId,
				'user_id' => $this->user->id,
			], [
				'game_id'            => $game->appId,
				'user_id'            => $this->user->id,
				'playtime_two_weeks' => $game->playtimeTwoWeeks,
				'playtime_forever'   => $game->playtimeForever,
			]);
		});

		// Log Achievements + Statistics
		dispatch((new Schema($user_games->pluck('appId')))
			->onQueue('high')
			->delay(Carbon::now()->addSecond()));
		dispatch((new Achievements($this->user, $user_games->pluck('appId')))
			->onQueue('high')
			->delay(Carbon::now()->addSeconds(1)));

		// Grab News
		$i    = 1;
		$news = Game\News::whereIn('game_id', $user_games->pluck('appId'))
		                 ->select('game_id')
		                 ->get();

		$user_games->reject(function ($game) use ($existing_games, $news) {
			return (!$existing_games->contains($game->appId) || $news->contains($game->appId));
		})->each(function ($game) use (&$i) {
			dispatch((new News($game->appId))
				->onQueue('medium')
				->delay(Carbon::now()->addSeconds($i++)));
		});

		// Grab game screenshots
		// Don't grab any screenshots for games
		// if we already have screenshots for that game
		$i           = 1;
		$screenshots = Game\Screenshot::whereIn('game_id', $user_games->pluck('appId'))
		                              ->select('game_id')
		                              ->get();

		$user_games->reject(function ($game) use ($existing_games, $screenshots) {
			return (!$existing_games->contains($game->appId) || $screenshots->contains($game->appId));
		})->map(function ($game) {
			return $game->appId;
		})->chunk(10)->each(function ($ids) use (&$i) {
			dispatch((new Screenshots($ids))
				->onQueue('low')
				->delay(Carbon::now()->addSeconds($i++)));
		});
	}
}
