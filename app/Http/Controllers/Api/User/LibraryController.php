<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Game;
use App\Models\GameUsers;
use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class LibraryController extends Controller
{
	public function list()
	{
		$user              = \Auth::user();
		$categorised_games = [];

		$stats = GameUsers::where('user_id', $user->id)
		                  ->whereIn('game_id', $user->games->pluck('id'))
		                  ->get();

		$library = $user->categories->transform(function ($category) use ($user, $stats, &$categorised_games) {
			$data = $category->format(false);

			$games = $category->games->transform(function ($game) use ($user, $stats, &$categorised_games) {
				$categorised_games[] = $game->id;

				return $this->formatGame($user, $game, $stats);
			});

			$data['games'] = $games;

			return $data;
		});

		$other_games = $user->games()->whereNotIn('id', $categorised_games)
		                    ->get()
		                    ->transform(function ($game) use ($user, $stats) {
			                    return $this->formatGame($user, $game, $stats);
		                    });

		if ($other_games->count()) {
			$library[] = [
				'name'        => 'Games',
				'description' => 'All of your games that don\'t have a category',
				'games'       => $other_games,
			];
		}

		return response()->json($library);
	}

	private function formatGame(User $user, Game $game, $stats)
	{
		$game_data = $game->format(true);

		$game_data['friends'] = $user->friends()->whereHas('games', function ($q) use ($game) {
			$q->where('id', $game->id);
		})->get()->transform(function ($friend) {
			return $friend->format();
		});

		$game_stats = $stats->where('game_id', $game->id)->first();

		if (isset($game_stats)) {
			$playtime_two_weeks = null;
			$playtime_forever   = null;

			if ($game_stats->playtime_two_weeks > 0) {
				if ($game_stats->playtime_two_weeks >= 60)
					$playtime_two_weeks = floor($game_stats->playtime_two_weeks / 60) . ' hours';
				else
					$playtime_two_weeks = $game_stats->playtime_two_weeks . ' minutes';
			}

			if ($game_stats->playtime_forever > 0) {
				if ($game_stats->playtime_forever >= 60)
					$playtime_forever = floor($game_stats->playtime_forever / 60) . ' hours';
				else
					$playtime_forever = $game_stats->playtime_forever . ' minutes';
			}

			$game_data['stats'] = [
				'playtimeTwoWeeks' => $playtime_two_weeks,
				'playtimeForever'  => $playtime_forever,
			];
		}

		return $game_data;
	}
}
