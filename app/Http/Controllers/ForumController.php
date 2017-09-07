<?php

namespace App\Http\Controllers;

use App\Models\Forums\Suggestion;
use App\Models\Forums\SuggestionVote;

class ForumController extends Controller
{
	public function index()
	{
		$suggestions = Suggestion::orderBy('created_at', 'desc')
		                         ->paginate(25);

		$suggestions->getCollection()->transform(function ($suggestion) {
			return $suggestion->format();
		});

		for ($i = 0; $i < 5; $i++)
			$suggestions->getCollection()[] = ['filler' => true];

		return view('forums', [
			'user'         => \Auth::user()->format(),
			'suggestions'  => $suggestions,
			'previousPage' => $suggestions->previousPageUrl(),
			'nextPage'     => $suggestions->nextPageUrl(),
		]);
	}

	public function suggest()
	{
		request()->validate([
			'title'       => 'required|min:5|max:255',
			'description' => 'required|min:50|max:65535', // Whoa...
		]);

		Suggestion::create([
			'title'       => request('title'),
			'description' => request('description'),
		]);

		return response()->redirectToRoute('forums.home');
	}

	public function vote(Suggestion $suggestion, $type)
	{
		if ($type !== 'up' && $type !== 'down')
			return response()->redirectTo('forums.home');

		$current_vote = SuggestionVote::where([
			'suggestion_id' => $suggestion->id,
			'user_id'       => \Auth::id(),
		])->first();

		if (isset($current_vote)) {
			$current_vote->type = $type;
			$current_vote->save();
		} else {
			SuggestionVote::create([
				'user_id'       => \Auth::id(),
				'suggestion_id' => $suggestion->id,
				'type'          => $type,
			]);
		}

		return response()->redirectToRoute('forums.home');
	}
}
