<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Game;
use App\User;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
	public function achievements(Game $game)
	{
		$user = \Auth::user();

		$achievements = $user->achievements($game->id);

		if (!isset($achievements)) return response()->json([]);

		$achievements->transform(function ($achievement) {
			return $achievement->format();
		})->sortBy('unlockedAt');

		$recent   = $achievements->first(function ($achievement) {
			return $achievement['achieved'];
		});
		$locked   = $achievements->filter(function ($achievement) {
			return !$achievement['achieved'];
		});
		$unlocked = $achievements->filter(function ($achievement) {
			return $achievement['achieved'];
		});

		$data = [
			'recent'   => $recent,
			'stats'    => [
				'unlocked' => $unlocked->count(),
				'locked'   => $locked->count(),
				'total'    => $achievements->count(),
				'percent'  => ($achievements->count() <= 0 ? 0 :
					floor(($unlocked->count() / $achievements->count()) * 100)),
			],
			'locked'   => $locked,
			'unlocked' => $unlocked,
		];

		return response()->json($data);
	}

	public function news(Game $game)
	{
		$news = $game->news()
		             ->orderBy('posted', 'desc')
		             ->get()
		             ->transform(function ($item) {
			             return $item->format();
		             });

		return response()->json($news);
	}
}
